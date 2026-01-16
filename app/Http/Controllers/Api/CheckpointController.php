<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::with('lokasi')->get();
        return response()->json($checkpoints);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:checkpoints',
            'deskripsi' => 'nullable|string',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;

        $checkpoint = Checkpoint::create($validated);

        return response()->json($checkpoint, 201);
    }

    public function show(Checkpoint $checkpoint)
    {
        return response()->json($checkpoint->load('lokasi'));
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'lokasi_id' => 'exists:lokasis,id',
            'nama' => 'string|max:255',
            'kode' => 'string|max:50|unique:checkpoints,kode,' . $checkpoint->id,
            'deskripsi' => 'nullable|string',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ]);

        $checkpoint->update($validated);

        return response()->json($checkpoint);
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();
        return response()->json(['message' => 'Checkpoint berhasil dihapus']);
    }
}
