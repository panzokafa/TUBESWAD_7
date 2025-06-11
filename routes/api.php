<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BukuApiController;
use App\Http\Controllers\API\KategoriApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/buku', [BukuApiController::class, 'index']);
Route::get('/buku/{id}', [BukuApiController::class, 'show']);
Route::post('/buku', [BukuApiController::class, 'store']);
Route::put('/buku/{id}', [BukuApiController::class, 'update']); // untuk edit
Route::delete('/buku/{id}', [BukuApiController::class, 'destroy']); // untuk hapus

Route::apiResource('kategori', KategoriApiController::class);