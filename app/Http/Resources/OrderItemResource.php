<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
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
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            'price_at_order' => (float) $this->price_at_order,
            'sub_total' => (float) $this->sub_total,
            // Bisa tambahkan ProductResource jika ingin detail produk lengkap,
            // tapi untuk order item, product_name dan price_at_order sudah cukup untuk histori
            // 'product' => new ProductResource($this->whenLoaded('product')),
        ];
    }
}