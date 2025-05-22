<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\Position;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Position;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    // function getPosition($id)
    // {
    //     $userPosition = Position::with('user')->find($id);
    //     if ($userPosition) {
    //         return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);
    //     } else {
    //         return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
    //     }

    // }

    // function insertPosition(Request $request)
    // {
    //     $userPosition =Position::create($request->all());
    //     return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);
        
    // }

    // function updatePosition(Request $request, $id)
    // {
    //     $userPosition = Position::find($id);
    //     if ($userPosition) {
    //         $userPosition->update($request->all());
    //         $userPosition->save();
            
    //         return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);

    //     } else {
    //         return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
    //     }
    // }

    // public function destroyPosition($id)
    // {
    //     $application = Position::findOrFail($id);
    //     $application->delete();
    //     return response()->json(['message' => 'Application deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    // }

    public function index()
    {
        return Position::where('user_id', Auth::id())->orderBy('mulai_jabatan','DESC')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string',
            'unit_kerja' => 'required|string',
            'mulai_jabatan' => 'required|string',           
            'sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only([
            'nama_jabatan',
            'unit_kerja',
            'mulai_jabatan',
            'akhir_jabatan',
        ]);
        $data['user_id'] = 1;

        if ($request->hasFile('sk')) {
            $data['sk'] = $request->file('sk')->store('sk', 'public');
        }

        return Position::create($data);
    }

    public function update(Request $request, $id)
    {
        $education = Position::findOrFail($id);
        // $request->validate([
        //     'tingkat_pendidikan' => 'required|string',
        //     'institusi' => 'required|string',
        //     'jurusan' => 'nullable|string',
        //     // 'tahun_masuk' => 'required|string',
        //     // 'tahun_lulus' => 'nullable|string',
        //     'sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        // ]);

        $data = $request->only([
            'nama_jabatan',
            'unit_kerja',
            'mulai_jabatan',
            'akhir_jabatan',
        ]);
        $data['user_id'] = 1;
        if ($request->hasFile('sk')) {
            // Hapus sk lama
            if ($education->sk && Storage::disk('public')->exists($education->sk)) {
                Storage::disk('public')->delete($education->sk);
            }

            $data['sk'] = $request->file('sk')->store('sk', 'public');
        }

        $education->update($data);
        return $education;
    }

    public function destroy($id)
    {
        $education = Position::findOrFail($id);
        // if ($education->user_id !== Auth::id()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }
        if ($education->sk && Storage::disk('public')->exists($education->sk)) {
            Storage::disk('public')->delete($education->sk);
        }

        $education->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
