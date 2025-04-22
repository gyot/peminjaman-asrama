<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserProfile;

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

    function updateProfile(Request $request, $id)
    {
        $userProfile = UserProfile::find($id);
        if ($userProfile) {
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images'), $filename);
                $userProfile->foto = 'images/' . $filename;
            }else {
                $userProfile->foto = $userProfile->foto;
            }
            $userProfile->tempat_lahir = $request->tempat_lahir;
            $userProfile->tanggal_lahir = $request->tanggal_lahir;
            $userProfile->alamat = $request->alamat;
            $userProfile->nomor_hp = $request->nomor_hp;
            
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
