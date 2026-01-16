<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PatroliController extends Controller
{
    public function index(Request $request)
    {
        $query = Patroli::with(['user', 'lokasi'])
            ->withCount('details');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('waktu_mulai', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('waktu_mulai', '<=', $request->end_date);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $patrolis = $query->latest('waktu_mulai')
            ->paginate(15);

        // Statistics
        $stats = [
            'total' => Patroli::count(),
            'today' => Patroli::whereDate('waktu_mulai', Carbon::today())->count(),
            'week' => Patroli::whereBetween('waktu_mulai', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month' => Patroli::whereMonth('waktu_mulai', Carbon::now()->month)->count(),
        ];

        $users = User::where('perusahaan_id', auth()->user()->perusahaan_id)
            ->where('role', '!=', 'superadmin')
            ->get();
            
        return view('perusahaan.patrolis.index', compact('patrolis', 'stats', 'users'));
    }

    public function show(Patroli $patroli)
    {
        $patroli->load(['user', 'lokasi', 'details.checkpoint']);
        return view('perusahaan.patrolis.show', compact('patroli'));
    }
}
