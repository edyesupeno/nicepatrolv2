<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventarisPatroli;
use App\Models\Perusahaan;

class InventarisPatroliSeeder extends Seeder
{
    public function run(): void
    {
        $perusahaan = Perusahaan::first();
        
        if (!$perusahaan) {
            $this->command->warn('Tidak ada perusahaan. Jalankan seeder perusahaan terlebih dahulu.');
            return;
        }

        $inventaris = [
            [
                'nama' => 'Radio HT Motorola GP328',
                'kategori' => 'Komunikasi',
                'catatan' => 'Radio komunikasi untuk koordinasi tim patroli',
            ],
            [
                'nama' => 'Senter LED Rechargeable',
                'kategori' => 'Penerangan',
                'catatan' => 'Senter LED dengan baterai rechargeable 5000mAh',
            ],
            [
                'nama' => 'Tongkat Keamanan',
                'kategori' => 'Keamanan',
                'catatan' => 'Tongkat keamanan standar untuk petugas patroli',
            ],
            [
                'nama' => 'Buku Laporan Patroli',
                'kategori' => 'Alat Tulis',
                'catatan' => 'Buku untuk mencatat hasil patroli harian',
            ],
            [
                'nama' => 'Sepeda Motor Patrol',
                'kategori' => 'Transportasi',
                'catatan' => 'Motor untuk patroli area luas',
            ],
            [
                'nama' => 'Body Camera',
                'kategori' => 'Keamanan',
                'catatan' => 'Kamera untuk merekam aktivitas patroli',
            ],
            [
                'nama' => 'GPS Tracker',
                'kategori' => 'Komunikasi',
                'catatan' => 'Alat pelacak lokasi real-time',
            ],
            [
                'nama' => 'Jas Hujan',
                'kategori' => 'Lainnya',
                'catatan' => 'Perlengkapan untuk patroli saat hujan',
            ],
        ];

        foreach ($inventaris as $item) {
            InventarisPatroli::create([
                'perusahaan_id' => $perusahaan->id,
                'nama' => $item['nama'],
                'kategori' => $item['kategori'],
                'catatan' => $item['catatan'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Inventaris patroli berhasil di-seed!');
    }
}
