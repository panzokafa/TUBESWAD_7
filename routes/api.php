<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuApiController;
use App\Http\Controllers\Api\KategoriApiController;
use App\Http\Controllers\Api\AuthApiController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthApiController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthApiController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/buku', [BukuApiController::class, 'index']);
    Route::get('/buku/{id}', [BukuApiController::class, 'show']);
    Route::post('/buku', [BukuApiController::class, 'store']);
    Route::put('/buku/{id}', [BukuApiController::class, 'update']);
    Route::delete('/buku/{id}', [BukuApiController::class, 'destroy']);

    Route::apiResource('kategori', KategoriApiController::class);
});
