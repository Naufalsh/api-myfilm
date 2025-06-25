<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {
    //     $userId = $request->header('Authorization');

    //     if ($userId) {
    //         $data = Kendaraan::where('email', $userId)->get();
    //     } else {
    //         $data = Kendaraan::all();
    //     }

    //     Log::info('Menampilkan daftar kendaraan', [
    //         'email' => $userId,
    //         // 'data_count' => $data
    //     ]);

    //     return response()->json($data);
    // }

   public function index(Request $request)
{
    $userId = $request->header('Authorization');

    if ($userId && $userId !== "all") {
        // Jika userId dikirim dan bukan string 'all', tampilkan data milik user
        $data = Kendaraan::where('email', $userId)->get();
    } else {
        // Jika userId kosong atau string 'all', tampilkan semua data
        $data = Kendaraan::all();
    }

    return response()->json($data);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'namaMobil' => 'required|string|max:255',
            'hargaMobil' => 'required|string|max:255',
            'tahun' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048' // max 2MB
        ]);

        Log::info('Menyimpan kendaraan baru', [
            'namaMobil' => $request->namaMobil,
            'hargaMobil' => $request->hargaMobil,
            'tahun' => $request->tahun,
            'email' => $request->header('Authorization'),
            'ip' => $request->ip(),
        ]);

        $path = $request->file('gambar')->store('kendaraans', 'public');
        $email = $request->header('Authorization');

        Kendaraan::create([
            // 'user_id' => auth()->id(),
            'email' => $email,
            'namaMobil' => $request->namaMobil,
            'hargaMobil' => $request->hargaMobil,
            'tahun' => $request->tahun,
            'gambar' => $path,
        ]);

        Log::info('kendaraan berhasil disimpan ke database', [
            'namaMobil' => $request->namaMobil,
            'email' => $email,
            'gambar' => $path,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kendaraan $kendaraan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kendaraan $kendaraan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        Log::info('Mengupdate kendaraan', [
            'id' => $id,
            'email' => request()->header('Authorization'),
        ]);
        $kendaraan = Kendaraan::where('id', $id)->firstOrFail();
        // $kendaraan = kendaraan::where('id', $id)
        //             ->where('user_id', auth()->id())
        //             ->firstOrFail();

        $request->validate([
            'namaMobil' => 'sometimes|required|string|max:255',
            'hargaMobil' => 'sometimes|required|string|max:255',
            'tahun' => 'sometimes|required|string|max:255',
            'gambar' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($kendaraan->gambar) {
                Storage::delete($kendaraan->gambar);
            }

            $kendaraan->gambar = $request->file('gambar')->store('public/gambars');
        }

        // Ambil email dari header Authorization
        $email = $request->header('Authorization');

        $kendaraan->namaMobil = $request->namaMobil;
        $kendaraan->hargaMobil = $request->hargaMobil;
        $kendaraan->tahun = $request->tahun;
        $kendaraan->email = $email;
        $kendaraan->save();
        // Kendaraan::update([
        //     'email' => $email,
        //     'namaMobil' => $request->namaMobil,
        //     'hargaMobil' => $request->hargaMobil,
        //     'tahun' => $request->tahun,
        //     'gambar' => $kendaraan->gambar_path,
        // ]);

        return response()->json([
            'status' => 'success',
            'data' => $kendaraan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Log::info('Menghapus kendaraan', [
            'id' => $id,
            'email' => request()->header('Authorization'),
        ]);
        $kendaraan = Kendaraan::where('id', $id)
            // ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($kendaraan->gambar) {
            Storage::delete($kendaraan->gambar);
        }

        $kendaraan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'kendaraan berhasil dihapus']);
    }
}