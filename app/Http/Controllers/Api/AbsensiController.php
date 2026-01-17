<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kehadiran;
use App\Models\LokasiAbsensi;
use App\Models\JadwalShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Get available attendance locations for user's project
     */
    public function getLokasiAbsensi(Request $request)
    {
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        // Get locations for user's project
        $lokasis = LokasiAbsensi::where('project_id', $user->karyawan->project_id)
            ->select('id', 'nama_lokasi', 'alamat', 'latitude', 'longitude', 'radius')
            ->get()
            ->map(function ($lokasi) {
                return [
                    'id' => $lokasi->id,
                    'hash_id' => $lokasi->hash_id,
                    'nama_lokasi' => $lokasi->nama_lokasi,
                    'alamat' => $lokasi->alamat,
                    'latitude' => (float) $lokasi->latitude,
                    'longitude' => (float) $lokasi->longitude,
                    'radius' => (int) $lokasi->radius,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $lokasis,
        ]);
    }
    
    /**
     * Check attendance status for today
     */
    public function checkTodayStatus(Request $request)
    {
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        $today = now()->format('Y-m-d');
        
        $kehadiran = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        $canCheckIn = !$kehadiran || !$kehadiran->jam_masuk;
        $canTakeBreak = $kehadiran && $kehadiran->jam_masuk && !$kehadiran->jam_istirahat;
        $canReturnFromBreak = $kehadiran && $kehadiran->jam_istirahat && !$kehadiran->jam_kembali;
        $canCheckOut = $kehadiran && $kehadiran->jam_masuk && !$kehadiran->jam_keluar && 
                      (!$kehadiran->jam_istirahat || $kehadiran->jam_kembali);
        
        return response()->json([
            'success' => true,
            'data' => [
                'can_check_in' => $canCheckIn,
                'can_take_break' => $canTakeBreak,
                'can_return_from_break' => $canReturnFromBreak,
                'can_check_out' => $canCheckOut,
                'kehadiran' => $kehadiran ? [
                    'jam_masuk' => $kehadiran->jam_masuk,
                    'jam_keluar' => $kehadiran->jam_keluar,
                    'jam_istirahat' => $kehadiran->jam_istirahat,
                    'jam_kembali' => $kehadiran->jam_kembali,
                    'status' => $kehadiran->status,
                    'lokasi_masuk' => $kehadiran->lokasi_masuk,
                    'lokasi_keluar' => $kehadiran->lokasi_keluar,
                    'lokasi_istirahat' => $kehadiran->lokasi_istirahat,
                    'lokasi_kembali' => $kehadiran->lokasi_kembali,
                ] : null,
            ],
        ]);
    }
    
    /**
     * Take break
     */
    public function takeBreak(Request $request)
    {
        $request->validate([
            'lokasi_absensi_id' => 'required|exists:lokasi_absensis,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        $today = now()->format('Y-m-d');
        
        // Get today's attendance
        $kehadiran = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        if (!$kehadiran || !$kehadiran->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absensi masuk hari ini',
            ], 400);
        }
        
        if ($kehadiran->jam_istirahat) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi istirahat hari ini',
            ], 400);
        }
        
        // Get location details
        $lokasiAbsensi = LokasiAbsensi::find($request->lokasi_absensi_id);
        
        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $lokasiAbsensi->latitude,
            $lokasiAbsensi->longitude
        );
        
        $onRadius = $distance <= $lokasiAbsensi->radius;
        
        try {
            DB::beginTransaction();
            
            // Upload photo
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = 'absensi_istirahat_' . $user->karyawan->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('absensi', $fileName, 'public');
            }
            
            $jamIstirahat = now()->format('H:i:s'); // Server time, tidak bisa dimanipulasi client
            
            // Update kehadiran
            $kehadiran->update([
                'jam_istirahat' => $jamIstirahat,
                'foto_istirahat' => $fotoPath,
                'lokasi_istirahat' => $lokasiAbsensi->nama_lokasi,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi istirahat berhasil dicatat',
                'data' => [
                    'jam_istirahat' => $jamIstirahat,
                    'lokasi' => $lokasiAbsensi->nama_lokasi,
                    'on_radius' => $onRadius,
                    'distance' => round($distance, 2),
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat absensi istirahat: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Return from break
     */
    public function returnFromBreak(Request $request)
    {
        $request->validate([
            'lokasi_absensi_id' => 'required|exists:lokasi_absensis,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        $today = now()->format('Y-m-d');
        
        // Get today's attendance
        $kehadiran = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        if (!$kehadiran || !$kehadiran->jam_istirahat) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absensi istirahat hari ini',
            ], 400);
        }
        
        if ($kehadiran->jam_kembali) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi kembali bekerja hari ini',
            ], 400);
        }
        
        // Get location details
        $lokasiAbsensi = LokasiAbsensi::find($request->lokasi_absensi_id);
        
        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $lokasiAbsensi->latitude,
            $lokasiAbsensi->longitude
        );
        
        $onRadius = $distance <= $lokasiAbsensi->radius;
        
        try {
            DB::beginTransaction();
            
            // Upload photo
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = 'absensi_kembali_' . $user->karyawan->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('absensi', $fileName, 'public');
            }
            
            $jamKembali = now()->format('H:i:s'); // Server time, tidak bisa dimanipulasi client
            
            // Calculate break duration
            $jamIstirahat = Carbon::parse($kehadiran->jam_istirahat);
            $jamKembaliCarbon = Carbon::parse($jamKembali);
            $durasiIstirahat = (int) $jamIstirahat->diffInMinutes($jamKembaliCarbon);
            
            // Update kehadiran
            $kehadiran->update([
                'jam_kembali' => $jamKembali,
                'foto_kembali' => $fotoPath,
                'lokasi_kembali' => $lokasiAbsensi->nama_lokasi,
                'durasi_istirahat' => $durasiIstirahat,
            ]);
            
            DB::commit();
            
            // Format duration
            $hours = floor($durasiIstirahat / 60);
            $minutes = $durasiIstirahat % 60;
            $durasiFormatted = "{$hours} jam {$minutes} menit";
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi kembali bekerja berhasil dicatat',
                'data' => [
                    'jam_kembali' => $jamKembali,
                    'lokasi' => $lokasiAbsensi->nama_lokasi,
                    'durasi_istirahat' => $durasiFormatted,
                    'on_radius' => $onRadius,
                    'distance' => round($distance, 2),
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat absensi kembali bekerja: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check in attendance
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'lokasi_absensi_id' => 'required|exists:lokasi_absensis,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        $today = now()->format('Y-m-d');
        
        // Check if already checked in today
        $existingKehadiran = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        if ($existingKehadiran && $existingKehadiran->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi masuk hari ini',
            ], 400);
        }
        
        // Get location details
        $lokasiAbsensi = LokasiAbsensi::find($request->lokasi_absensi_id);
        
        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $lokasiAbsensi->latitude,
            $lokasiAbsensi->longitude
        );
        
        $onRadius = $distance <= $lokasiAbsensi->radius;
        
        try {
            DB::beginTransaction();
            
            // Upload photo
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = 'absensi_masuk_' . $user->karyawan->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('absensi', $fileName, 'public');
            }
            
            // Get today's shift
            $jadwalShift = JadwalShift::with('shift')
                ->where('karyawan_id', $user->karyawan->id)
                ->where('tanggal', $today)
                ->first();
            
            $jamMasuk = now()->format('H:i:s'); // Server time, tidak bisa dimanipulasi client
            $status = 'hadir';
            
            // Check if late
            if ($jadwalShift && $jadwalShift->shift && $jamMasuk > $jadwalShift->shift->jam_mulai) {
                $status = 'terlambat';
            }
            
            // Create or update kehadiran
            $kehadiran = Kehadiran::updateOrCreate(
                [
                    'karyawan_id' => $user->karyawan->id,
                    'tanggal' => $today,
                ],
                [
                    'perusahaan_id' => $user->perusahaan_id,
                    'project_id' => $user->karyawan->project_id,
                    'shift_id' => $jadwalShift && $jadwalShift->shift ? $jadwalShift->shift->id : null,
                    'jam_masuk' => $jamMasuk,
                    'foto_masuk' => $fotoPath,
                    'lokasi_masuk' => $lokasiAbsensi->nama_lokasi,
                    'status' => $status,
                    'on_radius' => $onRadius,
                    'on_radius_masuk' => $onRadius,
                    'jarak_masuk' => (int) round($distance),
                    'latitude_masuk' => $request->latitude,
                    'longitude_masuk' => $request->longitude,
                    'map_absen_masuk' => "https://www.google.com/maps?q={$request->latitude},{$request->longitude}",
                    'sumber_data' => 'mobile',
                ]
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi masuk berhasil dicatat',
                'data' => [
                    'jam_masuk' => $jamMasuk,
                    'status' => $status,
                    'lokasi' => $lokasiAbsensi->nama_lokasi,
                    'on_radius' => $onRadius,
                    'distance' => round($distance, 2),
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat absensi: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Check out attendance
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'lokasi_absensi_id' => 'required|exists:lokasi_absensis,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $user = $request->user();
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        $today = now()->format('Y-m-d');
        
        // Get today's attendance
        $kehadiran = Kehadiran::where('karyawan_id', $user->karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        if (!$kehadiran || !$kehadiran->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absensi masuk hari ini',
            ], 400);
        }
        
        if ($kehadiran->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi keluar hari ini',
            ], 400);
        }
        
        // Get location details
        $lokasiAbsensi = LokasiAbsensi::find($request->lokasi_absensi_id);
        
        // Calculate distance
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $lokasiAbsensi->latitude,
            $lokasiAbsensi->longitude
        );
        
        $onRadius = $distance <= $lokasiAbsensi->radius;
        
        try {
            DB::beginTransaction();
            
            // Upload photo
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $fileName = 'absensi_keluar_' . $user->karyawan->id . '_' . now()->format('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
                $fotoPath = $file->storeAs('absensi', $fileName, 'public');
            }
            
            // Get today's shift
            $jadwalShift = JadwalShift::with('shift')
                ->where('karyawan_id', $user->karyawan->id)
                ->where('tanggal', $today)
                ->first();
            
            $jamKeluar = now()->format('H:i:s'); // Server time, tidak bisa dimanipulasi client
            
            // Calculate work duration
            $jamMasuk = Carbon::parse($kehadiran->jam_masuk);
            $jamKeluarCarbon = Carbon::parse($jamKeluar);
            $durasiKerja = (int) $jamMasuk->diffInMinutes($jamKeluarCarbon);
            
            // Determine status
            $status = $kehadiran->status; // Keep existing status (hadir/terlambat)
            
            // Check if leaving early
            if ($jadwalShift && $jadwalShift->shift && $jamKeluar < $jadwalShift->shift->jam_selesai) {
                // If already late and leaving early, keep as 'terlambat' (most severe)
                // If on time but leaving early, change to 'pulang_cepat'
                if ($status !== 'terlambat') {
                    $status = 'pulang_cepat';
                }
                // Note: If already 'terlambat', we keep it as 'terlambat' (don't change to combined status)
            }
            
            // Update kehadiran
            $kehadiran->update([
                'jam_keluar' => $jamKeluar,
                'foto_keluar' => $fotoPath,
                'lokasi_keluar' => $lokasiAbsensi->nama_lokasi,
                'status' => $status,
                'durasi_kerja' => $durasiKerja,
                'on_radius_keluar' => $onRadius,
                'jarak_keluar' => (int) round($distance),
                'latitude_keluar' => $request->latitude,
                'longitude_keluar' => $request->longitude,
                'map_absen_keluar' => "https://www.google.com/maps?q={$request->latitude},{$request->longitude}",
            ]);
            
            DB::commit();
            
            // Format duration
            $hours = floor($durasiKerja / 60);
            $minutes = $durasiKerja % 60;
            $durasiFormatted = "{$hours} jam {$minutes} menit";
            
            return response()->json([
                'success' => true,
                'message' => 'Absensi keluar berhasil dicatat',
                'data' => [
                    'jam_keluar' => $jamKeluar,
                    'status' => $status,
                    'lokasi' => $lokasiAbsensi->nama_lokasi,
                    'durasi_kerja' => $durasiFormatted,
                    'on_radius' => $onRadius,
                    'distance' => round($distance, 2),
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Delete uploaded file if exists
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat absensi: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Calculate distance between two coordinates (in meters)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

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

