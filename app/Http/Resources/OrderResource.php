<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_uid' => $this->order_uid,
            'customer' => new UserResource($this->whenLoaded('customer')), // UserResource sederhana
            'courier' => new UserResource($this->whenLoaded('courier')),
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'delivery_address' => $this->delivery_address,
            'delivery_latitude' => (float) $this->delivery_latitude,
            'delivery_longitude' => (float) $this->delivery_longitude,
            'estimated_delivery_time' => $this->estimated_delivery_time ? $this->estimated_delivery_time->toIso8601String() : null,
            'actual_delivery_time' => $this->actual_delivery_time ? $this->actual_delivery_time->toIso8601String() : null,
            'notes_customer' => $this->notes_customer,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'items' => OrderItemResource::collection($this->whenLoaded('items')), // Kita perlu OrderItemResource
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}