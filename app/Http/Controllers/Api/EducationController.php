<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Education;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    // function getEducation($id)
    // {
    //     $userEducation = Education::with('user')->find($id);
    //     if ($userEducation) {
    //         return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);
    //     } else {
    //         return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
    //     }

    // }

    // function insertEducation(Request $request)
    // {
    //     $userEducation =Education::create($request->all());
    //     return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);
        
    // }

    // function updateEducation(Request $request, $id)
    // {
    //     $userEducation = Education::find($id);
    //     if ($userEducation) {
    //         $userEducation->update($request->all());
    //         $userEducation->save();
            
    //         return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);

    //     } else {
    //         return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
    //     }
    // }

    // public function destroyEducation($id)
    // {
    //     $application = Education::findOrFail($id);
    //     $application->delete();
    //     return response()->json(['message' => 'Application deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    // }
    public function index()
    {
        return Education::where('user_id', Auth::id())->orderBy('tahun_lulus','DESC')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'tingkat_pendidikan' => 'required|string',
            'institusi' => 'required|string',
            'jurusan' => 'nullable|string',
            'tahun_masuk' => 'required|string',
            'tahun_lulus' => 'nullable|string',
            'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['tingkat_pendidikan', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_lulus']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('ijazah')) {
            $data['ijazah'] = $request->file('ijazah')->store('ijazah', 'public');
        }

        return Education::create($data);
    }

    public function update(Request $request, $id)
    {
        $education = Education::findOrFail($id);
        // $request->validate([
        //     'tingkat_pendidikan' => 'required|string',
        //     'institusi' => 'required|string',
        //     'jurusan' => 'nullable|string',
        //     // 'tahun_masuk' => 'required|string',
        //     // 'tahun_lulus' => 'nullable|string',
        //     'ijazah' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        // ]);

        $data = $request->only(['tingkat_pendidikan', 'institusi', 'jurusan', 'tahun_masuk', 'tahun_lulus']);
        $data['user_id'] = Auth::id();
        if ($request->hasFile('ijazah')) {
            // Hapus ijazah lama
            if ($education->ijazah && Storage::disk('public')->exists($education->ijazah)) {
                Storage::disk('public')->delete($education->ijazah);
            }

            $data['ijazah'] = $request->file('ijazah')->store('ijazah', 'public');
        }

        $education->update($data);
        return $education;
    }

    public function destroy($id)
    {
        $education = Education::findOrFail($id);
        // if ($education->user_id !== Auth::id()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }
        if ($education->ijazah && Storage::disk('public')->exists($education->ijazah)) {
            Storage::disk('public')->delete($education->ijazah);
        }

        $education->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
