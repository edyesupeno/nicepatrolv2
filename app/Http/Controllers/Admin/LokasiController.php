<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::with('perusahaan')->withCount('checkpoints')->get();
        return view('admin.lokasis.index', compact('lokasis'));
    }

    public function create()
    {
        return view('admin.lokasis.create');
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

        Lokasi::create($validated);

        return redirect()->route('admin.lokasis.index')
            ->with('success', 'Lokasi berhasil ditambahkan');
    }

    public function edit(Lokasi $lokasi)
    {
        return view('admin.lokasis.edit', compact('lokasi'));
    }

    public function update(Request $request, Lokasi $lokasi)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $lokasi->update($validated);

        return redirect()->route('admin.lokasis.index')
            ->with('success', 'Lokasi berhasil diupdate');
    }

    public function destroy(Lokasi $lokasi)
    {
        $lokasi->delete();

        return redirect()->route('admin.lokasis.index')
            ->with('success', 'Lokasi berhasil dihapus');
    }
}
