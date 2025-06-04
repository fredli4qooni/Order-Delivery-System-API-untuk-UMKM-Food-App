<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Order & Delivery System API - UMKM/Food App",
 *      description="Dokumentasi API untuk sistem pemesanan dan pengantaran makanan UMKM.",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000/api/v1",
 *      description="API Server Utama"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login dengan email dan password untuk mendapatkan token.",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 *
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoint untuk autentikasi user (Register, Login, Logout)"
 * )
 * @OA\Tag(
 *     name="Admin - Categories",
 *     description="Manajemen Kategori oleh Admin"
 * )
 * @OA\Tag(
 *     name="Admin - Products",
 *     description="Manajemen Produk oleh Admin"
 * )
 * @OA\Tag(
 *     name="Admin - Orders",
 *     description="Manajemen Order oleh Admin"
 * )
 * @OA\Tag(
 *     name="Customer - Catalog",
 *     description="Melihat Kategori dan Produk oleh Customer/Publik"
 * )
 * @OA\Tag(
 *     name="Customer - Orders",
 *     description="Manajemen Order oleh Customer"
 * )
 * @OA\Tag(
 *     name="Courier - Orders",
 *     description="Manajemen Order oleh Kurir"
 * )
  * @OA\Schema(
 *      schema="User",
 *      title="User Model",
 *      description="Model data untuk User",
 *      @OA\Property(property="id", type="integer", format="int64", description="ID User", example=1),
 *      @OA\Property(property="name", type="string", description="Nama User", example="John Doe"),
 *      @OA\Property(property="email", type="string", format="email", description="Email User", example="john.doe@example.com"),
 *      @OA\Property(property="role", type="string", description="Role User (admin, customer, courier)", example="customer"),
 *      @OA\Property(property="email_verified_at", type="string", format="date-time", description="Waktu verifikasi email", example="2023-01-01T12:00:00Z", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", description="Waktu dibuat", example="2023-01-01T12:00:00Z"),
 *      @OA\Property(property="updated_at", type="string", format="date-time", description="Waktu diupdate", example="2023-01-01T12:00:00Z")
 * )
 *
 * @OA\Schema(
 *      schema="UserSimple",
 *      title="User Model (Simple)",
 *      description="Representasi sederhana dari User Model",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="John Doe"),
 *      @OA\Property(property="email", type="string", example="user@example.com"),
 *      @OA\Property(property="role", type="string", example="customer")
 * )
 * 
 * // Tambahkan skema untuk Category, Product, Order, OrderItem, dll.
 * @OA\Schema(
 *      schema="Category",
 *      title="Category Model",
 *      @OA\Property(property="id", type="integer", example=1),
 *      @OA\Property(property="name", type="string", example="Makanan Ringan"),
 *      @OA\Property(property="slug", type="string", example="makanan-ringan"),
 *      @OA\Property(property="description", type="string", nullable=true, example="Kategori untuk makanan ringan."),
 *      @OA\Property(property="created_at", type="string", format="date-time"),
 *      @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * // @OA\Schema untuk Product, Order, OrderItem, dll.
 * // ... Anda perlu melanjutkan ini untuk semua model/resource Anda
 *
 */

class ApiController
{
}