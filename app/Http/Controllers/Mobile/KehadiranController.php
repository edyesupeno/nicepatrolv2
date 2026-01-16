<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KehadiranController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get today's kehadiran
        $todayKehadiran = Kehadiran::where('karyawan_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();
        
        // Get this month kehadiran
        $monthKehadiran = Kehadiran::where('karyawan_id', $user->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->orderBy('tanggal', 'desc')
            ->get();
        
        return view('mobile.employee.kehadiran', compact('todayKehadiran', 'monthKehadiran'));
    }
    
    public function checkin(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            
            // Check if already checked in today
            $existing = Kehadiran::where('karyawan_id', $user->id)
                ->whereDate('tanggal', today())
                ->first();
            
            if ($existing && $existing->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-in hari ini',
                ], 400);
            }
            
            // Create or update kehadiran
            $kehadiran = Kehadiran::updateOrCreate(
                [
                    'karyawan_id' => $user->id,
                    'tanggal' => today(),
                ],
                [
                    'jam_masuk' => now()->format('H:i:s'),
                    'latitude_masuk' => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'status' => 'hadir',
                ]
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil',
                'data' => $kehadiran,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal check-in: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function checkout(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        try {
            DB::beginTransaction();
            
            $user = auth()->user();
            
            $kehadiran = Kehadiran::where('karyawan_id', $user->id)
                ->whereDate('tanggal', today())
                ->first();
            
            if (!$kehadiran || !$kehadiran->jam_masuk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan check-in hari ini',
                ], 400);
            }
            
            if ($kehadiran->jam_keluar) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-out hari ini',
                ], 400);
            }
            
            $kehadiran->update([
                'jam_keluar' => now()->format('H:i:s'),
                'latitude_keluar' => $request->latitude,
                'longitude_keluar' => $request->longitude,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
                'data' => $kehadiran,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal check-out: ' . $e->getMessage(),
            ], 500);
        }
    }
}
