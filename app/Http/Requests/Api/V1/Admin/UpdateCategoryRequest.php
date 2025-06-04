<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // Untuk unique rule

class UpdateCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Otorisasi sudah dihandle oleh middleware 'role:admin' pada route
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Dapatkan ID kategori dari route parameter
        // Route model binding akan otomatis menyediakan objek Category
        $category = $this->route('category'); // 'category' nama parameter di route

        return [
            'name' => [
                'sometimes', // 'sometimes' hanya validasi jika field ada di request
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($category ? $category->id : null),
            ],
            'description' => 'nullable|string',
            'slug' => [
                'nullable', 
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category ? $category->id : null),
            ],
        ];
    }
}