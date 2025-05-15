<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

class Profile extends Controller
{
    //
    function show($id)
    {
        $userProfile = UserProfile::with('user')->where('user_id','=',$id)->get();
        if ($userProfile) {
            return response()->json($userProfile, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'nomor_hp' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:100048',
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            // Simpan ke storage/app/public/images
            $path = $file->storeAs('images', $filename, 'public');

            // Simpan path publik ke DB: storage/images/namafile.jpg
            $validated['foto'] = str_replace('public/', 'storage/', $path);
        }

        $userProfile = UserProfile::create($validated);

        return response()->json($userProfile, 201, [], JSON_PRETTY_PRINT);
    }

    function updateProfile(Request $request,$id)
    {
        $userProfile = UserProfile::find($request->id);
        if ($userProfile) {
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($userProfile->foto && Storage::disk('public')->exists(str_replace('storage/', '', $userProfile->foto))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $userProfile->foto));
                }

                $file = $request->file('foto');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                // Simpan ke storage/app/public/images
                $path = $file->storeAs('images', $filename, 'public');

                // Simpan path publik ke DB: storage/images/namafile.jpg
                $userProfile->foto = str_replace('public/', 'storage/', $path);
            }

            $userProfile->tempat_lahir = $request->tempat_lahir;
            $userProfile->tanggal_lahir = $request->tanggal_lahir;
            $userProfile->alamat = $request->alamat;
            $userProfile->nomor_hp = $request->nomor_hp;
            $userProfile->save();

            return response()->json($userProfile, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }

    public function destroyProfile($id)
    {
        $application = UserProfile::findOrFail($id);
        $application->delete();
        return response()->json(['message' => 'Application deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    }

    public function updateAccount(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json($user, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }
    public function destroyAccount($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Account deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    }
    public function getUserById($id)
    {
        $user = User::find($id);
        if ($user) {
            return response()->json($user, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users, 200, [], JSON_PRETTY_PRINT);
    }
    public function getUserByEmail($email)
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return response()->json($user, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }
    public function getUserByName($name)
    {
        $user = User::where('name', $name)->first();
        if ($user) {
            return response()->json($user, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }
}
