<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsetKawasan;
use App\Models\Perusahaan;

class AsetKawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perusahaans = Perusahaan::all();

        if ($perusahaans->isEmpty()) {
            $this->command->warn('Tidak ada perusahaan. Jalankan seeder perusahaan terlebih dahulu.');
            return;
        }

        $asetData = [
            [
                'nama' => 'CCTV Indoor Lobby',
                'kategori' => 'Elektronik',
                'merk' => 'Hikvision',
                'model' => 'DS-2CE16D0T',
                'serial_number' => 'SN123456789',
            ],
            [
                'nama' => 'CCTV Outdoor Parkir',
                'kategori' => 'Elektronik',
                'merk' => 'Hikvision',
                'model' => 'DS-2CE16D0T-IR',
                'serial_number' => 'SN987654321',
            ],
            [
                'nama' => 'Fire Extinguisher APAR 3kg',
                'kategori' => 'Keselamatan',
                'merk' => 'Yamato',
                'model' => 'YA-30X',
                'serial_number' => 'FE2024001',
            ],
            [
                'nama' => 'Hydrant Box Indoor',
                'kategori' => 'Keselamatan',
                'merk' => 'Hooseki',
                'model' => 'HB-01',
                'serial_number' => 'HY2024001',
            ],
            [
                'nama' => 'Emergency Light LED',
                'kategori' => 'Elektronik',
                'merk' => 'Philips',
                'model' => 'EL-200',
                'serial_number' => 'EL2024001',
            ],
            [
                'nama' => 'Access Control Door',
                'kategori' => 'Keamanan',
                'merk' => 'ZKTeco',
                'model' => 'F18',
                'serial_number' => 'AC2024001',
            ],
        ];

        foreach ($perusahaans as $perusahaan) {
            foreach ($asetData as $aset) {
                AsetKawasan::create([
                    'perusahaan_id' => $perusahaan->id,
                    'kode_aset' => 'AST-' . strtoupper(uniqid()),
                    'nama' => $aset['nama'],
                    'kategori' => $aset['kategori'],
                    'merk' => $aset['merk'],
                    'model' => $aset['model'],
                    'serial_number' => $aset['serial_number'],
                    'deskripsi' => 'Aset untuk keperluan patrol dan monitoring',
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Aset Kawasan seeder berhasil dijalankan!');
    }
}
