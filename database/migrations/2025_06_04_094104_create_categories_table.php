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
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // shorthand untuk bigIncrements('id')->primary()
            $table->string('name')->unique();
            $table->string('slug')->unique(); // Untuk URL yang SEO-friendly
            $table->text('description')->nullable();
            $table->timestamps(); // shorthand untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};