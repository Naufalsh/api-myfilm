<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    // Ambil header Authorization
    $userId = $request->header('Authorization');

    // Cek apakah header ada atau tidak
   if ($userId) {
        $items = Gallery::where('Authorization', $userId)->latest()->get();
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Authorization header tidak ditemukan.',
        ], 401);
    }

    // Tulis log untuk debugging
    Log::info('Data galeri yang diambil:', ['items_count' => count($items)]);

    // Kembalikan data sebagai JSON
    return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil ditampilkan.',
            'data' => $items
        ], 200);
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userId = $request->header('Authorization');
        $validator = Validator::make($request->all(), [
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:255',
            'tanggal' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        Log::info('Validating request data', ['data' => $request->all()]);
        Log::info('userId', ['userId' => $userId]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'success',
                'message' => 'Validasi gagal.',
                // 'errors' => $validator->errors()
            ], 422); // Unprocessable Entity
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('items', 'public');
        }

        $item = Gallery::create([
            'Authorization' => $userId,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'gambar' => $gambarPath,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil ditambahkan.',
            'data' => 'success',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Gallery::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail item berhasil dimuat.',
            'data' => $item
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        Log::info('Update request received', ['id' => $id, 'data' => $request->all()]);
        $item = Gallery::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        // Validasi input
        $validator = Validator::make($request->all(), [
            'lokasi' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string|max:255',
            'tanggal' => 'sometimes|string|max:255',
            'gambar' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('lokasi')) {
            $item->lokasi = $request->lokasi;
        }
        if ($request->has('deskripsi')) {
            $item->deskripsi = $request->deskripsi;
        }
        if ($request->has('tanggal')) {
            $item->tanggal = $request->tanggal;
        }

        if ($request->has('ket')) {
            $item->keterangan = $request->ket;
        }

        if ($request->hasFile('gambar')) {
            if ($item->gambar) {
                Storage::disk('public')->delete($item->gambar);
            }

            $gambarPath = $request->file('gambar')->store('items', 'public');
            $item->gambar = $gambarPath;
        }

        $item->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil diperbarui.',
            // 'data' => $item
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Gallery::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Item tidak ditemukan.',
            ], 404);
        }

        // Hapus gambar dari storage jika ada
        if ($item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus.'
        ], 200);
    }

    public function allItems()
    {
        $items = Gallery::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Semua item berhasil dimuat.',
            'data' => $items
        ], 200);
    }
}