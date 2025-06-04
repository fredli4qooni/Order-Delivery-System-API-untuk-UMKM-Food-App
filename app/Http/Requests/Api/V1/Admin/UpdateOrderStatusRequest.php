<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi oleh middleware 'role:admin'
    }

    public function rules(): array
    {
        $validOrderStatuses = ['pending_payment', 'processing', 'payment_failed', 'out_for_delivery', 'delivered', 'cancelled', 'failed'];

        return [
            'status' => ['required', 'string', Rule::in($validOrderStatuses)],
        ];
    }
}