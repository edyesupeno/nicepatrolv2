<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use App\Models\PatroliDetail;
use Illuminate\Http\Request;

class PatroliController extends Controller
{
    public function index()
    {
        $patrolis = Patroli::with(['lokasi', 'user', 'details.checkpoint'])->get();
        return response()->json($patrolis);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'catatan' => 'nullable|string',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['user_id'] = auth()->id();
        $validated['waktu_mulai'] = now();
        $validated['status'] = 'berlangsung';

        $patroli = Patroli::create($validated);

        return response()->json($patroli->load('lokasi'), 201);
    }

    public function show(Patroli $patroli)
    {
        return response()->json($patroli->load(['lokasi', 'user', 'details.checkpoint']));
    }

    public function update(Request $request, Patroli $patroli)
    {
        $validated = $request->validate([
            'status' => 'in:berlangsung,selesai,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        if ($request->status === 'selesai' && !$patroli->waktu_selesai) {
            $validated['waktu_selesai'] = now();
        }

        $patroli->update($validated);

        return response()->json($patroli);
    }

    public function scanCheckpoint(Request $request, Patroli $patroli)
    {
        $validated = $request->validate([
            'checkpoint_id' => 'required|exists:checkpoints,id',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'catatan' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
            'status' => 'in:normal,bermasalah',
        ]);

        $validated['patroli_id'] = $patroli->id;
        $validated['waktu_scan'] = now();

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('patroli-photos', 'public');
            $validated['foto'] = $path;
        }

        $detail = PatroliDetail::create($validated);

        return response()->json($detail->load('checkpoint'), 201);
    }

    public function destroy(Patroli $patroli)
    {
        $patroli->delete();
        return response()->json(['message' => 'Patroli berhasil dihapus']);
    }
}
