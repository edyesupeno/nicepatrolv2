<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MutasiAset;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Karyawan;
use App\Models\Project;
use App\Models\User;

class MutasiAsetSeeder extends Seeder
{
    public function run(): void
    {
        // Get sample data
        $dataAsets = DataAset::limit(3)->get();
        $asetKendaraans = AsetKendaraan::limit(2)->get();
        $karyawans = Karyawan::limit(5)->get();
        $projects = Project::limit(4)->get();
        $users = User::where('role', '!=', 'superadmin')->limit(3)->get();

        if ($dataAsets->isEmpty() || $karyawans->isEmpty() || $projects->count() < 2) {
            $this->command->warn('Insufficient data for MutasiAset seeder. Please run DataAsetSeeder, KaryawanSeeder, and ProjectSeeder first.');
            return;
        }

        // Clear existing data first
        MutasiAset::truncate();

        $mutasiData = [
            // Data Aset Mutations
            [
                'perusahaan_id' => $dataAsets->first()->perusahaan_id,
                'tanggal_mutasi' => now()->subDays(10),
                'asset_type' => 'data_aset',
                'asset_id' => $dataAsets->first()->id,
                'karyawan_id' => $karyawans->first()->id,
                'project_asal_id' => $projects->first()->id,
                'project_tujuan_id' => $projects->skip(1)->first()->id,
                'alasan_mutasi' => 'Kebutuhan operasional project baru',
                'keterangan' => 'Aset diperlukan untuk mendukung operasional di project tujuan',
                'status' => 'selesai',
                'disetujui_oleh' => $users->first()->id ?? null,
                'tanggal_persetujuan' => now()->subDays(8),
                'catatan_persetujuan' => 'Disetujui untuk mendukung operasional'
            ],
            [
                'perusahaan_id' => $dataAsets->skip(1)->first()->perusahaan_id ?? $dataAsets->first()->perusahaan_id,
                'tanggal_mutasi' => now()->subDays(5),
                'asset_type' => 'data_aset',
                'asset_id' => $dataAsets->skip(1)->first()->id ?? $dataAsets->first()->id,
                'karyawan_id' => $karyawans->skip(1)->first()->id ?? $karyawans->first()->id,
                'project_asal_id' => $projects->skip(1)->first()->id,
                'project_tujuan_id' => $projects->skip(2)->first()->id ?? $projects->first()->id,
                'alasan_mutasi' => 'Reorganisasi aset perusahaan',
                'keterangan' => 'Penyesuaian distribusi aset antar project',
                'status' => 'disetujui',
                'disetujui_oleh' => $users->skip(1)->first()->id ?? $users->first()->id,
                'tanggal_persetujuan' => now()->subDays(3),
                'catatan_persetujuan' => 'Disetujui dengan catatan untuk segera dipindahkan'
            ],
            [
                'perusahaan_id' => $dataAsets->first()->perusahaan_id,
                'tanggal_mutasi' => now()->subDays(2),
                'asset_type' => 'data_aset',
                'asset_id' => $dataAsets->skip(2)->first()->id ?? $dataAsets->first()->id,
                'karyawan_id' => $karyawans->skip(2)->first()->id ?? $karyawans->first()->id,
                'project_asal_id' => $projects->first()->id,
                'project_tujuan_id' => $projects->skip(3)->first()->id ?? $projects->skip(1)->first()->id,
                'alasan_mutasi' => 'Permintaan dari project manager',
                'keterangan' => null,
                'status' => 'pending'
            ]
        ];

        // Add vehicle mutations if available
        if ($asetKendaraans->isNotEmpty()) {
            $mutasiData[] = [
                'perusahaan_id' => $asetKendaraans->first()->perusahaan_id,
                'tanggal_mutasi' => now()->subDays(7),
                'asset_type' => 'aset_kendaraan',
                'asset_id' => $asetKendaraans->first()->id,
                'karyawan_id' => $karyawans->skip(3)->first()->id ?? $karyawans->first()->id,
                'project_asal_id' => $projects->first()->id,
                'project_tujuan_id' => $projects->skip(1)->first()->id,
                'alasan_mutasi' => 'Kebutuhan transportasi project',
                'keterangan' => 'Kendaraan diperlukan untuk mobilitas tim project',
                'status' => 'selesai',
                'disetujui_oleh' => $users->first()->id ?? null,
                'tanggal_persetujuan' => now()->subDays(6),
                'catatan_persetujuan' => 'Disetujui untuk kebutuhan operasional'
            ];

            if ($asetKendaraans->count() > 1) {
                $mutasiData[] = [
                    'perusahaan_id' => $asetKendaraans->skip(1)->first()->perusahaan_id,
                    'tanggal_mutasi' => now()->subDays(1),
                    'asset_type' => 'aset_kendaraan',
                    'asset_id' => $asetKendaraans->skip(1)->first()->id,
                    'karyawan_id' => $karyawans->skip(4)->first()->id ?? $karyawans->first()->id,
                    'project_asal_id' => $projects->skip(1)->first()->id,
                    'project_tujuan_id' => $projects->skip(2)->first()->id ?? $projects->first()->id,
                    'alasan_mutasi' => 'Maintenance dan perawatan rutin',
                    'keterangan' => 'Kendaraan perlu dipindahkan untuk perawatan berkala',
                    'status' => 'ditolak',
                    'disetujui_oleh' => $users->skip(2)->first()->id ?? $users->first()->id,
                    'tanggal_persetujuan' => now(),
                    'catatan_persetujuan' => 'Ditolak karena kendaraan masih dibutuhkan di project asal'
                ];
            }
        }

        foreach ($mutasiData as $index => $data) {
            // Generate unique nomor_mutasi for each record
            $data['nomor_mutasi'] = 'MUT/' . now()->format('Ymd') . '/' . sprintf('%04d', $index + 1);
            MutasiAset::create($data);
        }

        $this->command->info('MutasiAset seeder completed successfully!');
    }
}