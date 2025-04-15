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
        $application = Application::with('facilities')->findOrFail($id);
        return response()->json($application);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validasi input
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

    // Simpan data ke tabel `applications`
    $application = Application::create([
        'name' => $validated['name'],
        'address' => $validated['address'],
        'event_name' => $validated['event_name'],
        'event_start_date' => $validated['event_start_date'],
        'event_end_date' => $validated['event_end_date'],
        'phone_number' => $validated['phone_number'],
        'notes' => $validated['notes'] ?? null,
    ]);

    // Simpan data ke pivot table `application_facility`
    $application->facilities()->sync($validated['facility_id']);

    return response()->json(['message' => 'Aplikasi berhasil disimpan.'], 201);
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

    public function setApproval(Request $request,$id) {
        return Approvals::create([
            'id_applications' => $id, 
            'id_user' => $request->id_user, 
            'status' => $request->status, 
            'notes' => $request->message,
        ]);  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
