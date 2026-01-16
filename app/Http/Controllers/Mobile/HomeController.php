<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Redirect based on role
        if ($user->isSecurityOfficer()) {
            return $this->securityDashboard();
        } elseif ($user->isOfficeEmployee()) {
            return $this->employeeDashboard();
        }
        
        // Default fallback
        abort(403, 'Unauthorized role for mobile app');
    }
    
    private function securityDashboard()
    {
        $user = auth()->user();
        
        // Get today's patroli
        $todayPatroli = Patroli::where('user_id', $user->id)
            ->whereDate('tanggal_patroli', today())
            ->count();

        // Get active patroli
        $activePatroli = Patroli::where('user_id', $user->id)
            ->where('status', 'sedang_patroli')
            ->first();
        
        // Get this month stats
        $monthPatroli = Patroli::where('user_id', $user->id)
            ->whereMonth('tanggal_patroli', now()->month)
            ->whereYear('tanggal_patroli', now()->year)
            ->count();

        return view('mobile.security.home', compact('todayPatroli', 'activePatroli', 'monthPatroli'));
    }
    
    private function employeeDashboard()
    {
        $user = auth()->user();
        
        // Employee stats (kehadiran, dll)
        // TODO: Implement employee specific data
        
        return view('mobile.employee.home', compact('user'));
    }
}
