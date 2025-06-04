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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_uid')->unique()->comment('Public facing unique order ID'); // ID unik untuk customer
            
            // Foreign key untuk user (customer)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('cascade'); // Jika user dihapus, ordernya juga terhapus (pertimbangkan kebijakan bisnis Anda)

            // Foreign key untuk user (courier), bisa nullable
            $table->foreignId('courier_id')
                  ->nullable()
                  ->constrained('users') // merujuk ke tabel 'users' juga
                  ->onUpdate('cascade')
                  ->onDelete('set null'); // Jika kurir dihapus, order tetap ada, courier_id jadi null

            $table->decimal('total_amount', 12, 2);

            $table->string('status')->default('pending_payment')
                  ->comment('e.g., pending_payment, processing, out_for_delivery, delivered, cancelled, failed');
            
            $table->text('delivery_address');
            $table->decimal('delivery_latitude', 10, 7)->nullable(); // Presisi untuk GPS
            $table->decimal('delivery_longitude', 10, 7)->nullable(); // Presisi untuk GPS
            
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();

            $table->text('notes_customer')->nullable(); // Catatan dari customer
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending')
                  ->comment('e.g., pending, paid, failed');

            $table->timestamps();
            $table->softDeletes(); // "menghapus" order tanpa benar-benar menghilangkannya dari database
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};