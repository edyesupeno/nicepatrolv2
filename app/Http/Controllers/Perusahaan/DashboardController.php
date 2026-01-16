<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use App\Models\Kantor;
use App\Models\Checkpoint;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $stats = [
            'total_patroli' => Patroli::where('perusahaan_id', $perusahaanId)->count(),
            'patroli_hari_ini' => Patroli::where('perusahaan_id', $perusahaanId)
                ->whereDate('waktu_mulai', today())->count(),
            'total_kantor' => Kantor::where('perusahaan_id', $perusahaanId)->count(),
            'total_checkpoint' => Checkpoint::where('perusahaan_id', $perusahaanId)->count(),
            'total_petugas' => User::where('perusahaan_id', $perusahaanId)
                ->where('role', 'petugas')->count(),
            'patroli_berlangsung' => Patroli::where('perusahaan_id', $perusahaanId)
                ->where('status', 'berlangsung')->count(),
        ];

        $recentPatrolis = Patroli::where('perusahaan_id', $perusahaanId)
            ->with(['user', 'lokasi'])
            ->latest()
            ->take(5)
            ->get();

        return view('perusahaan.dashboard.index', compact('stats', 'recentPatrolis'));
    }
}
