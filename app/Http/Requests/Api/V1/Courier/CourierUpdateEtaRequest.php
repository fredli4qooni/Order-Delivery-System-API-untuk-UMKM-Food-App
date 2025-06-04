<?php

namespace App\Http\Requests\Api\V1\Courier;

use Illuminate\Foundation\Http\FormRequest;

class CourierUpdateEtaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        if (!$order) {
            return false;
        }
        return $this->user()->id === $order->courier_id;
    }

    public function rules(): array
    {
        return [
            // ETA harus setelah waktu sekarang
            'estimated_delivery_time' => 'required|date_format:Y-m-d H:i:s|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'authorize' => 'You are not authorized to update this order ETA or the order is not assigned to you.'
        ];
    }
    
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException($this->messages()['authorize']);
    }
}