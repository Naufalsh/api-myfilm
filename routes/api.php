<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReviewController;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('/reviews/store', [ReviewController::class, 'store']);
Route::post('/reviews/edit/{id}', [ReviewController::class, 'update']);
Route::delete('/reviews/delete/{id}', [ReviewController::class, 'destroy']);

Route::get('/reviews', [ReviewController::class, 'index']);


require __DIR__ . '/auth.php';