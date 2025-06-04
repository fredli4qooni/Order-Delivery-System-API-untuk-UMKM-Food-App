<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Requests\Api\V1\Admin\StoreCategoryRequest;   // Import Store Request
use App\Http\Requests\Api\V1\Admin\UpdateCategoryRequest;   // Import Update Request
use App\Http\Resources\CategoryResource;                  
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Untuk response codes

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Tambahkan paginasi dan filter jika perlu
        $categories = Category::latest()->paginate($request->input('per_page', 10));
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validatedData = $request->validated();
        $category = Category::create($validatedData);

        return response()->json(new CategoryResource($category), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * Route model binding otomatis inject Category berdasarkan 'slug' atau 'id' (sesuai getRouteKeyName di model)
     */
    public function show(Category $category) // Model Category di-inject otomatis
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $validatedData = $request->validated();
        $category->update($validatedData);

        return response()->json(new CategoryResource($category->fresh()), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Cek apakah ada produk yang terkait sebelum menghapus (jika relasi onDelete='restrict')
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Cannot delete category. It has associated products.'
            ], Response::HTTP_CONFLICT); // 409 Conflict
        }

        $category->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}