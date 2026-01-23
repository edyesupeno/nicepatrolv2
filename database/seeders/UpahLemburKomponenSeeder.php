<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KomponenPayroll;
use App\Models\Perusahaan;

class UpahLemburKomponenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all perusahaan
        $perusahaans = Perusahaan::all();
        
        foreach ($perusahaans as $perusahaan) {
            // Check if Upah Lembur component already exists for this perusahaan
            $existingComponent = KomponenPayroll::withoutGlobalScope('perusahaan')
                ->where('perusahaan_id', $perusahaan->id)
                ->where('kode', 'UPAH_LEMBUR')
                ->first();
            
            if (!$existingComponent) {
                KomponenPayroll::create([
                    'perusahaan_id' => $perusahaan->id,
                    'project_id' => null, // Global untuk semua project
                    'nama_komponen' => 'Upah Lembur',
                    'kode' => 'UPAH_LEMBUR',
                    'jenis' => 'Tunjangan',
                    'kategori' => 'Variable',
                    'tipe_perhitungan' => 'Otomatis',
                    'nilai' => 0, // Nilai akan dihitung otomatis
                    'nilai_maksimal' => null,
                    'deskripsi' => 'Upah lembur dihitung otomatis berdasarkan data lembur yang disetujui',
                    'kena_pajak' => true,
                    'boleh_edit' => false, // Tidak boleh diedit karena dihitung otomatis
                    'aktif' => true,
                ]);
                
                $this->command->info("Created Upah Lembur component for perusahaan: {$perusahaan->nama}");
            } else {
                $this->command->info("Upah Lembur component already exists for perusahaan: {$perusahaan->nama}");
            }
        }
    }
}