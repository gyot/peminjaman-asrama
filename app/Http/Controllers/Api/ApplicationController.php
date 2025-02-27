<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //        
        // return Application::all();
        $applications = Application::with('user')->get();

        return response()->json($applications, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'facility_id' => 'required|exists:facilities,id',
            'event_name' => 'required|string',
            'name' => 'required|string',
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        return Application::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
