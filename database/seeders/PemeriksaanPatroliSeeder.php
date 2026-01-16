<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PemeriksaanPatroli;
use App\Models\PertanyaanPemeriksaan;
use App\Models\Perusahaan;

class PemeriksaanPatroliSeeder extends Seeder
{
    public function run(): void
    {
        $perusahaan = Perusahaan::first();
        
        if (!$perusahaan) {
            $this->command->warn('Tidak ada perusahaan. Jalankan seeder perusahaan terlebih dahulu.');
            return;
        }

        // Pemeriksaan 1: Peralatan Komunikasi
        $pemeriksaan1 = PemeriksaanPatroli::create([
            'perusahaan_id' => $perusahaan->id,
            'nama' => 'Pemeriksaan Peralatan Komunikasi',
            'deskripsi' => 'Cek kondisi radio, walkie-talkie, dan peralatan komunikasi',
            'frekuensi' => 'harian',
            'pemeriksaan_terakhir' => now()->subDays(1),
            'is_active' => true,
        ]);

        $pertanyaans1 = [
            [
                'pertanyaan' => 'Kondisi baterai radio HT',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Baik', 'Perlu Charge', 'Rusak'],
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Sinyal komunikasi berfungsi dengan baik?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Ya', 'Tidak'],
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Catatan kerusakan atau masalah',
                'tipe_jawaban' => 'text',
                'opsi_jawaban' => null,
                'urutan' => 3,
            ],
        ];

        foreach ($pertanyaans1 as $p) {
            PertanyaanPemeriksaan::create(array_merge($p, [
                'pemeriksaan_patroli_id' => $pemeriksaan1->id,
                'is_required' => true,
            ]));
        }

        // Pemeriksaan 2: Sistem Keamanan
        $pemeriksaan2 = PemeriksaanPatroli::create([
            'perusahaan_id' => $perusahaan->id,
            'nama' => 'Pemeriksaan Sistem Keamanan',
            'deskripsi' => 'Verifikasi CCTV, alarm, dan sistem keamanan lainnya',
            'frekuensi' => 'mingguan',
            'pemeriksaan_terakhir' => now()->subDays(7),
            'is_active' => true,
        ]);

        $pertanyaans2 = [
            [
                'pertanyaan' => 'Semua CCTV berfungsi normal?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Ya', 'Tidak'],
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Sistem alarm aktif dan responsif?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Ya', 'Tidak'],
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Deskripsi masalah yang ditemukan',
                'tipe_jawaban' => 'text',
                'opsi_jawaban' => null,
                'urutan' => 3,
            ],
        ];

        foreach ($pertanyaans2 as $p) {
            PertanyaanPemeriksaan::create(array_merge($p, [
                'pemeriksaan_patroli_id' => $pemeriksaan2->id,
                'is_required' => true,
            ]));
        }

        $this->command->info('Pemeriksaan patroli berhasil di-seed!');
    }
}
