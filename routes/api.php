<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuApiController;
use App\Http\Controllers\Api\KategoriApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\AnggotaApiController;
use App\Http\Controllers\Api\PengembalianApiController;
use App\Http\Controllers\Api\PeminjamanApiController;

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
    //membuat routes api untuk kategori buku
    Route::apiResource('kategori', KategoriApiController::class);

    Route::middleware('isAdmin')->group(function () {
        Route::get('/anggota', [AnggotaApiController::class, 'index']);
        Route::post('/anggota', [AnggotaApiController::class, 'store']);
        Route::delete('/anggota/{id}', [AnggotaApiController::class, 'destroy']);
    });

    Route::get('/anggota/{id}', [AnggotaApiController::class, 'show']);
    Route::put('/anggota/{id}', [AnggotaApiController::class, 'update']);

    
    Route::patch('/pengembalian/{id}', [PengembalianApiController::class, 'pengembalian']);
    Route::get('/pengembalian/{id}', [PengembalianApiController::class, 'index']);


    Route::post('/peminjaman', [PeminjamanApiController::class, 'store']);
    Route::get('/peminjaman', [PeminjamanApiController::class, 'index']);
    Route::get('/peminjaman/{id}', [PeminjamanApiController::class, 'show']);
});


