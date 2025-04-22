<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Education;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Position;

class PositionController extends Controller
{
    function getPosition($id)
    {
        $userPosition = Position::with('user')->find($id);
        if ($userPosition) {
            return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);
        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }

    }

    function insertPosition(Request $request)
    {
        $userPosition =Position::create($request->all());
        return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);
        
    }

    function updatePosition(Request $request, $id)
    {
        $userPosition = Position::find($id);
        if ($userPosition) {
            $userPosition->update($request->all());
            $userPosition->save();
            
            return response()->json($userPosition, 200, [], JSON_PRETTY_PRINT);

        } else {
            return response()->json(['message' => 'Data Tidak Ditemukan'], 404);
        }
    }

    public function destroyPosition($id)
    {
        $application = Position::findOrFail($id);
        $application->delete();
        return response()->json(['message' => 'Application deleted successfully'], 200, [], JSON_PRETTY_PRINT);
    }
}
