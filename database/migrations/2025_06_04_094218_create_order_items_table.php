<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Foreign key untuk order
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // Jika order dihapus, item-itemnya juga terhapus

            // Foreign key untuk product
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onUpdate('cascade')
                  ->onDelete('restrict'); // Jangan hapus produk jika masih ada di order item yang belum selesai/dibatalkan. 

            $table->string('product_name'); // Simpan nama produk saat order, untuk histori jika nama produk di tabel products berubah
            $table->unsignedInteger('quantity');
            $table->decimal('price_at_order', 10, 2); // Harga satuan produk saat order
            $table->decimal('sub_total', 12, 2); // quantity * price_at_order
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};