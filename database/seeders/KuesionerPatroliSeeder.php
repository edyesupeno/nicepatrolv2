<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KuesionerPatroli;
use App\Models\PertanyaanKuesioner;
use App\Models\Perusahaan;

class KuesionerPatroliSeeder extends Seeder
{
    public function run(): void
    {
        $perusahaan = Perusahaan::first();
        
        if (!$perusahaan) {
            $this->command->warn('Tidak ada perusahaan. Jalankan seeder perusahaan terlebih dahulu.');
            return;
        }

        // Kuesioner 1: Keamanan Harian
        $kuesioner1 = KuesionerPatroli::create([
            'perusahaan_id' => $perusahaan->id,
            'judul' => 'Kuesioner Keamanan Harian',
            'deskripsi' => 'Checklist keamanan yang harus diisi setiap shift',
            'is_active' => true,
        ]);

        $pertanyaans1 = [
            [
                'pertanyaan' => 'Apakah semua pintu masuk sudah terkunci dengan baik?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Baik', 'Tidak Baik'],
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Kondisi penerangan di area patroli',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Baik', 'Tidak Baik'],
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Apakah ada aktivitas mencurigakan yang terlihat?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Baik', 'Tidak Baik'],
                'urutan' => 3,
            ],
            [
                'pertanyaan' => 'Catatan khusus atau temuan lainnya',
                'tipe_jawaban' => 'text',
                'opsi_jawaban' => null,
                'urutan' => 4,
            ],
        ];

        foreach ($pertanyaans1 as $p) {
            PertanyaanKuesioner::create(array_merge($p, [
                'kuesioner_patroli_id' => $kuesioner1->id,
                'is_required' => true,
            ]));
        }

        // Kuesioner 2: Kondisi Fasilitas
        $kuesioner2 = KuesionerPatroli::create([
            'perusahaan_id' => $perusahaan->id,
            'judul' => 'Kuesioner Kondisi Fasilitas',
            'deskripsi' => 'Penilaian kondisi fasilitas dan infrastruktur',
            'is_active' => true,
        ]);

        $pertanyaans2 = [
            [
                'pertanyaan' => 'Kondisi CCTV berfungsi dengan baik?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Ya', 'Tidak'],
                'urutan' => 1,
            ],
            [
                'pertanyaan' => 'Sistem alarm dalam kondisi aktif?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Ya', 'Tidak'],
                'urutan' => 2,
            ],
            [
                'pertanyaan' => 'Deskripsi kerusakan atau masalah yang ditemukan',
                'tipe_jawaban' => 'text',
                'opsi_jawaban' => null,
                'urutan' => 3,
            ],
        ];

        foreach ($pertanyaans2 as $p) {
            PertanyaanKuesioner::create(array_merge($p, [
                'kuesioner_patroli_id' => $kuesioner2->id,
                'is_required' => true,
            ]));
        }

        $this->command->info('Kuesioner patroli berhasil di-seed!');
    }
}
