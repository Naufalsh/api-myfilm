<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\GalleryController;
use App\Models\Gallery;
use App\Http\Controllers\KendaraanController;
use App\Http\Controllers\PlayerController;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/reviews/store', [ReviewController::class, 'store']);
Route::post('/reviews/edit/{id}', [ReviewController::class, 'update']);
Route::delete('/reviews/delete/{id}', [ReviewController::class, 'destroy']);

Route::get('/reviews', [ReviewController::class, 'index']);

//route gallery
Route::get('items/all', [GalleryController::class, 'allItems']);
Route::get('items', [GalleryController::class, 'index']);
Route::post('items', [GalleryController::class, 'store']);
Route::get('items/{item}', [GalleryController::class, 'show']);
Route::post('items/{item}', [GalleryController::class, 'update']);
Route::patch('items/{item}', [GalleryController::class, 'update']);
Route::delete('items/{item}', [GalleryController::class, 'destroy']);
Route::get('images/{id}', function ($id) {
    $ket = Gallery::where('id', $id)->firstOrFail();
    $path = storage_path('app/public/' . $ket->gambar);
    if (!file_exists($path)) {
        return response()->json(['message' => 'Image not found'], 404);
    }
    return response()->file($path);
});

//route kendaraan
Route::post('/kendaraans/store', [KendaraanController::class, 'store']);
Route::get('/kendaraans', [KendaraanController::class, 'index']);
Route::post('/kendaraans/{id}', [KendaraanController::class, 'update']);
Route::delete('/kendaraans/{id}', [KendaraanController::class, 'destroy']);

//route for player api
Route::get('players', [PlayerController::class, 'index']);
Route::post('players', [PlayerController::class, 'store']);
Route::post('players/{id}', [PlayerController::class, 'update']);
Route::delete('players/{id}', [PlayerController::class, 'destroy']);


require __DIR__ . '/auth.php';