<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use App\Models\Kantor;
use App\Models\Checkpoint;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\Cuti;
use App\Models\Resign;
use App\Models\Project;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Load dashboard with minimal data for fast initial load
        return view('perusahaan.dashboard.index');
    }

    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $stats = [
            // Basic Stats
            'total_patroli' => Patroli::where('perusahaan_id', $perusahaanId)->count(),
            'patroli_hari_ini' => Patroli::where('perusahaan_id', $perusahaanId)
                ->whereDate('waktu_mulai', today())->count(),
            'patroli_berlangsung' => Patroli::where('perusahaan_id', $perusahaanId)
                ->where('status', 'berlangsung')->count(),
            'total_kantor' => Kantor::where('perusahaan_id', $perusahaanId)->count(),
            'total_checkpoint' => Checkpoint::where('perusahaan_id', $perusahaanId)->count(),
            'total_petugas' => User::where('perusahaan_id', $perusahaanId)
                ->where('role', 'petugas')->count(),
            
            // HR Stats
            'total_karyawan' => Karyawan::where('perusahaan_id', $perusahaanId)->count(),
            'karyawan_aktif' => Karyawan::where('perusahaan_id', $perusahaanId)
                ->where('is_active', true)->count(),
            'kehadiran_hari_ini' => Kehadiran::where('perusahaan_id', $perusahaanId)
                ->whereDate('tanggal', today())->count(),
            'cuti_pending' => Cuti::where('perusahaan_id', $perusahaanId)
                ->where('status', 'pending')->count(),
            'resign_pending' => Resign::where('perusahaan_id', $perusahaanId)
                ->where('status', 'pending')->count(),
            'total_projects' => Project::where('perusahaan_id', $perusahaanId)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get patrol chart data
     */
    public function getPatrolChart()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Last 7 days patrol data
        $patrolData = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $count = Patroli::where('perusahaan_id', $perusahaanId)
                ->whereDate('waktu_mulai', $date)
                ->count();
            
            $patrolData[] = $count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $patrolData
        ]);
    }

    /**
     * Get attendance chart data
     */
    public function getAttendanceChart()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Last 7 days attendance data
        $attendanceData = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('M d');
            
            $count = Kehadiran::where('perusahaan_id', $perusahaanId)
                ->whereDate('tanggal', $date)
                ->count();
            
            $attendanceData[] = $count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $attendanceData
        ]);
    }

    /**
     * Get project distribution chart
     */
    public function getProjectChart()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $projectData = Project::where('perusahaan_id', $perusahaanId)
            ->withCount('karyawans')
            ->get()
            ->map(function ($project) {
                return [
                    'name' => $project->nama,
                    'value' => $project->karyawans_count
                ];
            });

        return response()->json($projectData);
    }

    /**
     * Get monthly patrol trend
     */
    public function getMonthlyPatrolTrend()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Last 12 months data
        $monthlyData = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $count = Patroli::where('perusahaan_id', $perusahaanId)
                ->whereYear('waktu_mulai', $date->year)
                ->whereMonth('waktu_mulai', $date->month)
                ->count();
            
            $monthlyData[] = $count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $monthlyData
        ]);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $recentPatrolis = Patroli::where('perusahaan_id', $perusahaanId)
            ->with(['user', 'lokasi'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($patroli) {
                return [
                    'id' => $patroli->hash_id,
                    'type' => 'patrol',
                    'title' => 'Patroli ' . $patroli->lokasi->nama,
                    'user' => $patroli->user->name,
                    'time' => $patroli->waktu_mulai->diffForHumans(),
                    'status' => $patroli->status,
                    'icon' => 'fas fa-shield-alt',
                    'color' => $patroli->status === 'berlangsung' ? 'blue' : ($patroli->status === 'selesai' ? 'green' : 'red')
                ];
            });

        return response()->json($recentPatrolis);
    }

    /**
     * Get attendance summary for today
     */
    public function getTodayAttendanceSummary()
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $totalKaryawan = Karyawan::where('perusahaan_id', $perusahaanId)
            ->where('is_active', true)
            ->count();
        
        $hadir = Kehadiran::where('perusahaan_id', $perusahaanId)
            ->whereDate('tanggal', today())
            ->where('status_kehadiran', 'hadir')
            ->count();
        
        $terlambat = Kehadiran::where('perusahaan_id', $perusahaanId)
            ->whereDate('tanggal', today())
            ->where('status_kehadiran', 'terlambat')
            ->count();
        
        $alpha = $totalKaryawan - $hadir - $terlambat;

        return response()->json([
            'total' => $totalKaryawan,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'alpha' => max(0, $alpha),
            'percentage' => $totalKaryawan > 0 ? round(($hadir + $terlambat) / $totalKaryawan * 100, 1) : 0
        ]);
    }
}
