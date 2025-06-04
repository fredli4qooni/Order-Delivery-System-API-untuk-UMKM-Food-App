<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource; 
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of available products.
     * Customer hanya bisa melihat produk yang is_available = true.
     */
    public function index(Request $request)
    {
        $query = Product::with('category') // Eager load kategori
                        ->where('is_available', true); // Filter hanya produk yang tersedia

        // Filter berdasarkan kategori jika ada parameter 'category_slug' atau 'category_id'
        if ($request->has('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        } elseif ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Implementasi pencarian sederhana berdasarkan nama atau deskripsi
        if ($request->has('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->latest()->paginate($request->input('per_page', 12));
        return ProductResource::collection($products);
    }

    /**
     * Display the specified available product.
     */
    public function show(Product $product) // Menggunakan route model binding (slug/id)
    {
        // Pastikan produk yang diminta tersedia untuk customer
        if (!$product->is_available) {
            return response()->json(['message' => 'Product not found or not available.'], 404);
        }

        return new ProductResource($product->load('category'));
    }
}