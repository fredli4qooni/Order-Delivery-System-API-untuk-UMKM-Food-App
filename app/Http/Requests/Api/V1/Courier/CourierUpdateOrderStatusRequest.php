<?php

namespace App\Http\Requests\Api\V1\Courier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Order; // Untuk memeriksa status order saat ini

class CourierUpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan order yang akan diupdate adalah milik kurir yang sedang login
        // atau kurir tersebut adalah kurir yang ditugaskan pada order ini.
        $order = $this->route('order'); // Dapatkan order dari route model binding
        if (!$order) {
            return false; // Jika order tidak ditemukan
        }
        return $this->user()->id === $order->courier_id;
    }

    public function rules(): array
    {
        $order = $this->route('order');
        // Kurir hanya boleh mengubah status ke status tertentu,
        // dan mungkin ada batasan berdasarkan status saat ini.
        $allowedCourierStatuses = ['out_for_delivery', 'delivered', 'failed']; // Status yang bisa diupdate kurir
        
        // Jika status saat ini processing atau out_for_delivery, kurir bisa update ke delivered atau failed.
        // Jika status saat ini processing, kurir bisa update ke out_for_delivery.
        $rules = [
            'status' => ['required', Rule::in($allowedCourierStatuses)],
        ];

        // Validasi transisi status (opsional tapi lebih baik)
        // Contoh: tidak bisa dari 'processing' langsung ke 'delivered' jika harus 'out_for_delivery' dulu
        // Ini bisa lebih kompleks tergantung alur bisnis.
        // if ($order) {
        //     if ($order->status === 'processing' && $this->input('status') === 'delivered') {
        //          // Tambahkan error kustom jika transisi tidak valid
        //     }
        // }


        return $rules;
    }

    public function messages(): array
    {
        return [
            'status.in' => 'The selected status is not valid for a courier to update.',
            'authorize' => 'You are not authorized to update this order status or the order is not assigned to you.'
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException($this->messages()['authorize']);
    }
}