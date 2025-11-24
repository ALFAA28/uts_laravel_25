<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * 
     *
     * @var array<int, 
     */
    protected $fillable = [
        'product_id',
        'nama',
        'stok',
        'tambahan_harga',
    ];

    /**
     * Mendefinisikan relasi BelongsTo ke Product.
     * Satu ProductVariant dimiliki oleh satu Product.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}