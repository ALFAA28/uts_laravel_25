<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class ProductCategoryController extends Controller
{
    /**
     * Mengambil daftar semua kategori produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            // Mengambil semua kategori produk
            $productCategories = ProductCategory::all();

            // Jika ada data, kembalikan data dengan status 200 OK (walaupun kosong)
            return response()->json($productCategories);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil data kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan kategori produk baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'deskripsi' => 'required',
            ]);

            $productCategory = ProductCategory::create($validatedData);

            return response()->json($productCategory, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menyimpan kategori produk baru', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan kategori produk tertentu berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $productCategory = ProductCategory::with('product.variant')->findOrFail($id);
            return response()->json($productCategory);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil detail kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil semua produk dari kategori tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsByCategoryId($id)
    {
        try {
            $productCategory = ProductCategory::findOrFail($id);

            if ($productCategory->products->isEmpty()) {
                return response()->json(['message' => 'Tidak ada produk yang ditemukan untuk kategori ini'], 404);
            }

            return response()->json([
                'category' => $productCategory->nama,
                'products' => $productCategory->products
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal mengambil produk dari kategori', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui kategori produk tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $productCategory = ProductCategory::findOrFail($id);

            $validatedData = $request->validate([
                'nama' => 'required|max:255',
                'deskripsi' => 'required',
            ]);

            $productCategory->update($validatedData);

            return response()->json($productCategory);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal memperbarui kategori produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus kategori produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $productCategory = ProductCategory::findOrFail($id);

            $productCategory->delete();

            return response()->json(['message' => 'Kategori produk berhasil dihapus']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Data kategori produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            return response()->json(['message' => 'Gagal menghapus kategori produk', 'error' => $e->getMessage()], 500);
        }
    }
}