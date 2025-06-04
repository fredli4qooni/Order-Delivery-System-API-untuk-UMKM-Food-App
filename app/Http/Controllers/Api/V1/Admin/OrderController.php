<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User; // Untuk mengecek role courier
use App\Http\Resources\OrderResource;
use App\Http\Requests\Api\V1\Admin\UpdateOrderStatusRequest;
use App\Http\Requests\Api\V1\Admin\AssignCourierRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of all orders.
     * Admin bisa melihat semua order.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'courier', 'items']); // Eager load relasi

        // Filtering (contoh)
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('customer_id') && $request->customer_id != '') {
            $query->where('user_id', $request->customer_id);
        }
        if ($request->has('courier_id') && $request->courier_id != '') {
            $query->where('courier_id', $request->courier_id);
        }
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }


        $orders = $query->latest()->paginate($request->input('per_page', 10));
        return OrderResource::collection($orders);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order) // Route model binding
    {
        return new OrderResource($order->load(['customer', 'courier', 'items.product'])); // Load product detail di items
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $validated = $request->validated();
        $oldStatus = $order->status;
        $order->status = $validated['status'];

        if ($validated['status'] === 'delivered' && !$order->actual_delivery_time) {
            $order->actual_delivery_time = now();
        }

        $order->save();

        return new OrderResource($order->fresh()->load(['customer', 'courier', 'items']));
    }

    /**
     * Assign a courier to the specified order.
     */
    public function assignCourier(AssignCourierRequest $request, Order $order)
    {
        $validated = $request->validated();
        $courierId = $validated['courier_id'];

        // Cek apakah order sudah punya kurir atau sudah dalam status akhir
        if ($order->courier_id && $order->courier_id != $courierId) {
        }
        if (in_array($order->status, ['delivered', 'cancelled', 'failed'])) {
             return response()->json([
                'message' => 'Cannot assign courier to an order that is already ' . $order->status . '.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $order->courier_id = $courierId;
       
        $order->save();

        return new OrderResource($order->fresh()->load(['customer', 'courier', 'items']));
    }
}