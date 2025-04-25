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
    function getProfile($id)
    {
        $userProfile = UserProfile::with('user')->find($id);
        if ($userProfile) {
            return response()->json($userProfile, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

    }

    function updateProfile(Request $request)
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
}
