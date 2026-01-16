<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayrollSetting;
use App\Models\Perusahaan;

class PayrollSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all perusahaan
        $perusahaans = Perusahaan::all();

        foreach ($perusahaans as $perusahaan) {
            // Create or update default payroll settings based on current Indonesian regulations
            PayrollSetting::updateOrCreate(
                ['perusahaan_id' => $perusahaan->id],
                [
                    // BPJS Kesehatan (sesuai peraturan terbaru)
                    'bpjs_kesehatan_perusahaan' => 4.00, // 4%
                    'bpjs_kesehatan_karyawan' => 1.00,   // 1%
                    
                    // BPJS Ketenagakerjaan - JHT (Jaminan Hari Tua)
                    'bpjs_jht_perusahaan' => 3.70,  // 3.7%
                    'bpjs_jht_karyawan' => 2.00,    // 2%
                    
                    // BPJS Ketenagakerjaan - JP (Jaminan Pensiun)
                    'bpjs_jp_perusahaan' => 2.00,   // 2%
                    'bpjs_jp_karyawan' => 1.00,     // 1%
                    
                    // BPJS Ketenagakerjaan - JKK (Jaminan Kecelakaan Kerja)
                    // Biasanya 100% ditanggung perusahaan, rate tergantung tingkat risiko
                    'bpjs_jkk_perusahaan' => 0.24,  // 0.24% (risiko sangat rendah)
                    'bpjs_jkk_karyawan' => 0.00,    // 0% (default ditanggung perusahaan)
                    
                    // BPJS Ketenagakerjaan - JKM (Jaminan Kematian)
                    // Biasanya 100% ditanggung perusahaan
                    'bpjs_jkm_perusahaan' => 0.30,  // 0.3%
                    'bpjs_jkm_karyawan' => 0.00,    // 0% (default ditanggung perusahaan)
                ]
            );
        }
    }
}
