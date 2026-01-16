<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::with('perusahaan')->get();
        return response()->json($lokasis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        $lokasi = Lokasi::create($validated);

        return response()->json($lokasi, 201);
    }

    public function show(Lokasi $lokasi)
    {
        return response()->json($lokasi->load('checkpoints'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'nama' => 'string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $lokasi->update($validated);

        return response()->json($lokasi);
    }

    public function destroy(Lokasi $lokasi)
    {
        $lokasi->delete();
        return response()->json(['message' => 'Lokasi berhasil dihapus']);
    }
}
