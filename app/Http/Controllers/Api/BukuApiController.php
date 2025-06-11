<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Buku;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class BukuApiController extends Controller
{
    public function index()
    {
        return response()->json(Buku::all());
    }

    public function show($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        return response()->json($buku);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_buku'    => 'required|unique:buku',
            'judul'        => 'required',
            'pengarang'    => 'required',
            'penerbit'     => 'required',
            'tahun_terbit' => 'required',
            'deskripsi'    => 'required',
            'gambar'       => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'status'       => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only([
            'kode_buku',
            'judul',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'deskripsi',
            'status'
        ]);

        if ($request->hasFile('gambar')) {
            $namaGambar = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images'), $namaGambar);
            $data['gambar'] = $namaGambar;
        }

        $buku = Buku::create($data);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $buku
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul'        => 'required',
            'pengarang'    => 'required',
            'penerbit'     => 'required',
            'tahun_terbit' => 'required',
            'deskripsi'    => 'required',
            'gambar'       => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'status'       => 'in:In Stock,Out of Stock'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            if ($buku->gambar) {
                $path = public_path('images/' . $buku->gambar);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }

            $namaGambar = time() . '.' . $request->gambar->extension();
            $request->gambar->move(public_path('images'), $namaGambar);
            $buku->gambar = $namaGambar;
        }

        $buku->update($request->only([
            'judul',
            'pengarang',
            'penerbit',
            'tahun_terbit',
            'deskripsi',
            'status'
        ]));

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data' => $buku
        ]);
    }

    public function destroy($id)
    {
        $buku = Buku::find($id);
        if (!$buku) {
            return response()->json(['message' => 'Buku tidak ditemukan'], 404);
        }

        if ($buku->gambar) {
            $path = public_path('images/' . $buku->gambar);
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $buku->delete();

        return response()->json(['message' => 'Buku berhasil dihapus']);
    }
}
