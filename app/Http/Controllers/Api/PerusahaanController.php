<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaans = Perusahaan::with('users')->get();
        return response()->json($perusahaans);
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:perusahaans',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $perusahaan = Perusahaan::create($validated);

        return response()->json($perusahaan, 201);
    }

    public function show(Perusahaan $perusahaan)
    {
        return response()->json($perusahaan->load('users', 'lokasis'));
    }

    public function update(Request $request, Perusahaan $perusahaan)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'string|max:255',
            'kode' => 'string|max:50|unique:perusahaans,kode,' . $perusahaan->id,
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $perusahaan->update($validated);

        return response()->json($perusahaan);
    }

    public function destroy(Perusahaan $perusahaan)
    {
        if (!auth()->user()->isSuperAdmin()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $perusahaan->delete();

        return response()->json(['message' => 'Perusahaan berhasil dihapus']);
    }
}
