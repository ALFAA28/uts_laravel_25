<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menciptakan tabel 'product_variants' dan relasi ke 'products'.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Kunci asing ke Produk
            $table->string('nama'); 
            $table->integer('stok')->default(0); 
            $table->decimal('tambahan_harga', 10, 2)->nullable(); 

            // Relasi: Jika Produk dihapus, Varian ikut dihapus
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};