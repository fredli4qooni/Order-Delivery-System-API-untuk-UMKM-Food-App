<?php

namespace App\Http\Controllers\Api\V1\Courier;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Requests\Api\V1\Courier\CourierUpdateOrderStatusRequest;
use App\Http\Requests\Api\V1\Courier\CourierUpdateEtaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
// use App\Jobs\SendOrderStatusNotificationJob;

class OrderController extends Controller
{
    /**
     * Display a listing of orders assigned to the authenticated courier.
     */
    public function index(Request $request)
    {
        $courier = Auth::user();
        $query = Order::where('courier_id', $courier->id)
                        ->with(['customer:id,name,email', 'items', 'items.product:id,name,slug']); // Eager load

        // Filter berdasarkan status yang relevan untuk kurir
        // Misal: hanya tampilkan yang 'processing', 'out_for_delivery'
        $relevantStatuses = ['processing', 'out_for_delivery'];
        if ($request->has('status') && in_array($request->input('status'), $relevantStatuses)) {
            $query->where('status', $request->input('status'));
        } else {
            // Default hanya tampilkan order yang aktif untuk dikerjakan
            $query->whereIn('status', $relevantStatuses);
        }
        
        // Tambahkan filter lain jika perlu (misal, berdasarkan tanggal, dll)
        if ($request->boolean('show_completed_today')) {
            $query->orWhere(function($q) use ($courier) {
                $q->where('courier_id', $courier->id)
                  ->where('status', 'delivered')
                  ->whereDate('actual_delivery_time', today());
            });
        }


        $orders = $query->latest()->paginate($request->input('per_page', 10));

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified assigned order.
     */
    public function show(Order $order) // Model Order di-inject (berdasarkan id)
    {
        $courier = Auth::user();
        // Pastikan order ini ditugaskan ke kurir yang sedang login
        if ($order->courier_id !== $courier->id) {
            return response()->json(['message' => 'Forbidden. This order is not assigned to you.'], Response::HTTP_FORBIDDEN);
        }

        return new OrderResource($order->load(['customer:id,name,email', 'items', 'items.product:id,name,slug,image_url']));
    }

    /**
     * Update the status of an assigned order by the courier.
     */
    public function updateStatus(CourierUpdateOrderStatusRequest $request, Order $order)
    {
        $validatedData = $request->validated(); // Validasi & otorisasi sudah dihandle oleh FormRequest
        $originalStatus = $order->status;

        // Logika tambahan sebelum update status
        if ($order->status === 'delivered') {
             return response()->json(['message' => 'Order has already been delivered.'], Response::HTTP_BAD_REQUEST);
        }
        if ($order->status === 'cancelled' || $order->status === 'failed' && $originalStatus !== 'failed') {
             return response()->json(['message' => 'Cannot update status of a cancelled or failed order.'], Response::HTTP_BAD_REQUEST);
        }

        // Validasi transisi status yang lebih ketat jika diperlukan
        // Contoh: dari 'processing' hanya boleh ke 'out_for_delivery' atau 'failed'
        if ($originalStatus === 'processing' && !in_array($validatedData['status'], ['out_for_delivery', 'failed'])) {
            return response()->json(['message' => "Cannot change status from 'processing' to '{$validatedData['status']}'."], Response::HTTP_BAD_REQUEST);
        }
        if ($originalStatus === 'out_for_delivery' && !in_array($validatedData['status'], ['delivered', 'failed'])) {
            return response()->json(['message' => "Cannot change status from 'out_for_delivery' to '{$validatedData['status']}'."], Response::HTTP_BAD_REQUEST);
        }


        $order->status = $validatedData['status'];

        if ($order->status === 'delivered' && !$order->actual_delivery_time) {
            $order->actual_delivery_time = now();
        }
        // Jika kurir menandai 'failed', mungkin perlu alasan (bisa ditambahkan field 'failure_reason')
        // if ($order->status === 'failed' && $request->has('failure_reason')) {
        //    $order->failure_reason = $request->input('failure_reason');
        // }

        $order->save();

        // Kirim notifikasi ke customer dan admin
        // SendOrderStatusNotificationJob::dispatch($order, $order->customer, "Order status updated to {$order->status} by courier.");
        // SendOrderStatusNotificationJob::dispatch($order, User::where('role', 'admin')->first(), "Order {$order->order_uid} status updated to {$order->status} by courier.");


        return new OrderResource($order->fresh()->load(['customer', 'courier', 'items']));
    }

    /**
     * Update the Estimated Time of Arrival (ETA) for an order by the courier.
     */
    public function updateEta(CourierUpdateEtaRequest $request, Order $order)
    {
        $validatedData = $request->validated(); // Validasi & otorisasi sudah dihandle

        // Kurir hanya bisa update ETA jika order belum selesai atau dibatalkan
        if (in_array($order->status, ['delivered', 'cancelled', 'failed'])) {
             return response()->json(['message' => "Cannot update ETA for an order with status '{$order->status}'."], Response::HTTP_BAD_REQUEST);
        }

        $order->estimated_delivery_time = $validatedData['estimated_delivery_time'];
        $order->save();

        // Kirim notifikasi ke customer tentang ETA baru
        // SendOrderEtaUpdateNotificationJob::dispatch($order, $order->customer);

        return new OrderResource($order->fresh()->load(['customer', 'courier', 'items']));
    }
}