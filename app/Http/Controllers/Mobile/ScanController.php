<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('mobile.security.scan');
    }
    
    public function verify(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);
        
        $checkpoint = Checkpoint::where('qr_code', $request->qr_code)
            ->where('is_active', true)
            ->first();
        
        if (!$checkpoint) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau checkpoint tidak aktif',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Checkpoint ditemukan',
            'data' => [
                'id' => $checkpoint->id,
                'nama_checkpoint' => $checkpoint->nama_checkpoint,
                'lokasi' => $checkpoint->lokasi->nama_lokasi ?? null,
            ],
        ]);
    }
}
