<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\Api\V1\Admin\StoreProductRequest;
use App\Http\Requests\Api\V1\Admin\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage; // Untuk mengelola file

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with('category') // Eager load relasi category
            ->latest()
            ->paginate($request->input('per_page', 10));
        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $validatedData = $request->validated();

        $product = Product::create($validatedData);
        return response()->json(new ProductResource($product->load('category')), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * Route model binding menggunakan 'slug' atau 'id' dari Product model.
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validatedData = $request->validated();


        $product->update($validatedData);
        return response()->json(new ProductResource($product->fresh()->load('category')), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {

        // Hapus gambar terkait jika ada
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Handle image upload for a product.
     * endpoint terpisah untuk upload gambar.
     */
    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Hapus gambar lama jika ada
        if ($product->image_url) {
            Storage::disk('public')->delete($product->image_url);
        }

        // Simpan gambar baru
        $path = $request->file('image')->store('products', 'public');

        // Update path gambar di database
        $product->image_url = $path;
        $product->save();

        return response()->json(new ProductResource($product->fresh()->load('category')), Response::HTTP_OK);
    }
}