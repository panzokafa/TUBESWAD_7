<?php

namespace App\Http\Controllers\Api;
            
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AnggotaApiController extends Controller
{
    public function index()
    {
        $anggota = User::where('isAdmin', 0)->with('profile')->get();
        return response()->json(['anggota' => $anggota]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'npm'=> 'required|unique:profile',
            'prodi'=> 'required',
            'alamat'=> 'required',
            'noTelp'=> 'required',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $profile = Profile::create([
            'npm' => $request->npm,
            'prodi' => $request->prodi,
            'alamat' => $request->alamat,
            'noTelp' => $request->noTelp,
            'users_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Anggota berhasil ditambahkan',
            'user' => $user,
            'profile' => $profile
        ], 201);
    }

    public function show($id)
    {
        $user = User::with('profile')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }
 
        $peminjaman = Peminjaman::where('users_id', $id)->get();

        return response()->json([
            'user' => $user,
            'peminjaman' => $peminjaman
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $profile = Profile::where('users_id', $id)->first();

        if (!$user || !$profile) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'npm'=> 'required',
            'prodi'=> 'required',
            'alamat'=> 'required',
            'noTelp'=> 'required',
            'photoProfile' => 'nullable|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->name = $request->name;
        $profile->npm = $request->npm;
        $profile->prodi = $request->prodi;
        $profile->alamat = $request->alamat;
        $profile->noTelp = $request->noTelp;

        if ($request->hasFile('photoProfile')) {
            $photo = $request->file('photoProfile');
            $photoName = time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images/photoProfile'), $photoName);
            $profile->photoProfile = $photoName;
        }

        $user->save();
        $profile->save();

        return response()->json([
            'message' => 'Anggota berhasil diperbarui',
            'user' => $user,
            'profile' => $profile
        ]);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Anggota tidak ditemukan'], 404);

        $user->delete();

        return response()->json(['message' => 'Anggota berhasil dihapus']);
    }
}
