<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menciptakan tabel 'products' dan relasi ke 'product_categories'.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_category_id'); // Kunci asing ke Kategori
            $table->string('nama');
            $table->decimal('harga', 10, 2); 
            $table->text('deskripsi')->nullable();

            // Relasi: Jika Kategori dihapus, Produk ikut dihapus
            $table->foreign('product_category_id')
                  ->references('id')
                  ->on('product_categories')
                  ->onDelete('cascade'); 
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};