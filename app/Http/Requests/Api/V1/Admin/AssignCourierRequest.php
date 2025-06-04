<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignCourierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Otorisasi oleh middleware 'role:admin'
    }

    public function rules(): array
    {
        return [
            'courier_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where(function ($query) {
                    return $query->where('role', 'courier');
                }),
            ],
        ];
    }

    public function messages()
    {
        return [
            'courier_id.exists' => 'The selected courier is invalid or not a courier.',
        ];
    }
}