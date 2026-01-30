<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MaintenanceAset;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\User;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use Carbon\Carbon;

class MaintenanceAsetSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get sample data
        $perusahaan = Perusahaan::first();
        if (!$perusahaan) return;

        $projects = Project::where('perusahaan_id', $perusahaan->id)->take(2)->get();
        if ($projects->isEmpty()) return;

        $user = User::where('perusahaan_id', $perusahaan->id)->first();
        if (!$user) return;

        $dataAsets = DataAset::where('perusahaan_id', $perusahaan->id)->take(3)->get();
        $asetKendaraans = AsetKendaraan::where('perusahaan_id', $perusahaan->id)->take(3)->get();

        $maintenanceData = [];

        // Sample maintenance untuk Data Aset
        foreach ($dataAsets as $index => $aset) {
            $project = $projects->random();
            
            // Past maintenance (completed)
            $maintenanceData[] = [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'nomor_maintenance' => "MNT-{$project->id}-2024-" . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'asset_type' => 'data_aset',
                'asset_id' => $aset->id,
                'jenis_maintenance' => ['preventive', 'corrective', 'predictive'][array_rand(['preventive', 'corrective', 'predictive'])],
                'tanggal_maintenance' => Carbon::now()->subDays(rand(30, 90)),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '12:00',
                'estimasi_durasi' => 240,
                'deskripsi_pekerjaan' => 'Pemeliharaan rutin ' . $aset->nama_aset . ' - pembersihan, kalibrasi, dan pengecekan fungsi',
                'catatan_sebelum' => 'Kondisi aset masih baik, perlu pembersihan berkala',
                'catatan_sesudah' => 'Maintenance selesai, aset berfungsi dengan baik',
                'teknisi_internal' => 'Tim Maintenance Internal',
                'biaya_sparepart' => rand(100000, 500000),
                'biaya_jasa' => rand(200000, 800000),
                'biaya_lainnya' => rand(50000, 200000),
                'status' => 'completed',
                'prioritas' => ['low', 'medium', 'high'][array_rand(['low', 'medium', 'high'])],
                'hasil_maintenance' => 'berhasil',
                'tindakan_dilakukan' => 'Pembersihan menyeluruh, penggantian filter, kalibrasi ulang',
                'rekomendasi' => 'Maintenance berikutnya dalam 3 bulan',
                'reminder_aktif' => true,
                'reminder_hari' => 7,
                'interval_maintenance' => 90,
                'tanggal_maintenance_berikutnya' => Carbon::now()->addDays(90),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Upcoming maintenance (scheduled)
            $maintenanceData[] = [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'nomor_maintenance' => "MNT-{$project->id}-2026-" . str_pad($index + 10, 4, '0', STR_PAD_LEFT),
                'asset_type' => 'data_aset',
                'asset_id' => $aset->id,
                'jenis_maintenance' => 'preventive',
                'tanggal_maintenance' => Carbon::now()->addDays(rand(7, 30)),
                'waktu_mulai' => '09:00',
                'estimasi_durasi' => 180,
                'deskripsi_pekerjaan' => 'Maintenance preventif ' . $aset->nama_aset . ' - pengecekan berkala dan pembersihan',
                'catatan_sebelum' => 'Dijadwalkan maintenance rutin',
                'teknisi_internal' => 'Tim Maintenance Internal',
                'biaya_sparepart' => rand(50000, 300000),
                'biaya_jasa' => rand(150000, 600000),
                'status' => 'scheduled',
                'prioritas' => 'medium',
                'reminder_aktif' => true,
                'reminder_hari' => 3,
                'interval_maintenance' => 90,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Sample maintenance untuk Aset Kendaraan
        foreach ($asetKendaraans as $index => $kendaraan) {
            $project = $projects->random();
            
            // Past maintenance (completed)
            $maintenanceData[] = [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'nomor_maintenance' => "MNT-{$project->id}-2024-" . str_pad($index + 20, 4, '0', STR_PAD_LEFT),
                'asset_type' => 'aset_kendaraan',
                'asset_id' => $kendaraan->id,
                'jenis_maintenance' => 'corrective',
                'tanggal_maintenance' => Carbon::now()->subDays(rand(15, 60)),
                'waktu_mulai' => '08:00',
                'waktu_selesai' => '16:00',
                'estimasi_durasi' => 480,
                'deskripsi_pekerjaan' => 'Service berkala kendaraan ' . $kendaraan->merk . ' ' . $kendaraan->model . ' - ganti oli, tune up, cek rem',
                'catatan_sebelum' => 'Kendaraan sudah mencapai 10.000 km, perlu service berkala',
                'catatan_sesudah' => 'Service selesai, kendaraan dalam kondisi prima',
                'vendor_eksternal' => 'Bengkel Resmi ' . $kendaraan->merk,
                'kontak_vendor' => '021-12345678',
                'biaya_sparepart' => rand(500000, 2000000),
                'biaya_jasa' => rand(300000, 1000000),
                'biaya_lainnya' => rand(100000, 300000),
                'status' => 'completed',
                'prioritas' => 'high',
                'hasil_maintenance' => 'berhasil',
                'masalah_ditemukan' => 'Oli kotor, filter udara tersumbat, kampas rem tipis',
                'tindakan_dilakukan' => 'Ganti oli mesin, ganti filter udara, ganti kampas rem, tune up mesin',
                'rekomendasi' => 'Service berikutnya pada 15.000 km atau 6 bulan',
                'reminder_aktif' => true,
                'reminder_hari' => 14,
                'interval_maintenance' => 180,
                'tanggal_maintenance_berikutnya' => Carbon::now()->addDays(180),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Upcoming maintenance (scheduled)
            $maintenanceData[] = [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'nomor_maintenance' => "MNT-{$project->id}-2026-" . str_pad($index + 30, 4, '0', STR_PAD_LEFT),
                'asset_type' => 'aset_kendaraan',
                'asset_id' => $kendaraan->id,
                'jenis_maintenance' => 'preventive',
                'tanggal_maintenance' => Carbon::now()->addDays(rand(5, 20)),
                'waktu_mulai' => '08:00',
                'estimasi_durasi' => 240,
                'deskripsi_pekerjaan' => 'Maintenance preventif kendaraan ' . $kendaraan->merk . ' ' . $kendaraan->model . ' - cek rutin dan perawatan',
                'catatan_sebelum' => 'Maintenance rutin terjadwal',
                'vendor_eksternal' => 'Bengkel Resmi ' . $kendaraan->merk,
                'kontak_vendor' => '021-12345678',
                'biaya_sparepart' => rand(200000, 800000),
                'biaya_jasa' => rand(200000, 600000),
                'status' => 'scheduled',
                'prioritas' => 'medium',
                'reminder_aktif' => true,
                'reminder_hari' => 7,
                'interval_maintenance' => 180,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Overdue maintenance
            if ($index === 0) {
                $maintenanceData[] = [
                    'perusahaan_id' => $perusahaan->id,
                    'project_id' => $project->id,
                    'created_by' => $user->id,
                    'nomor_maintenance' => "MNT-{$project->id}-2026-" . str_pad(99, 4, '0', STR_PAD_LEFT),
                    'asset_type' => 'aset_kendaraan',
                    'asset_id' => $kendaraan->id,
                    'jenis_maintenance' => 'corrective',
                    'tanggal_maintenance' => Carbon::now()->subDays(5), // Overdue
                    'waktu_mulai' => '08:00',
                    'estimasi_durasi' => 360,
                    'deskripsi_pekerjaan' => 'Perbaikan mendesak - masalah pada sistem rem',
                    'catatan_sebelum' => 'Dilaporkan ada masalah pada sistem rem, perlu segera ditangani',
                    'vendor_eksternal' => 'Bengkel Darurat 24 Jam',
                    'kontak_vendor' => '021-87654321',
                    'biaya_sparepart' => 1500000,
                    'biaya_jasa' => 800000,
                    'status' => 'scheduled',
                    'prioritas' => 'urgent',
                    'reminder_aktif' => true,
                    'reminder_hari' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert data
        foreach ($maintenanceData as $data) {
            MaintenanceAset::create($data);
        }

        $this->command->info('MaintenanceAset seeder completed successfully!');
    }
}