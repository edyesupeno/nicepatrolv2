<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaans = Perusahaan::withCount('users')->get();
        return view('admin.perusahaans.index', compact('perusahaans'));
    }

    public function create()
    {
        return view('admin.perusahaans.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:perusahaans',
            'is_active' => 'boolean',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|min:6',
        ]);

        // Create perusahaan
        $perusahaan = Perusahaan::create([
            'nama' => $validated['nama'],
            'kode' => $validated['kode'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Create admin user for this perusahaan
        \App\Models\User::create([
            'perusahaan_id' => $perusahaan->id,
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['admin_password']),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan dan admin berhasil ditambahkan');
    }

    public function show(Perusahaan $perusahaan)
    {
        $perusahaan->load('users', 'lokasis');
        return view('admin.perusahaans.show', compact('perusahaan'));
    }

    public function edit(Perusahaan $perusahaan)
    {
        return view('admin.perusahaans.edit', compact('perusahaan'));
    }

    public function update(Request $request, Perusahaan $perusahaan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:50|unique:perusahaans,kode,' . $perusahaan->id,
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $perusahaan->update($validated);

        return redirect()->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan berhasil diupdate');
    }

    public function destroy(Perusahaan $perusahaan)
    {
        $perusahaan->delete();

        return redirect()->route('admin.perusahaans.index')
            ->with('success', 'Perusahaan berhasil dihapus');
    }
}
