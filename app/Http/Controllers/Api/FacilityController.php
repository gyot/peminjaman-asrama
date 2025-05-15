<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Facility;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    public function index()
    {
        return response()->json(Facility::where('status','<>','dihapus')->get(), 200);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            $gambarPath = $request->hasFile('image') ? $request->file('image')->store('fasilitas', 'public') : null;

            $fasilitas = Facility::create([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'capacity' => $request->capacity,
                'unit' => $request->unit,
                'price' => $request->price,
                'image' => $gambarPath,
                'description' => $request->description,
                'status' => ''
            ]);

            return response()->json(['message' => 'Fasilitas berhasil ditambahkan!', 'data' => $fasilitas], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create facility', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Facility $facility)
    {
        return response()->json($facility, 200);
    }

    public function update(Request $request)
    {
        $facility = Facility::find($request->id);
        $validated = $this->validateRequest($request);
        try {
            $gambarPath = $request->hasFile('image') ? $request->file('image')->store('fasilitas', 'public') : $facility->image;

            $facility->update([
                'user_id' => $request->user_id,
                'name' => $request->name,
                'capacity' => $request->capacity,
                'unit' => $request->unit,
                'price' => $request->price,
                'image' => $gambarPath,
                'description' => $request->description
            ]);

            return response()->json(['message' => 'Fasilitas berhasil diperbarui!', 'data' => $facility], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update facility', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Facility $facility)
    {
        try {
            // Update status fasilitas menjadi "dihapus"
            $facility->update([
                'status' => 'dihapus', // Pastikan kolom 'status' ada di tabel 'facilities'
            ]);

            return response()->json(['message' => 'Status fasilitas berhasil diperbarui menjadi dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update facility status', 'message' => $e->getMessage()], 500);
        }
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);
    }
}