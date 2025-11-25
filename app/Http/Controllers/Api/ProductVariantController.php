<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception; // Import kelas Exception umum

class ProductVariantController extends Controller
{
    /**
     * Menampilkan daftar semua varian produk.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $productVariants = ProductVariant::all();

            // Cek jika koleksi kosong. Jika tidak ada data, kembalikan 404.
            if ($productVariants->isEmpty()) {
                return response()->json(['message' => 'Data varian produk tidak ditemukan'], 404);
            }

            return response()->json($productVariants);
        } catch (Exception $e) {
            // Menangkap Exception umum untuk masalah database atau server
            return response()->json(['message' => 'Gagal mengambil data varian produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan varian produk baru ke dalam penyimpanan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            // Keterhubungan: Memastikan product_id ada di tabel products
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'nama' => 'required|max:255', // Contoh: Warna Merah, Ukuran L
                'stok' => 'required|integer|min:0',
                'tambahan_harga' => 'nullable|numeric|min:0',
            ]);
            
            $productVariant = ProductVariant::create($validatedData);

            return response()->json($productVariant, 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani ValidationException
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Menangkap Exception umum untuk kegagalan penyimpanan database
            return response()->json(['message' => 'Gagal menyimpan varian produk baru', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan varian produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Menggunakan findOrFail yang akan melempar ModelNotFoundException jika ID tidak ditemukan
            $productVariant = ProductVariant::findOrFail($id);

            return response()->json($productVariant);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data varian produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal mengambil detail varian produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui varian produk tertentu.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan varian produk ada
            $productVariant = ProductVariant::findOrFail($id);

            // Validasi data input, termasuk memastikan product_id masih valid
            $validatedData = $request->validate([
                'product_id' => 'required|exists:products,id',
                'nama' => 'required|max:255', 
                'stok' => 'required|integer|min:0',
                'tambahan_harga' => 'nullable|numeric|min:0',
            ]);

            $productVariant->update($validatedData);

            return response()->json($productVariant);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data varian produk tidak ditemukan'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Menangani ValidationException
            return response()->json(['message' => 'Data yang dimasukkan tidak valid', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal memperbarui varian produk', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus varian produk tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Menggunakan findOrFail untuk memastikan varian produk ada
            $productVariant = ProductVariant::findOrFail($id);

            $productVariant->delete();

            return response()->json(['message' => 'Varian produk berhasil dihapus']);

        } catch (ModelNotFoundException $e) {
            // Menangkap ModelNotFoundException dan mengembalikan respons 404 kustom
            return response()->json(['message' => 'Data varian produk tidak ditemukan'], 404);
        } catch (Exception $e) {
            // Menangkap Exception umum
            return response()->json(['message' => 'Gagal menghapus varian produk', 'error' => $e->getMessage()], 500);
        }
    }
}