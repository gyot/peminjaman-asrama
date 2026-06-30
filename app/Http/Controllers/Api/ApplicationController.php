<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\Approvals;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //        
        // return Application::where('status', 'pending')->get();
        // $applications = Application::with('user')->get();
        $applications = Application::whereDoesntHave('approval')->get();


        return response()->json($applications, 200, [], JSON_PRETTY_PRINT);
    }

    public function show($id)
    {
        //        
        // return Application::where('status', 'pending')->get();
        // $applications = Application::with('user')->get();
        $application = Application::with(['facilities','approval'])->findOrFail($id);
        return response()->json($application);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string',
        'address' => 'required|string',
        'event_name' => 'required|string',
        'event_start_date' => 'required|date',
        'event_end_date' => 'required|date',
        'phone_number' => 'required|string',
        'notes' => 'nullable|string',
        'facility_id' => 'required|array',
        'facility_id.*' => 'exists:facilities,id',
    ]);

    DB::beginTransaction();
    try {
        $application = Application::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'event_name' => $validated['event_name'],
            'event_start_date' => $validated['event_start_date'],
            'event_end_date' => $validated['event_end_date'],
            'phone_number' => $validated['phone_number'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $application->facilities()->sync($validated['facility_id']);

        DB::commit();
        return response()->json([
            'message' => 'Aplikasi berhasil disimpan.',
            'data' => $application->load('facilities') // Memuat relasi 'facilities' jika diperlukan
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}



    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        // $application = Application::find($id);
        // $application->status = 'confirmed';
        // $application->save();
        // return response()->json($application, 200, [], JSON_PRETTY_PRINT);
        $application = Application::find($id); 
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }
        $application->status = 'approved';
        $application->save();
        return response()->json($application, 200, [], JSON_PRETTY_PRINT);

    }

    public function approvals() {
        $approvals = Approvals::with(['user', 'applications'])->get();
        return response()->json($approvals  , 200, [], JSON_PRETTY_PRINT);
        
    }

    public function setApproval(Request $request, $id)
{
    $request->validate([
        'id_user' => 'required|exists:users,id',
        'status' => 'required|string',
        'message' => 'nullable|string',
    ]);

    // Pastikan aplikasi ada
    if (!Application::find($id)) {
        return response()->json(['message' => 'Application not found'], 404);
    }else {
        $approval = Approvals::create([
            'id_applications' => $id, 
            'id_user' => $request->id_user, 
            'status' => $request->status, 
            'notes' => $request->message,
        ]);
    
        return response()->json([
            'message' => 'Approval created',
            'data' => $approval,
        ]);
        // return $id;
    }

}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
