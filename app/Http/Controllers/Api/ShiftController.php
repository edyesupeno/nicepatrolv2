<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalShift;
use App\Models\Karyawan;
use Carbon\Carbon;

class ShiftController extends Controller
{
    /**
     * Get shift schedule for authenticated user
     * Query params:
     * - date: YYYY-MM-DD (optional, default: today)
     * - week: get 7 days from date (optional)
     * - month: YYYY-MM (optional, get all shifts for the month)
     */
    public function mySchedule(Request $request)
    {
        $user = $request->user();
        
        // Get karyawan data
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        // Get date parameter
        $date = $request->input('date', now()->format('Y-m-d'));
        $startDate = Carbon::parse($date);
        
        // Determine query type
        if ($request->has('month')) {
            // Get all shifts for the month
            $month = $request->input('month'); // Format: YYYY-MM
            $startDate = Carbon::parse($month . '-01');
            $endDate = $startDate->copy()->endOfMonth();
        } elseif ($request->has('week') || $request->boolean('week')) {
            // Get 7 days from date
            $endDate = $startDate->copy()->addDays(6);
        } else {
            // Get single day
            $endDate = $startDate->copy();
        }
        
        // Get jadwal shift
        $jadwalShifts = JadwalShift::with('shift')
            ->where('karyawan_id', $karyawan->id)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('tanggal')
            ->get();
        
        // Format response
        $schedules = $jadwalShifts->map(function ($jadwal) {
            return [
                'id' => $jadwal->id,
                'tanggal' => $jadwal->tanggal->format('Y-m-d'),
                'tanggal_formatted' => $jadwal->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                'day_name' => $jadwal->tanggal->locale('id')->isoFormat('dddd'),
                'day_short' => $this->getDayShort($jadwal->tanggal->dayOfWeek),
                'day_number' => $jadwal->tanggal->day,
                'shift' => $jadwal->shift ? [
                    'id' => $jadwal->shift->id,
                    'kode_shift' => $jadwal->shift->kode_shift,
                    'nama_shift' => $jadwal->shift->nama_shift,
                    'jam_mulai' => $jadwal->shift->jam_mulai,
                    'jam_selesai' => $jadwal->shift->jam_selesai,
                    'jam_formatted' => $jadwal->shift->jam_mulai . ' - ' . $jadwal->shift->jam_selesai,
                    'durasi_istirahat' => $jadwal->shift->durasi_istirahat,
                    'warna' => $jadwal->shift->warna,
                ] : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => [
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama_lengkap,
                    'nik' => $karyawan->nik_karyawan,
                ],
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'start_date_formatted' => $startDate->locale('id')->isoFormat('D MMMM YYYY'),
                    'end_date_formatted' => $endDate->locale('id')->isoFormat('D MMMM YYYY'),
                ],
                'schedules' => $schedules,
                'total' => $schedules->count(),
            ],
        ]);
    }
    
    /**
     * Get today's shift for authenticated user
     */
    public function todayShift(Request $request)
    {
        $user = $request->user();
        
        // Get karyawan data
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        // Get today's shift
        $today = now()->format('Y-m-d');
        $jadwalShift = JadwalShift::with('shift')
            ->where('karyawan_id', $karyawan->id)
            ->where('tanggal', $today)
            ->first();
        
        if (!$jadwalShift) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada jadwal shift hari ini',
                'data' => null,
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jadwalShift->id,
                'tanggal' => $jadwalShift->tanggal->format('Y-m-d'),
                'tanggal_formatted' => $jadwalShift->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY'),
                'shift' => $jadwalShift->shift ? [
                    'id' => $jadwalShift->shift->id,
                    'kode_shift' => $jadwalShift->shift->kode_shift,
                    'nama_shift' => $jadwalShift->shift->nama_shift,
                    'jam_mulai' => $jadwalShift->shift->jam_mulai,
                    'jam_selesai' => $jadwalShift->shift->jam_selesai,
                    'jam_formatted' => $jadwalShift->shift->jam_mulai . ' - ' . $jadwalShift->shift->jam_selesai,
                    'durasi_istirahat' => $jadwalShift->shift->durasi_istirahat,
                    'toleransi_keterlambatan' => $jadwalShift->shift->toleransi_keterlambatan,
                    'warna' => $jadwalShift->shift->warna,
                ] : null,
            ],
        ]);
    }
    
    /**
     * Helper function to get short day name
     */
    private function getDayShort($dayOfWeek)
    {
        $days = [
            0 => 'MIN', // Sunday
            1 => 'SEN', // Monday
            2 => 'SEL', // Tuesday
            3 => 'RAB', // Wednesday
            4 => 'KAM', // Thursday
            5 => 'JUM', // Friday
            6 => 'SAB', // Saturday
        ];
        
        return $days[$dayOfWeek] ?? '';
    }
}
