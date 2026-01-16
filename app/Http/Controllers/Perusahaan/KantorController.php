<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Kantor;
use Illuminate\Http\Request;

class KantorController extends Controller
{
    public function index()
    {
        $kantors = Kantor::select(['id', 'nama', 'alamat', 'telepon', 'email', 'is_pusat', 'is_active'])
            ->paginate(10);
            
        return view('perusahaan.kantors.index', compact('kantors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_pusat' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama kantor wajib diisi',
            'nama.max' => 'Nama kantor maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'telepon.max' => 'No. telepon maksimal 20 karakter',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['is_pusat'] = $request->has('is_pusat') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        Kantor::create($validated);

        return redirect()->route('perusahaan.kantors.index')
            ->with('success', 'Kantor berhasil ditambahkan');
    }

    public function edit(Kantor $kantor)
    {
        return response()->json($kantor);
    }

    public function update(Request $request, Kantor $kantor)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_pusat' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'nama.required' => 'Nama kantor wajib diisi',
            'nama.max' => 'Nama kantor maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'telepon.max' => 'No. telepon maksimal 20 karakter',
        ]);

        $validated['is_pusat'] = $request->has('is_pusat') ? true : false;
        $validated['is_active'] = $request->has('is_active') ? true : false;

        $kantor->update($validated);

        return redirect()->route('perusahaan.kantors.index')
            ->with('success', 'Kantor berhasil diupdate');
    }

    public function destroy(Kantor $kantor)
    {
        $kantor->delete();

        return redirect()->route('perusahaan.kantors.index')
            ->with('success', 'Kantor berhasil dihapus');
    }
}
