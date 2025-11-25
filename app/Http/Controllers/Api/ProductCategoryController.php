<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception; // Import kelas Exception umum

class ProductCategoryController extends Controller
{
    /**
     * * Mengambil daftar semua kategori produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Mengambil semua kategori produk
            $productCategories = ProductCategory::all();

            // Cek jika koleksi kosong. Jika tidak ada data, kembalikan 404.
            if ($productCategories->isEmpty()) {
                return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
            }

            // Jika ada data, kembalikan data dengan status 200 OK
            return response()->json($productCategories);
        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database atau server
            return response()->json(['message' => 'Gagal mengambil data kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * * Menyimpan kategori produk baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi data input
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'deskripsi' => 'required',
            ]);
            
            // Membuat kategori produk baru
            $productCategory = ProductCategory::create($validatedData);

            // Mengembalikan kategori yang baru dibuat dengan status 201 Created
            return response()->json($productCategory, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani ValidationException
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Menangkap Exception umum untuk kegagalan penyimpanan database
            return response()->json(['message' => 'Gagal menyimpan kategori produk baru', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * * Menampilkan kategori produk tertentu berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Menggunakan findOrFail yang akan melempar ModelNotFoundException jika ID tidak ditemukan
            $productCategory = ProductCategory::findOrFail($id);

            // Mengembalikan data kategori produk
            return response()->json($productCategory);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal mengambil detail kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil semua produk dari kategori tertentu (Metode untuk Keterhubungan).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByCategoryId($id)
    {
        try {
            // Memuat kategori dan produk terkait. Asumsi: Model ProductCategory punya relasi 'products'.
            $productCategory = ProductCategory::with('products')->findOrFail($id);
            
            // Periksa apakah ada produk di kategori ini
            if ($productCategory->products->isEmpty()) {
                return response()->json(['message' => 'Tidak ada produk yang ditemukan untuk kategori ini'], 404);
            }

            // Mengembalikan nama kategori dan daftar produknya
            return response()->json([
                'category' => $productCategory->nama,
                'products' => $productCategory->products
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal mengambil produk dari kategori', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * * Memperbarui kategori produk tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan kategori produk ada
            $productCategory = ProductCategory::findOrFail($id);

            // Validasi data input
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'deskripsi' => 'required',
            ]);

            // Memperbarui kategori produk
            $productCategory->update($validatedData);

            // Mengembalikan data kategori yang sudah diperbarui
            return response()->json($productCategory);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani ValidationException
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal memperbarui kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * * Menghapus kategori produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan kategori produk ada
            $productCategory = ProductCategory::findOrFail($id);

            $productCategory->delete();

            // Mengembalikan pesan sukses penghapusan
            return response()->json(['message' => 'Kategori produk berhasil dihapus']);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal menghapus kategori produk', 'error' => $e->getMessage()], 500);
        }
    }
}