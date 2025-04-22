<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Education;
use App\Models\User;

class EducationController extends Controller
{
    function getEducation($id)
    {
        $userEducation = Education::with('user')->find($id);
        if ($userEducation) {
            return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

    }

    function insertEducation(Request $request)
    {
        $userEducation =Education::create($request->all());
        return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);
        
    }

    function updateEducation(Request $request, $id)
    {
        $userEducation = Education::find($id);
        if ($userEducation) {
            $userEducation->update($request->all());
            $userEducation->save();
            
            return response()->json($userEducation, 200, [], JSON_PRETTY_PRINT);

        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }

    public function destroyEducation($id)
    {
        $application = Education::findOrFail($id);
        $application->delete();
        return response()->json(['message' => 'Application deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    }
}
