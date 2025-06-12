<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Buku;
use App\Models\User;

class PeminjamanApiController extends Controller
{
    public function index()
    {
        return response()->json(Peminjaman::with('buku', 'user')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'buku_id' => 'required|exists:bukus,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $peminjaman = Peminjaman::create([
            'user_id' => $request->user_id,
            'buku_id' => $request->buku_id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
        ]);

        $buku = Buku::find($request->buku_id);
        $buku->stok -= 1;
        $buku->save();

        return response()->json($peminjaman, 201);
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with('buku', 'user')->findOrFail($id);
        return response()->json($peminjaman);
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->tanggal_dikembalikan = now();
        $peminjaman->save();

        $buku = Buku::find($peminjaman->buku_id);
        $buku->stok += 1;
        $buku->save();

        return response()->json(['message' => 'Buku berhasil dikembalikan.']);
    }
}
