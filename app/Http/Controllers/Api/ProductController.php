<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception; // Import kelas Exception umum

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $products = Product::all();

            // Cek jika koleksi kosong. Jika tidak ada data, kembalikan 404.
            if ($products->isEmpty()) {
                // Mengembalikan 404 jika tidak ada data
                return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
            }

            // Mengembalikan 200 OK
            return response()->json($products);
            
        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database atau server lainnya
            return response()->json(['message' => 'Gagal mengambil data produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan produk baru ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Catatan: ValidationException yang dilempar oleh validate() secara default akan ditangani secara global oleh Laravel
        // dan mengembalikan respons JSON 422 (Unprocessable Entity).

        try {
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                // 'exists' memastikan ID kategori ada di tabel product_categories
                'product_category_id' => 'required|exists:product_categories,id',
                'harga' => 'required|numeric|min:0',
                'deskripsi' => 'nullable',
            ]);
            
            $product = Product::create($validatedData);

            // Mengembalikan 201 Created
            return response()->json($product, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ini akan jarang tertangkap jika ValidationException ditangani secara global,
            // tetapi baik untuk pengetahuan.
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);

        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database saat menyimpan
            return response()->json(['message' => 'Gagal menyimpan produk baru', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan produk tertentu. (Sudah memiliki penanganan error yang baik)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // PENTING: Menggunakan with() untuk memuat relasi 'category' dan 'variants' (Eager Loading)
            $product = Product::with(['category', 'variant'])->find($id);

            if (!$product) {
                return response()->json(['message' => 'Produk tidak ditemukan'], 404);
            }

            return response()->json($product, 200);
            
        } catch (Exception $e) {
            // Penanganan jika ada kesalahan lain saat memuat relasi
            return response()->json(['message' => 'Gagal mengambil detail produk', 'error' => $e->getMessage()], 500);
        }
    }        
    
    /**
     * Mengambil semua varian dari produk tertentu (Metode untuk Keterhubungan). (Sudah memiliki penanganan error yang baik)
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
                // 200 OK dengan pesan informatif jika tidak ada varian, atau 404 jika ini dianggap sumber daya yang hilang
                // Saya mempertahankan 404 agar sesuai dengan logika awal, meskipun 200 juga bisa diterima.
                return response()->json(['message' => 'Tidak ada varian yang ditemukan untuk produk ini'], 404);
            }

            // Mengembalikan nama produk dan daftar variannya
            return response()->json([
                'product' => $product->nama,
                'variants' => $product->variants
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        } catch (Exception $e) {
             return response()->json(['message' => 'Gagal mengambil varian produk', 'error' => $e->getMessage()], 500);
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
            // 1. Menggunakan findOrFail untuk memastikan produk ada, jika tidak ada akan melempar ModelNotFoundException
            $product = Product::findOrFail($id);

            // 2. Validasi data input
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'product_category_id' => 'required|exists:product_categories,id',
                'harga' => 'required|numeric|min:0',
                'deskripsi' => 'nullable',
            ]);

            // 3. Memperbarui produk
            $product->update($validatedData);

            // Mengembalikan 200 OK dengan data produk yang diperbarui
            return response()->json($product);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ini akan jarang tertangkap jika ValidationException ditangani secara global.
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);

        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database saat update
            return response()->json(['message' => 'Gagal memperbarui produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus produk tertentu. (Sudah memiliki penanganan error yang baik)
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

            // Mengembalikan 200 OK (atau 204 No Content, tetapi 200 dengan pesan lebih informatif)
            return response()->json(['message' => 'Produk berhasil dihapus']);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database saat delete
            return response()->json(['message' => 'Gagal menghapus produk', 'error' => $e->getMessage()], 500);
        }
    }

    
}