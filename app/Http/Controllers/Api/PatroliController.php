<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatroliController extends Controller
{
    public function index(Request $request)
    {
        $patrolis = Patroli::select([
                'id',
                'perusahaan_id',
                'user_id',
                'lokasi_id',
                'tanggal_patroli',
                'waktu_mulai',
                'waktu_selesai',
                'status',
                'catatan'
            ])
            ->with([
                'user:id,name',
                'lokasi:id,nama_lokasi'
            ])
            ->orderBy('tanggal_patroli', 'desc')
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $patrolis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'tanggal_patroli' => 'required|date',
            'waktu_mulai' => 'required',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['user_id'] = auth()->id();
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['status'] = 'sedang_patroli';

            $patroli = Patroli::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patroli berhasil dimulai',
                'data' => $patroli->load(['user:id,name', 'lokasi:id,nama_lokasi']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai patroli: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Patroli $patroli)
    {
        $patroli->load([
            'user:id,name',
            'lokasi:id,nama_lokasi',
            'details.checkpoint:id,nama_checkpoint'
        ]);

        return response()->json([
            'success' => true,
            'data' => $patroli,
        ]);
    }
}
