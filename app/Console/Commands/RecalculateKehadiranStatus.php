<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kehadiran;
use Carbon\Carbon;

class RecalculateKehadiranStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kehadiran:recalculate-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate kehadiran status (terlambat, pulang_cepat) based on shift time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation of kehadiran status...');
        
        // Get all kehadiran with status hadir and has shift
        $kehadirans = Kehadiran::with('shift')
            ->where('status', 'hadir')
            ->whereNotNull('shift_id')
            ->whereNotNull('jam_masuk')
            ->whereNotNull('jam_keluar')
            ->get();
        
        $this->info("Found {$kehadirans->count()} kehadiran records to process");
        
        $updated = 0;
        $bar = $this->output->createProgressBar($kehadirans->count());
        $bar->start();
        
        foreach ($kehadirans as $kehadiran) {
            if (!$kehadiran->shift) {
                $bar->advance();
                continue;
            }
            
            $newStatus = $this->calculateStatus(
                $kehadiran->jam_masuk,
                $kehadiran->jam_keluar,
                $kehadiran->shift->jam_mulai,
                $kehadiran->shift->jam_selesai
            );
            
            if ($newStatus !== 'hadir') {
                $kehadiran->status = $newStatus;
                $kehadiran->save();
                $updated++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Recalculation completed! Updated {$updated} records.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Calculate attendance status based on actual time vs shift time
     * 
     * @param string $jamMasuk Actual check-in time (datetime or HH:MM)
     * @param string $jamKeluar Actual check-out time (datetime or HH:MM)
     * @param string $shiftMulai Shift start time (HH:MM:SS or HH:MM)
     * @param string $shiftSelesai Shift end time (HH:MM:SS or HH:MM)
     * @return string Status: hadir, terlambat, pulang_cepat
     */
    private function calculateStatus($jamMasuk, $jamKeluar, $shiftMulai, $shiftSelesai)
    {
        try {
            // Parse times - handle both datetime and time-only formats
            if (strlen($jamMasuk) > 8) {
                // Datetime format: 2026-01-15 07:00:00
                $masuk = Carbon::parse($jamMasuk);
            } else {
                // Time only format: 07:00 or 07:00:00
                $masuk = Carbon::createFromFormat('H:i', substr($jamMasuk, 0, 5));
            }
            
            if ($jamKeluar) {
                if (strlen($jamKeluar) > 8) {
                    $keluar = Carbon::parse($jamKeluar);
                } else {
                    $keluar = Carbon::createFromFormat('H:i', substr($jamKeluar, 0, 5));
                }
            } else {
                $keluar = null;
            }
            
            $shiftStart = Carbon::createFromFormat('H:i', substr($shiftMulai, 0, 5));
            $shiftEnd = Carbon::createFromFormat('H:i', substr($shiftSelesai, 0, 5));
            
            // Toleransi: 15 menit untuk terlambat, 15 menit untuk pulang cepat
            $toleransiTerlambat = 15; // minutes
            $toleransiPulangCepat = 15; // minutes
            
            $isTerlambat = false;
            $isPulangCepat = false;
            
            // Extract time only for comparison
            $masukTime = Carbon::createFromFormat('H:i', $masuk->format('H:i'));
            $keluarTime = $keluar ? Carbon::createFromFormat('H:i', $keluar->format('H:i')) : null;
            
            // Check terlambat: jam masuk > shift mulai + toleransi
            $batasTerlambat = $shiftStart->copy()->addMinutes($toleransiTerlambat);
            if ($masukTime->gt($batasTerlambat)) {
                $isTerlambat = true;
            }
            
            // Check pulang cepat: jam keluar < shift selesai - toleransi
            if ($keluarTime) {
                $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                
                // Handle overnight shift
                if ($shiftEnd->lt($shiftStart)) {
                    $keluarTime->addDay();
                    $shiftEnd->addDay();
                    $batasPulangCepat = $shiftEnd->copy()->subMinutes($toleransiPulangCepat);
                }
                
                if ($keluarTime->lt($batasPulangCepat)) {
                    $isPulangCepat = true;
                }
            }
            
            // Determine final status
            if ($isTerlambat && $isPulangCepat) {
                // Both violations occurred
                return 'terlambat_pulang_cepat';
            } elseif ($isTerlambat) {
                return 'terlambat';
            } elseif ($isPulangCepat) {
                return 'pulang_cepat';
            } else {
                return 'hadir';
            }
            
        } catch (\Exception $e) {
            // If calculation fails, default to hadir
            return 'hadir';
        }
    }
}
