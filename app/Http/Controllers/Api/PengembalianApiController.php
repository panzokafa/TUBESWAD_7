<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Buku;
use App\Models\User;
use App\Models\Profile;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;    

class PengembalianApiController extends Controller
{
    //
    public function index()
    {
        $iduser = Auth::id(); // Jika menggunakan Sanctum
        $profile = Profile::where('users_id', $iduser)->first();
        $buku = Buku::where('status', 'dipinjam')->get();
        $user = User::all();
        $peminjam = Profile::where('users_id', '>', '0')->get();

        return response()->json([
            'profile' => $profile,
            'users' => $user,
            'buku' => $buku,
            'peminjam' => $peminjam,
        ]);
    }

    public function pengembalian(Request $request)
    {
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'buku_id' => 'required|exists:buku,id',
        ]);

        $pinjaman = Peminjaman::where('users_id', $request->users_id)
            ->where('buku_id', $request->buku_id)
            ->whereNull('tanggal_pengembalian');

        $dataPinjaman = $pinjaman->first();
        $count = $pinjaman->count();

        if ($count === 1) {
            try {
                DB::beginTransaction();

                // update tanggal pengembalian
                $dataPinjaman->tanggal_pengembalian = Carbon::now()->toDateString();
                $dataPinjaman->save();

                // update status buku
                $buku = Buku::findOrFail($request->buku_id);
                $buku->status = 'In Stock';
                $buku->save();

                DB::commit();

                return response()->json([
                    'message' => 'Berhasil mengembalikan buku',
                    'data' => $dataPinjaman
                ], 200);
            } catch (\Throwable $th) {
                DB::rollback();
                return response()->json([
                    'message' => 'Terjadi kesalahan saat proses pengembalian',
                    'error' => $th->getMessage()
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'Data peminjaman tidak valid atau sudah dikembalikan',
            ], 404);
        }
    }
}
