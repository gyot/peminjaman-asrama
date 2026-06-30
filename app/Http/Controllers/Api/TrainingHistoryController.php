<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrainingHistoryController extends Controller
{
    public function index()
    {
        // Ambil semua data pelatihan milik user yang login
        $histories = TrainingHistory::where('user_id', Auth::id())->get();
        return response()->json($histories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_pelatihan'     => 'required|string|max:255',
            'durasi'             => 'nullable|string|max:50',
            'materi'             => 'nullable|string',
            'penyelenggara'      => 'nullable|string|max:255',
            'tanggal_mulai'      => 'nullable|date',
            'tanggal_selesai'    => 'nullable|date',
            'lokasi'             => 'nullable|string|max:255',
            'sertifikat'         => 'nullable|file|mimes:pdf,jpg,png',
            'tingkat'            => 'nullable|string|max:50',
            'jenis_pelatihan'    => 'nullable|string|max:50',
            'catatan'            => 'nullable|string',
        ]);

        $data['user_id'] = Auth::id();

        if ($request->hasFile('sertifikat')) {
            $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat', 'public');
        }

        $history = TrainingHistory::create($data);

        return response()->json(['message' => 'Riwayat pelatihan berhasil ditambahkan', 'data' => $history]);
    }

    public function show($id)
    {
        $history = TrainingHistory::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return response()->json($history);
    }

    public function update(Request $request, $id)
    {
        $history = TrainingHistory::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $data = null;   
        if ($request->hasFile('sertifikat')) {
            $data = $request->validate([
                'nama_pelatihan'     => 'required|string|max:255',
                'durasi'             => 'nullable|string|max:50',
                'materi'             => 'nullable|string',
                'penyelenggara'      => 'nullable|string|max:255',
                'tanggal_mulai'      => 'nullable|date',
                'tanggal_selesai'    => 'nullable|date',
                'lokasi'             => 'nullable|string|max:255',
                'sertifikat'         => 'nullable|file|mimes:pdf,jpg,png',
                'tingkat'            => 'nullable|string|max:50',
                'jenis_pelatihan'    => 'nullable|string|max:50',
                'catatan'            => 'nullable|string',
            ]);

            // Hapus file lama jika ada
            if ($history->sertifikat) {
                Storage::disk('public')->delete($history->sertifikat);
            }

            $data['sertifikat'] = $request->file('sertifikat')->store('sertifikat', 'public');
        }else {
            # code...
            $data = $request->validate([
                'nama_pelatihan'     => 'required|string|max:255',
                'durasi'             => 'nullable|string|max:50',
                'materi'             => 'nullable|string',
                'penyelenggara'      => 'nullable|string|max:255',
                'tanggal_mulai'      => 'nullable|date',
                'tanggal_selesai'    => 'nullable|date',
                'lokasi'             => 'nullable|string|max:255',
                'tingkat'            => 'nullable|string|max:50',
                'jenis_pelatihan'    => 'nullable|string|max:50',
                'catatan'            => 'nullable|string',
            ]);
        }

        $history->update($data);

        return response()->json(['message' => 'Riwayat pelatihan berhasil diperbarui', 'data' => $history]);
    }

    public function destroy($id)
    {
        $history = TrainingHistory::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($history->sertifikat) {
            Storage::disk('public')->delete($history->sertifikat);
        }

        $history->delete();

        return response()->json(['message' => 'Riwayat pelatihan berhasil dihapus']);
    }
}