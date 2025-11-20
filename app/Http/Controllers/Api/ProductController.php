<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $products = Product::all();

        // Cek jika koleksi kosong. Jika tidak ada data, kembalikan 404.
        if ($products->isEmpty()) {
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        }

        return response()->json($products);
    }

    /**
     * Menyimpan produk baru ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Keterhubungan dihidupkan kembali: Memastikan product_category_id ada
        $validatedData = $request->validate([
            'nama' => 'required|max:255',
            'product_category_id' => 'required|exists:product_categories,id', // Diaktifkan kembali
            'harga' => 'required|numeric|min:0',
            'deskripsi' => 'nullable',
        ]);
        
        $product = Product::create($validatedData);

        return response()->json($product, 201);
    }

    /**
     * Menampilkan produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     *
    public function show($id)
    {
        try {
            // Menggunakan findOrFail yang akan melempar ModelNotFoundException jika ID tidak ditemukan
            $product = Product::findOrFail($id);

            return response()->json($product);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        }
    }
    */

public function show($id)
{
    {
        // PENTING: Menggunakan with() untuk memuat relasi 'category' dan 'variants'
        $product = Product::with(['category', 'variants'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($product, 200);
    }
}        
    /**
     * Mengambil semua varian dari produk tertentu (Metode untuk Keterhubungan).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVariantsByProductId($id)
    {
        try {
            // Memuat produk dan varian terkait. Asumsi: Model Product punya relasi 'variants'.
            $product = Product::with('variants')->findOrFail($id);
            
            // Periksa apakah ada varian di produk ini
            if ($product->variants->isEmpty()) {
                return response()->json(['message' => 'Tidak ada varian yang ditemukan untuk produk ini'], 404);
            }

            // Mengembalikan nama produk dan daftar variannya
            return response()->json([
                'product' => $product->nama,
                'variants' => $product->variants
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        }
    }

    /**
     * Memperbarui produk tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan produk ada
            $product = Product::findOrFail($id);

            // Validasi dihidupkan kembali: Memastikan product_category_id masih valid
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'product_category_id' => 'required|exists:product_categories,id', // Diaktifkan kembali
                'harga' => 'required|numeric|min:0',
                'deskripsi' => 'nullable',
            ]);

            $product->update($validatedData);

            return response()->json($product);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        }
    }

    /**
     * Menghapus produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan produk ada
            $product = Product::findOrFail($id);

            $product->delete();

            return response()->json(['message' => 'Produk berhasil dihapus']);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        }
    }

    
}