<?php

namespace App\Http\Requests\Api\V1\Customer;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product; // Untuk validasi ketersediaan produk

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Hanya user terautentikasi yang bisa membuat order.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi lebih lanjut dihandle oleh middleware route
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'delivery_address' => 'required|string|max:1000',
            'notes_customer' => 'nullable|string|max:1000',
            'payment_method' => 'nullable|string|max:50', 

            'items' => 'required|array|min:1', // Harus ada minimal 1 item
            'items.*.product_id' => [ // Untuk setiap item dalam array 'items'
                'required',
                'integer',
                // Rule kustom untuk cek apakah produk ada dan available
                function ($attribute, $value, $fail) {
                    $product = Product::find($value);
                    if (!$product) {
                        return $fail("Product with ID {$value} not found.");
                    }
                    if (!$product->is_available) {
                        return $fail("Product '{$product->name}' is currently not available.");
                    }
                },
            ],
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Your cart is empty. Please add at least one product.',
            'items.array' => 'Invalid items format.',
            'items.min' => 'Your cart is empty. Please add at least one product.',
            'items.*.product_id.required' => 'Product ID is missing for an item.',
            'items.*.product_id.integer' => 'Invalid Product ID format.',
            'items.*.quantity.required' => 'Quantity is missing for an item.',
            'items.*.quantity.integer' => 'Invalid quantity format.',
            'items.*.quantity.min' => 'Quantity for each item must be at least 1.',
        ];
    }
}