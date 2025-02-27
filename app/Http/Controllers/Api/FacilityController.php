<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Facility;

class FacilityController extends Controller
{
    public function index()
    {
        return Facility::all();
    }

    public function store(Request $request)
    {
        // return json_encode($request->image);
        $validated = $request->validate([
            'name' => 'required|string',
            'capacity' => 'required|integer',
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
        ]);

        $gambarPath = null;
        if ($request->hasFile('image')) {
            $gambarPath = $request->file('image')->store('fasilitas', 'public');
        }
        $fasilitas = Facility::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'unit' => $request->unit,
            'price' => $request->price,
            'image' => $gambarPath,
            'description' => $request->description
        ]);
        return response()->json(['message' => 'Fasilitas berhasil ditambahkan!', 'data' => $fasilitas], 201);
    }

    

    public function show(Facility $facility)
    {
        return $facility;
    }

    public function update(Request $request, Facility $facility)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string',
            'capacity' => 'sometimes|required|integer',
            'description' => 'nullable|string',
        ]);

        $facility->update($validated);
        return $facility;
    }

    public function destroy(Facility $facility)
    {
        $facility->delete();
        return response()->noContent();
    }
}