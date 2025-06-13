<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = $request->header('Authorization');

        $data = Review::all()->map(function ($item) use ($userId) {
            $item->mine = ($userId && $item->email === $userId) ? 1 : 0;
            return $item;
        });

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
            'judul_film' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:5',
            'komentar' => 'required|string',
            'poster' => 'required|image|mimes:jpg,jpeg,png|max:2048' // max 2MB
        ]);

        Log::info('Menyimpan review baru', [
            'judul_film' => $request->judul_film,
            'rating' => $request->rating,
            'email' => $request->header('Authorization'),
            'ip' => $request->ip(),
        ]);

        $path = $request->file('poster')->store('reviews', 'public');
        $email = $request->header('Authorization');

        Review::create([
            // 'user_id' => auth()->id(),
            'email' => $email,
            'judul_film' => $request->judul_film,
            'rating' => $request->rating,
            'komentar' => $request->komentar,
            'poster_path' => $path,
        ]);

        Log::info('Review berhasil disimpan ke database', [
            'judul_film' => $request->judul_film,
            'email' => $email,
            'poster_path' => $path,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        Log::info('Mengupdate review', [
            'id' => $id,
            'email' => $request->header('Authorization'),
        ]);
        // $review = Review::where('id', $id)
        //             ->where('user_id', auth()->id())
        //             ->firstOrFail();

        $request->validate([
            'judul_film' => 'sometimes|required|string|max:255',
            'rating' => 'sometimes|required|numeric|min:0|max:5',
            'komentar' => 'sometimes|required|string',
            'poster' => 'nullable|image|max:2048'
        ]);

        // if ($request->hasFile('poster')) {
        //     if ($review->poster_path) {
        //         Storage::delete($review->poster_path);
        //     }

        //     $review->poster_path = $request->file('poster')->store('public/posters');
        // }

        $email = $request->header('Authorization');

        // Find the review by ID and update its fields
        $review = Review::findOrFail($id);
        $review->email = $email;
        if ($request->has('judul_film')) {
            $review->judul_film = $request->judul_film;
        }
        if ($request->has('rating')) {
            $review->rating = $request->rating;
        }
        if ($request->has('komentar')) {
            $review->komentar = $request->komentar;
        }
        if ($request->hasFile('poster')) {
            $review->poster_path = $request->file('poster')->store('public/posters');
        }
        $review->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil diupdate',
            // 'data' => $review
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Log::info('Menghapus review', [
            'id' => $id,
            'email' => request()->header('Authorization'),
        ]);
        $review = Review::where('id', $id)
            // ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($review->poster_path) {
            Storage::delete($review->poster_path);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Review berhasil dihapus'
        ]);
    }
}
