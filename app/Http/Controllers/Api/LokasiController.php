<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $lokasis = Lokasi::select([
                'id',
                'perusahaan_id',
                'nama_lokasi',
                'alamat',
                'latitude',
                'longitude',
                'is_active'
            ])
            ->where('is_active', true)
            ->orderBy('nama_lokasi')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lokasis,
        ]);
    }
}
