<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Hanya tampilkan data yang relevan dan aman
        return [
            'id' => $this->id,
            'name' => $this->name,
            // 'email' => $this->email, // Mungkin tidak perlu selalu ditampilkan di detail order
            'role' => $this->role, // Berguna untuk membedakan
        ];
    }
}