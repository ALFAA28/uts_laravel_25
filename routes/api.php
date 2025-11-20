<?php

use App\Http\Controllers\Api\ProductCategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductVariantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {

    Route::apiResource('product-categories', ProductCategoryController::class);
    // Endpoint relasi: GET /api/product-categories/{id}/products
    Route::get('product-categories/{id}/products', [ProductCategoryController::class, 'getProductsByCategoryId']);
    
    // Rute Produk (CRUD dan Relasi)
    Route::apiResource('products', ProductController::class);
    // Endpoint relasi: GET /api/products/{id}/variants
    Route::get('products/{id}/variants', [ProductController::class, 'getVariantsByProductId']);
    
    // Rute Varian Produk (CRUD)
    Route::apiResource('product-variants', ProductVariantController::class);
    
    // Rute contoh standar
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
});
