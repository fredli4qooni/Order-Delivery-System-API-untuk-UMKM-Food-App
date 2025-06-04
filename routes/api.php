<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;

Route::prefix('v1')->group(function () {
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register'])->name('api.v1.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.v1.login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.logout');
        Route::get('/user', [AuthController::class, 'me'])->name('api.v1.user.me');

        Route::prefix('admin')->name('api.v1.admin.')->middleware('role:admin')->group(function () {
            Route::apiResource('/categories', AdminCategoryController::class);
            Route::apiResource('/products', AdminProductController::class);

            // Route khusus untuk upload gambar produk
            Route::post('/products/{product}/upload-image', [AdminProductController::class, 'uploadImage'])
                ->name('products.uploadImage');
        });

        // Customer Routes (akan ditambahkan nanti)
        // Route::prefix('customer')->name('api.v1.customer.')->middleware('role:customer')->group(function () {
        // });

        // Courier Routes (akan ditambahkan nanti)
        // Route::prefix('courier')->name('api.v1.courier.')->middleware('role:courier')->group(function () {
        // });
    });
});
