<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use App\Http\Requests\Api\V1\Customer\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Untuk database transaction
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the customer's orders.
     */
    public function index(Request $request)
    {
        $orders = Auth::user()->customerOrders() // Menggunakan relasi dari model User
                        ->with(['items', 'courier']) // Eager load items dan courier
                        ->latest()
                        ->paginate($request->input('per_page', 10));

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $validatedData = $request->validated();
        $user = Auth::user();
        $totalAmount = 0;
        $orderItemsData = [];

        // menggunakan DB Transaction untuk memastikan konsistensi data
        // Jika salah satu query gagal, semua akan di-rollback
        DB::beginTransaction();

        try {
            // Kumpulkan detail produk dan hitung total
            foreach ($validatedData['items'] as $item) {
                $product = Product::find($item['product_id']);

                // Double check ketersediaan (meskipun sudah divalidasi di FormRequest, baik untuk race condition kecil)
                if (!$product || !$product->is_available) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'One or more products are no longer available or not found.',
                        'errors' => ['items' => ["Product '{$product->name}' (ID: {$item['product_id']}) is not available."]]
                    ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
                }

                $subTotal = $product->price * $item['quantity'];
                $totalAmount += $subTotal;

                $orderItemsData[] = [
                    // 'order_id' akan diisi setelah order utama dibuat
                    'product_id' => $product->id,
                    'product_name' => $product->name, // Simpan nama produk saat ini
                    'quantity' => $item['quantity'],
                    'price_at_order' => $product->price, // Simpan harga produk saat ini
                    'sub_total' => $subTotal,
                ];
            }

            // Buat entri Order utama
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $totalAmount,
                'delivery_address' => $validatedData['delivery_address'],
                'notes_customer' => $validatedData['notes_customer'] ?? null,
                'payment_method' => $validatedData['payment_method'] ?? 'cod', // Default payment method
            ]);

            
            foreach ($orderItemsData as $idx => $itemData) {
                $orderItemsData[$idx]['order_id'] = $order->id;
            }
            OrderItem::insert($orderItemsData); // Bulk insert untuk efisiensi jika OrderItem tidak punya $timestamps=true

            DB::commit(); // Semua query berhasil, simpan perubahan

            // Load relasi untuk response
            $order->load(['items', 'customer']);

            return response()->json(new OrderResource($order), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack(); // Ada error, batalkan semua query
            // Log error $e->getMessage()
            return response()->json([
                'message' => 'Failed to create order. Please try again.',
                'error' => $e->getMessage() 
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified order if it belongs to the customer.
     */
    public function show(Order $order) // Route model binding
    {
        if (Auth::id() !== $order->user_id) {
            return response()->json(['message' => 'Forbidden. You do not own this order.'], Response::HTTP_FORBIDDEN);
        }

        return new OrderResource($order->load(['items', 'customer', 'courier']));
    }
}