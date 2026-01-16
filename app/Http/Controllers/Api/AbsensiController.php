<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kehadiran;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Get absensi summary for current month
     */
    public function summary(Request $request)
    {
        $user = $request->user();
        
        // Get karyawan_id from user
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        // Get current month or from request
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        // Get all kehadiran for the month
        $kehadirans = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get();
        
        // Count by status - map from database status to display status
        $summary = [
            'H' => 0,   // Hadir
            'T' => 0,   // Terlambat
            'PC' => 0,  // Pulang Cepat
            'TPC' => 0, // Terlambat & Pulang Cepat
            'A' => 0,   // Alpa
        ];
        
        $totalJamKerja = [
            'H' => 0,
            'T' => 0,
            'PC' => 0,
            'TPC' => 0,
            'A' => 0,
        ];
        
        foreach ($kehadirans as $kehadiran) {
            $status = $this->mapStatusToShort($kehadiran->status);
            
            if (isset($summary[$status])) {
                $summary[$status]++;
                
                // Calculate work hours from durasi_kerja (in minutes)
                if ($kehadiran->durasi_kerja) {
                    $totalJamKerja[$status] += $kehadiran->durasi_kerja;
                }
            }
        }
        
        // Format total jam kerja to "X Jam Y Menit"
        $formattedJamKerja = [];
        foreach ($totalJamKerja as $status => $minutes) {
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            $formattedJamKerja[$status] = "{$hours} Jam {$mins} Menit";
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'summary' => $summary,
                'total_jam_kerja' => $formattedJamKerja,
            ],
        ]);
    }
    
    /**
     * Get absensi schedule for a month
     */
    public function mySchedule(Request $request)
    {
        $user = $request->user();
        
        // Get karyawan_id from user
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        // Get month from request (format: YYYY-MM)
        $month = $request->input('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        // Get all kehadiran for the month
        $kehadirans = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();
        
        // Format schedules
        $schedules = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            
            // Find kehadiran for this date
            $kehadiran = $kehadirans->firstWhere('tanggal', $dateStr);
            
            $schedules[] = [
                'tanggal' => $dateStr,
                'tanggal_formatted' => $this->formatDateIndonesia($currentDate),
                'day_short' => $this->getDayShort($currentDate->dayOfWeek),
                'day_name' => $this->getDayName($currentDate->dayOfWeek),
                'day_number' => $currentDate->day,
                'absensi' => $kehadiran ? [
                    'status' => $this->mapStatusToShort($kehadiran->status),
                    'status_display' => $this->getStatusDisplay($kehadiran->status),
                    'jam_masuk' => $kehadiran->jam_masuk,
                    'jam_keluar' => $kehadiran->jam_keluar,
                    'keterangan' => $kehadiran->keterangan,
                    'warna' => $this->getStatusColor($this->mapStatusToShort($kehadiran->status)),
                ] : null,
            ];
            
            $currentDate->addDay();
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'schedules' => $schedules,
            ],
        ]);
    }
    
    /**
     * Map database status to short status
     */
    private function mapStatusToShort($status)
    {
        $mapping = [
            'hadir' => 'H',
            'terlambat' => 'T',
            'pulang_cepat' => 'PC',
            'alpa' => 'A',
            'izin' => 'A',
            'sakit' => 'A',
            'cuti' => 'A',
        ];
        
        // Check if it's combined status (terlambat & pulang_cepat)
        if (strpos($status, 'terlambat') !== false && strpos($status, 'pulang_cepat') !== false) {
            return 'TPC';
        }
        
        return $mapping[$status] ?? 'A';
    }
    
    /**
     * Helper: Format date in Indonesian
     */
    private function formatDateIndonesia($date)
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                   'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $dayName = $days[$date->dayOfWeek];
        $day = $date->day;
        $month = $months[$date->month - 1];
        $year = $date->year;
        
        return "{$dayName}, {$day} {$month} {$year}";
    }
    
    /**
     * Helper: Get day short name
     */
    private function getDayShort($dayOfWeek)
    {
        $days = ['MIN', 'SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB'];
        return $days[$dayOfWeek];
    }
    
    /**
     * Helper: Get day name
     */
    private function getDayName($dayOfWeek)
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayOfWeek];
    }
    
    /**
     * Helper: Get status display name
     */
    private function getStatusDisplay($status)
    {
        $displays = [
            'hadir' => 'Hadir',
            'terlambat' => 'Terlambat',
            'pulang_cepat' => 'Pulang Cepat',
            'alpa' => 'Alpa',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
        ];
        
        return $displays[$status] ?? 'Tidak Hadir';
    }
    
    /**
     * Helper: Get status color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'H' => '#16a34a',   // green-600
            'T' => '#7f1d1d',   // red-900 (dark red/black)
            'PC' => '#ca8a04',  // yellow-600
            'TPC' => '#1f2937', // gray-900 (black - most severe)
            'A' => '#dc2626',   // red-600
        ];
        
        return $colors[$status] ?? '#6b7280'; // gray-500 default
    }
}

