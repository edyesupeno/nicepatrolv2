<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::with('lokasi')->get();
        return view('admin.checkpoints.index', compact('checkpoints'));
    }

    public function create()
    {
        $lokasis = Lokasi::where('is_active', true)->get();
        return view('admin.checkpoints.create', compact('lokasis'));
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

        Checkpoint::create($validated);

        return redirect()->route('admin.checkpoints.index')
            ->with('success', 'Checkpoint berhasil ditambahkan');
    }

    public function edit(Checkpoint $checkpoint)
    {
        $lokasis = Lokasi::where('is_active', true)->get();
        return view('admin.checkpoints.edit', compact('checkpoint', 'lokasis'));
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:checkpoints,kode,' . $checkpoint->id,
            'deskripsi' => 'nullable|string',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ]);

        $checkpoint->update($validated);

        return redirect()->route('admin.checkpoints.index')
            ->with('success', 'Checkpoint berhasil diupdate');
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('admin.checkpoints.index')
            ->with('success', 'Checkpoint berhasil dihapus');
    }
}
