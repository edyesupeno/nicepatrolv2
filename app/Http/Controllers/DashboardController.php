<?php

namespace App\Http\Controllers;

use App\Models\Patroli;
use App\Models\Kantor;
use App\Models\Checkpoint;
use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on role
        if ($user->isSuperAdmin()) {
            $stats = [
                'total_perusahaan' => Perusahaan::count(),
                'total_patroli' => Patroli::count(),
                'patroli_hari_ini' => Patroli::whereDate('waktu_mulai', today())->count(),
                'total_kantor' => Kantor::count(),
                'total_project' => Project::count(),
                'total_checkpoint' => Checkpoint::count(),
                'total_petugas' => User::where('role', 'petugas')->count(),
            ];

            $recentPatrolis = Patroli::with(['user'])->latest()->take(5)->get();

            return view('dashboard', compact('stats', 'recentPatrolis'));
        } else {
            return redirect()->route('perusahaan.dashboard');
        }
    }
}
