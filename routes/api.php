<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Customer\CategoryController as CustomerCategoryController;
use App\Http\Controllers\Api\V1\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Api\V1\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;

Route::prefix('v1')->group(function () {
    // Publicly accessible product and category listings
    Route::get('/categories', [CustomerCategoryController::class, 'index'])->name('api.v1.categories.index');
    Route::get('/categories/{category:slug}', [CustomerCategoryController::class, 'show'])->name('api.v1.categories.show');
    Route::get('/products', [CustomerProductController::class, 'index'])->name('api.v1.products.index');
    Route::get('/products/{product:slug}', [CustomerProductController::class, 'show'])->name('api.v1.products.show');

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

            // Admin Order Management Routes
            Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
            Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
            Route::patch('/orders/{order}/assign-courier', [AdminOrderController::class, 'assignCourier'])->name('orders.assignCourier');
        });

        Route::prefix('customer')->name('api.v1.customer.')->middleware('role:customer')->group(function () {
            Route::post('/orders', [CustomerOrderController::class, 'store'])->name('orders.store');
            Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('orders.show');
        });
    });
});
