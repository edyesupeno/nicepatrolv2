<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\Request;

class CheckpointController extends Controller
{
    public function index(Request $request)
    {
        $query = Checkpoint::select([
            'id',
            'perusahaan_id',
            'lokasi_id',
            'nama_checkpoint',
            'deskripsi',
            'qr_code',
            'latitude',
            'longitude',
            'is_active'
        ])->with('lokasi:id,nama_lokasi');

        // Filter by lokasi if provided
        if ($request->has('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        // Only active checkpoints
        $query->where('is_active', true);

        $checkpoints = $query->orderBy('nama_checkpoint')->get();

        return response()->json([
            'success' => true,
            'data' => $checkpoints,
        ]);
    }

    public function show(Checkpoint $checkpoint)
    {
        $checkpoint->load('lokasi:id,nama_lokasi');

        return response()->json([
            'success' => true,
            'data' => $checkpoint,
        ]);
    }
}
