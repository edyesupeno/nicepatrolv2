<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payroll;
use App\Models\Kehadiran;
use Carbon\Carbon;

class RecalculatePayroll extends Command
{
    protected $signature = 'payroll:recalculate {periode?}';
    protected $description = 'Recalculate payroll untuk periode tertentu';

    public function handle()
    {
        $periode = $this->argument('periode') ?? now()->format('Y-m');
        
        $this->info("Recalculating payroll untuk periode: {$periode}");
        
        $payrolls = Payroll::where('periode', $periode)->get();
        
        if ($payrolls->isEmpty()) {
            $this->error("Tidak ada payroll untuk periode {$periode}");
            return 1;
        }
        
        // Get payroll settings
        $payrollSetting = \App\Models\PayrollSetting::first();
        
        if (!$payrollSetting) {
            $this->error("PayrollSetting belum dikonfigurasi!");
            return 1;
        }
        
        $bar = $this->output->createProgressBar($payrolls->count());
        $bar->start();
        
        foreach ($payrolls as $payroll) {
            // Recalculate kehadiran
            $periodeDate = Carbon::createFromFormat('Y-m', $periode);
            $startDate = $periodeDate->copy()->startOfMonth();
            $endDate = $periodeDate->copy()->endOfMonth();
            
            $kehadirans = Kehadiran::where('karyawan_id', $payroll->karyawan_id)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->get();
            
            // Count kehadiran - include all statuses that count as "masuk"
            // Also count if jam_masuk is not null (for records without status_kehadiran)
            $hariMasuk = $kehadirans->filter(function($k) {
                return in_array($k->status_kehadiran, ['Hadir', 'Terlambat', 'Pulang Cepat']) 
                    || (!empty($k->jam_masuk) && empty($k->status_kehadiran));
            })->count();
            
            $hariAlpha = $kehadirans->where('status_kehadiran', 'Alpha')->count();
            $hariSakit = $kehadirans->where('status_kehadiran', 'Sakit')->count();
            $hariIzin = $kehadirans->where('status_kehadiran', 'Izin')->count();
            $hariCuti = $kehadirans->where('status_kehadiran', 'Cuti')->count();
            $hariLembur = $kehadirans->where('lembur', true)->count();
            
            // Recalculate BPJS
            $gajiPokok = $payroll->gaji_pokok;
            
            // Get PTKP from karyawan
            $karyawan = $payroll->karyawan;
            $ptkpStatus = $karyawan->ptkp_status;
            $ptkpValue = $karyawan->ptkp_value;
            
            // BPJS Kesehatan (perusahaan)
            $bpjsKesehatan = ($gajiPokok * $payrollSetting->bpjs_kesehatan_perusahaan) / 100;
            
            // BPJS Ketenagakerjaan (perusahaan) = JHT + JP + JKK + JKM
            $bpjsJht = ($gajiPokok * $payrollSetting->bpjs_jht_perusahaan) / 100;
            $bpjsJp = ($gajiPokok * $payrollSetting->bpjs_jp_perusahaan) / 100;
            $bpjsJkk = ($gajiPokok * $payrollSetting->bpjs_jkk_perusahaan) / 100;
            $bpjsJkm = ($gajiPokok * $payrollSetting->bpjs_jkm_perusahaan) / 100;
            $bpjsKetenagakerjaan = $bpjsJht + $bpjsJp + $bpjsJkk + $bpjsJkm;
            
            // Recalculate tunjangan based on new hari_masuk
            $totalTunjangan = 0;
            $tunjanganDetail = $payroll->tunjangan_detail ?? [];
            
            foreach ($tunjanganDetail as &$tunjangan) {
                if ($tunjangan['tipe'] == 'Per Hari Masuk') {
                    $tunjangan['nilai_hitung'] = $tunjangan['nilai_dasar'] * $hariMasuk;
                } elseif ($tunjangan['tipe'] == 'Lembur Per Hari') {
                    $tunjangan['nilai_hitung'] = $tunjangan['nilai_dasar'] * $hariLembur;
                }
                $totalTunjangan += $tunjangan['nilai_hitung'];
            }
            
            // Recalculate potongan (add BPJS karyawan if not exists)
            $potonganDetail = $payroll->potongan_detail ?? [];
            $totalPotongan = 0;
            
            // Remove old BPJS potongan
            $potonganDetail = array_filter($potonganDetail, function($p) {
                return !str_contains($p['nama'], 'BPJS');
            });
            
            // Recalculate existing potongan
            foreach ($potonganDetail as &$potongan) {
                if ($potongan['tipe'] == 'Per Hari Masuk') {
                    $potongan['nilai_hitung'] = $potongan['nilai_dasar'] * $hariMasuk;
                } elseif ($potongan['tipe'] == 'Lembur Per Hari') {
                    $potongan['nilai_hitung'] = $potongan['nilai_dasar'] * $hariLembur;
                }
                $totalPotongan += $potongan['nilai_hitung'];
            }
            
            // Add BPJS potongan karyawan
            if ($payrollSetting->bpjs_kesehatan_karyawan > 0) {
                $potonganBpjsKes = ($gajiPokok * $payrollSetting->bpjs_kesehatan_karyawan) / 100;
                $potonganDetail[] = [
                    'kode' => 'BPJS_KES_KARYAWAN',
                    'nama' => 'Potongan BPJS Kesehatan',
                    'tipe' => 'Persentase',
                    'nilai_dasar' => $payrollSetting->bpjs_kesehatan_karyawan,
                    'nilai_hitung' => $potonganBpjsKes,
                ];
                $totalPotongan += $potonganBpjsKes;
            }
            
            $potonganBpjsJht = ($gajiPokok * $payrollSetting->bpjs_jht_karyawan) / 100;
            $potonganBpjsJp = ($gajiPokok * $payrollSetting->bpjs_jp_karyawan) / 100;
            $potonganBpjsKer = $potonganBpjsJht + $potonganBpjsJp;
            
            if ($potonganBpjsKer > 0) {
                $potonganDetail[] = [
                    'kode' => 'BPJS_TK_KARYAWAN',
                    'nama' => 'Potongan BPJS Ketenagakerjaan (JHT + JP)',
                    'tipe' => 'Persentase',
                    'nilai_dasar' => $payrollSetting->bpjs_jht_karyawan + $payrollSetting->bpjs_jp_karyawan,
                    'nilai_hitung' => $potonganBpjsKer,
                ];
                $totalPotongan += $potonganBpjsKer;
            }
            
            // Recalculate PPh 21
            $gajiKotorSebelumPajak = $gajiPokok + $totalTunjangan + $bpjsKesehatan + $bpjsKetenagakerjaan - $totalPotongan;
            $pajakPph21 = 0;
            
            if ($payrollSetting->pph21_bracket1_rate > 0) {
                $pajakPph21 = ($gajiKotorSebelumPajak * $payrollSetting->pph21_bracket1_rate) / 100;
            }
            
            // Recalculate totals
            $gajiBruto = $gajiPokok + $totalTunjangan + $bpjsKesehatan + $bpjsKetenagakerjaan;
            $gajiNetto = $gajiBruto - $totalPotongan - $pajakPph21;
            
            // Update payroll
            $payroll->update([
                'hari_masuk' => $hariMasuk,
                'hari_alpha' => $hariAlpha,
                'hari_sakit' => $hariSakit,
                'hari_izin' => $hariIzin,
                'hari_cuti' => $hariCuti,
                'hari_lembur' => $hariLembur,
                'ptkp_status' => $ptkpStatus,
                'ptkp_value' => $ptkpValue,
                'tunjangan_detail' => $tunjanganDetail,
                'total_tunjangan' => $totalTunjangan,
                'bpjs_kesehatan' => $bpjsKesehatan,
                'bpjs_ketenagakerjaan' => $bpjsKetenagakerjaan,
                'potongan_detail' => $potonganDetail,
                'total_potongan' => $totalPotongan,
                'pajak_pph21' => $pajakPph21,
                'gaji_bruto' => $gajiBruto,
                'gaji_netto' => $gajiNetto,
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Berhasil recalculate {$payrolls->count()} payroll");
        
        return 0;
    }
}
