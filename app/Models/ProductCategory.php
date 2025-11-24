<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    /**
     * 
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, 
     */
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

    /**
     * Mendefinisikan relasi One-to-Many ke Product.
     * Satu ProductCategory memiliki banyak Products.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}