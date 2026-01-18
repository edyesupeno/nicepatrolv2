<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PenerimaanBarang;
use App\Models\Perusahaan;

class PenerimaanBarangSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $perusahaan = Perusahaan::first();
        
        if (!$perusahaan) {
            $this->command->info('Tidak ada perusahaan ditemukan. Silakan jalankan seeder perusahaan terlebih dahulu.');
            return;
        }

        // Get first project and area for sample data
        $project = \App\Models\Project::first();
        $area = \App\Models\Area::first();

        $sampleData = [
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project?->id,
                'area_id' => $area?->id,
                'pos' => 'Rak A-01',
                'nomor_penerimaan' => 'PB202401190001',
                'nama_barang' => 'Paket Dokumen Kontrak A1',
                'kategori_barang' => 'Dokumen',
                'jumlah_barang' => 1,
                'satuan' => 'Pcs',
                'kondisi_barang' => 'Baik',
                'pengirim' => 'Client',
                'tujuan_departemen' => 'HRD',
                'tanggal_terima' => now()->subHours(2),
                'status' => 'Diterima',
                'petugas_penerima' => 'Security Officer',
                'keterangan' => 'Dokumen kontrak kerjasama tahun 2024',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project?->id,
                'area_id' => $area?->id,
                'pos' => 'Meja IT-02',
                'nomor_penerimaan' => 'PB202401190002',
                'nama_barang' => 'Laptop Dell Inspiron 15',
                'kategori_barang' => 'Elektronik',
                'jumlah_barang' => 1,
                'satuan' => 'Unit',
                'kondisi_barang' => 'Baik',
                'pengirim' => 'Kurir',
                'tujuan_departemen' => 'IT Department',
                'tanggal_terima' => now()->subHours(4),
                'status' => 'Diterima',
                'petugas_penerima' => 'Receptionist',
                'keterangan' => 'Laptop untuk karyawan baru',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => null, // No project assigned
                'area_id' => null, // No area assigned
                'pos' => 'Gudang B-15',
                'nomor_penerimaan' => 'PB202401190003',
                'nama_barang' => 'Kertas A4 80gsm',
                'kategori_barang' => 'Logistik',
                'jumlah_barang' => 10,
                'satuan' => 'Box',
                'kondisi_barang' => 'Segel Terbuka',
                'pengirim' => 'Kurir',
                'tujuan_departemen' => 'General Affairs',
                'tanggal_terima' => now()->subHours(6),
                'status' => 'Diterima',
                'petugas_penerima' => 'Admin GA',
                'keterangan' => 'Segel terbuka saat pengiriman, isi masih lengkap',
            ],
        ];

        foreach ($sampleData as $data) {
            PenerimaanBarang::create($data);
        }

        $this->command->info('Sample data penerimaan barang berhasil dibuat.');
    }
}