<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\Kantor;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function index()
    {
        $checkpoints = Checkpoint::with('kantor')
            ->paginate(10);
        
        $kantors = Kantor::all();
            
        return view('perusahaan.checkpoints.index', compact('checkpoints', 'kantors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:255',
        ], [
            'lokasi_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama checkpoint wajib diisi',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['kode_qr'] = 'CP-' . strtoupper(uniqid());

        Checkpoint::create($validated);

        return redirect()->route('perusahaan.checkpoints.index')
            ->with('success', 'Checkpoint berhasil ditambahkan');
    }

    public function edit(Checkpoint $checkpoint)
    {
        return response()->json($checkpoint);
    }

    public function update(Request $request, Checkpoint $checkpoint)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:kantors,id',
            'nama' => 'required|string|max:255',
        ], [
            'lokasi_id.required' => 'Kantor wajib dipilih',
            'nama.required' => 'Nama checkpoint wajib diisi',
        ]);

        $checkpoint->update($validated);

        return redirect()->route('perusahaan.checkpoints.index')
            ->with('success', 'Checkpoint berhasil diupdate');
    }

    public function destroy(Checkpoint $checkpoint)
    {
        $checkpoint->delete();

        return redirect()->route('perusahaan.checkpoints.index')
            ->with('success', 'Checkpoint berhasil dihapus');
    }
}
