# Order & Delivery System API (Proyek UMKM/Food App)

![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=for-the-badge&logo=laravel) ![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php) ![MySQL](https://img.shields.io/badge/MySQL-blue?style=for-the-badge&logo=mysql) ![License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)

Project Backend RESTful API untuk sistem pemesanan dan pengiriman yang dirancang untuk Usaha Mikro, Kecil, dan Menengah (UMKM) atau aplikasi makanan. Dibangun menggunakan Laravel 10 dengan PHP 8.1 dan MySQL sebagai database.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Stack Teknologi](#stack-teknologi)
- [Prasyarat](#prasyarat)
- [Instalasi & Setup](#instalasi--setup)
- [Menjalankan Proyek](#menjalankan-proyek)
- [Struktur API Endpoint](#struktur-api-endpoint)
- [Konfigurasi Penting](#konfigurasi-penting)
- [Lisensi](#lisensi)

---

## Fitur Utama

-   **Manajemen Produk & Kategori**: CRUD penuh untuk produk dan kategori oleh admin.
-   **Manajemen User & Role**: Sistem role (Admin, Customer, Courier).
-   **Autentikasi API**: Menggunakan Laravel Sanctum (Token-based).
-   **Proses Pemesanan**: Customer dapat membuat pesanan.
-   **Pelacakan Order**: Status order yang jelas (diproses, dikirim, selesai, dll.).
-   **Manajemen Order (Admin)**: Melihat semua order, update status, menugaskan kurir.
-   **Manajemen Order (Kurir)**: Melihat order yang ditugaskan, update status pengiriman, update estimasi waktu pengantaran (ETA).
-   **Upload Gambar Produk**: Admin dapat mengupload gambar untuk produk.
-   **API Panel Admin**: Endpoint khusus untuk fungsionalitas admin.
-   **Validasi Input**: Menggunakan Laravel Form Requests.
-   **Transformasi Data**: Menggunakan Laravel API Resources.
-   **Queue System Ready**: Konfigurasi awal untuk Redis (dapat diubah).

---

## Stack Teknologi

-   **Framework**: Laravel 10
-   **Bahasa Pemrograman**: PHP 8.1
-   **Database**: MySQL
-   **Web Server**: (Rekomendasi: Nginx atau Apache - development menggunakan `php artisan serve`)
-   **Autentikasi API**: Laravel Sanctum
-   **Queue Driver (Default)**: Redis 
-   **Dependency Manager**: Composer

## Struktur API Endpoint 

Semua endpoint API di-prefix dengan `/api/v1`.

-   **Autentikasi (`/api/v1`)**
    -   `POST /register`
    -   `POST /login`
    -   `POST /logout` (Membutuhkan Autentikasi)
    -   `GET /user` (Membutuhkan Autentikasi - Info user saat ini)

-   **Customer - Publik (`/api/v1/customer`)**
    -   `GET /categories`
    -   `GET /categories/{category:slug}`
    -   `GET /products` (dengan filter & search)
    -   `GET /products/{product:slug}`

-   **Customer - Terautentikasi (`/api/v1/customer`, Membutuhkan Auth & Role Customer)**
    -   `POST /orders`
    -   `GET /orders`
    -   `GET /orders/{order}`

-   **Admin (`/api/v1/admin`, Membutuhkan Auth & Role Admin)**
    -   CRUD Kategori: `GET, POST, GET /categories/{category:slug}, PUT/PATCH /categories/{category:slug}, DELETE /categories/{category:slug}`
    -   CRUD Produk: `GET, POST, GET /products/{product:slug}, PUT/PATCH /products/{product:slug}, DELETE /products/{product:slug}`
    -   Upload Gambar Produk: `POST /products/{product:slug}/upload-image`
    -   Manajemen Order:
        -   `GET /orders` (Semua order)
        -   `GET /orders/{order}` (Detail order)
        -   `PATCH /orders/{order}/details` (Update status, payment, ETA)
        -   `POST /orders/{order}/assign-courier`

-   **Kurir (`/api/v1/courier`, Membutuhkan Auth & Role Courier)**
    -   Manajemen Order:
        -   `GET /orders` (Order yang ditugaskan)
        -   `GET /orders/{order}` (Detail order)
        -   `PATCH /orders/{order}/status` (Update status oleh kurir)
        -   `PATCH /orders/{order}/eta` (Update ETA oleh kurir)

File koleksi Postman juga tersedia di dalam repository ini:
-   **Lokasi File**: `postman/Order_Delivery_API.postman_collection.json` 

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE.md).

---

