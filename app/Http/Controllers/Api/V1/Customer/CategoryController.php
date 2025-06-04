<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource; // Kita gunakan resource yang sama
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of available categories.
     * Customer hanya perlu melihat kategori yang mungkin memiliki produk aktif.
     */
    
    public function index(Request $request)
    {
        $categories = Category::latest()->paginate($request->input('per_page', 10));
        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category along with its available products.
     */
    public function show(Request $request, Category $category) // Menggunakan route model binding (slug/id)
    {
        // Load produk yang tersedia dalam kategori 
        $products = $category->products()
                             ->where('is_available', true) // Hanya produk yang tersedia
                             ->latest()
                             ->paginate($request->input('products_per_page', 9));

        
        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products)
        ]);
    }
}