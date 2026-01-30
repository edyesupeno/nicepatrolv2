<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AsetKendaraan;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class AsetKendaraanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first project and user for seeding
        $project = Project::first();
        $user = User::first();
        
        if (!$project || !$user) {
            $this->command->warn('No project or user found. Please run ProjectSeeder and UserSeeder first.');
            return;
        }

        $kendaraans = [
            [
                'perusahaan_id' => $user->perusahaan_id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'jenis_kendaraan' => 'mobil',
                'merk' => 'Toyota',
                'model' => 'Avanza',
                'tahun_pembuatan' => '2020',
                'warna' => 'Putih',
                'nomor_polisi' => 'B 1234 ABC',
                'nomor_rangka' => 'MHKA1BA1HKK123456',
                'nomor_mesin' => '3SZ-VE1234567',
                'tanggal_pembelian' => '2020-01-15',
                'harga_pembelian' => 180000000,
                'nilai_penyusutan' => 36000000,
                'nomor_stnk' => 'STNK001234567',
                'tanggal_berlaku_stnk' => Carbon::now()->addMonths(6), // 6 bulan lagi
                'nomor_bpkb' => 'BPKB001234567',
                'atas_nama_bpkb' => 'PT Nice Patrol Indonesia',
                'perusahaan_asuransi' => 'Asuransi Sinar Mas',
                'nomor_polis_asuransi' => 'POL001234567',
                'tanggal_berlaku_asuransi' => Carbon::now()->addMonths(8), // 8 bulan lagi
                'nilai_pajak_tahunan' => 2500000,
                'jatuh_tempo_pajak' => Carbon::now()->addDays(20), // 20 hari lagi (akan expired)
                'kilometer_terakhir' => 45000,
                'tanggal_service_terakhir' => Carbon::now()->subMonths(2),
                'tanggal_service_berikutnya' => Carbon::now()->addMonths(4),
                'driver_utama' => 'Budi Santoso',
                'lokasi_parkir' => 'Parkir Kantor Pusat',
                'status_kendaraan' => 'aktif',
                'catatan' => 'Kendaraan operasional untuk project utama',
            ],
            [
                'perusahaan_id' => $user->perusahaan_id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'jenis_kendaraan' => 'motor',
                'merk' => 'Honda',
                'model' => 'Beat',
                'tahun_pembuatan' => '2019',
                'warna' => 'Merah',
                'nomor_polisi' => 'B 5678 DEF',
                'nomor_rangka' => 'MH1JF4210KK654321',
                'nomor_mesin' => 'JF42E9876543',
                'tanggal_pembelian' => '2019-06-10',
                'harga_pembelian' => 16000000,
                'nilai_penyusutan' => 6400000,
                'nomor_stnk' => 'STNK009876543',
                'tanggal_berlaku_stnk' => Carbon::now()->addDays(15), // 15 hari lagi (akan expired)
                'nomor_bpkb' => 'BPKB009876543',
                'atas_nama_bpkb' => 'PT Nice Patrol Indonesia',
                'perusahaan_asuransi' => 'Asuransi Jasindo',
                'nomor_polis_asuransi' => 'POL009876543',
                'tanggal_berlaku_asuransi' => Carbon::now()->addMonths(3), // 3 bulan lagi
                'nilai_pajak_tahunan' => 350000,
                'jatuh_tempo_pajak' => Carbon::now()->addMonths(2),
                'kilometer_terakhir' => 28000,
                'tanggal_service_terakhir' => Carbon::now()->subMonths(1),
                'tanggal_service_berikutnya' => Carbon::now()->addMonths(2),
                'driver_utama' => 'Ahmad Wijaya',
                'lokasi_parkir' => 'Parkir Security Post',
                'status_kendaraan' => 'aktif',
                'catatan' => 'Motor patroli untuk security',
            ],
            [
                'perusahaan_id' => $user->perusahaan_id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'jenis_kendaraan' => 'mobil',
                'merk' => 'Daihatsu',
                'model' => 'Xenia',
                'tahun_pembuatan' => '2018',
                'warna' => 'Silver',
                'nomor_polisi' => 'B 9999 GHI',
                'nomor_rangka' => 'MHKM1BA3JKK789012',
                'nomor_mesin' => '3SZ-VE7890123',
                'tanggal_pembelian' => '2018-03-20',
                'harga_pembelian' => 165000000,
                'nilai_penyusutan' => 82500000,
                'nomor_stnk' => 'STNK555666777',
                'tanggal_berlaku_stnk' => Carbon::now()->addMonths(10),
                'nomor_bpkb' => 'BPKB555666777',
                'atas_nama_bpkb' => 'PT Nice Patrol Indonesia',
                'perusahaan_asuransi' => null, // Tidak ada asuransi
                'nomor_polis_asuransi' => null,
                'tanggal_berlaku_asuransi' => null,
                'nilai_pajak_tahunan' => 2200000,
                'jatuh_tempo_pajak' => Carbon::now()->addMonths(5),
                'kilometer_terakhir' => 78000,
                'tanggal_service_terakhir' => Carbon::now()->subMonths(3),
                'tanggal_service_berikutnya' => Carbon::now()->addMonths(1),
                'driver_utama' => null,
                'lokasi_parkir' => 'Parkir Cadangan',
                'status_kendaraan' => 'maintenance',
                'catatan' => 'Sedang dalam perbaikan mesin',
            ],
            [
                'perusahaan_id' => $user->perusahaan_id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'jenis_kendaraan' => 'motor',
                'merk' => 'Yamaha',
                'model' => 'Mio',
                'tahun_pembuatan' => '2021',
                'warna' => 'Biru',
                'nomor_polisi' => 'B 1111 JKL',
                'nomor_rangka' => 'MH3RH1210MK111222',
                'nomor_mesin' => 'RH12E1112223',
                'tanggal_pembelian' => '2021-08-05',
                'harga_pembelian' => 18500000,
                'nilai_penyusutan' => 3700000,
                'nomor_stnk' => 'STNK111222333',
                'tanggal_berlaku_stnk' => Carbon::now()->addMonths(14),
                'nomor_bpkb' => 'BPKB111222333',
                'atas_nama_bpkb' => 'PT Nice Patrol Indonesia',
                'perusahaan_asuransi' => 'Asuransi Adira',
                'nomor_polis_asuransi' => 'POL111222333',
                'tanggal_berlaku_asuransi' => Carbon::now()->addDays(25), // 25 hari lagi (akan expired)
                'nilai_pajak_tahunan' => 400000,
                'jatuh_tempo_pajak' => Carbon::now()->addMonths(7),
                'kilometer_terakhir' => 15000,
                'tanggal_service_terakhir' => Carbon::now()->subWeeks(2),
                'tanggal_service_berikutnya' => Carbon::now()->addMonths(3),
                'driver_utama' => 'Sari Indah',
                'lokasi_parkir' => 'Parkir Kantor',
                'status_kendaraan' => 'aktif',
                'catatan' => 'Motor baru untuk patroli malam',
            ],
            [
                'perusahaan_id' => $user->perusahaan_id,
                'project_id' => $project->id,
                'created_by' => $user->id,
                'jenis_kendaraan' => 'mobil',
                'merk' => 'Suzuki',
                'model' => 'Ertiga',
                'tahun_pembuatan' => '2017',
                'warna' => 'Hitam',
                'nomor_polisi' => 'B 7777 MNO',
                'nomor_rangka' => 'JSAFJB23S00444555',
                'nomor_mesin' => 'K15B4445556',
                'tanggal_pembelian' => '2017-11-12',
                'harga_pembelian' => 195000000,
                'nilai_penyusutan' => 117000000,
                'nomor_stnk' => 'STNK444555666',
                'tanggal_berlaku_stnk' => Carbon::now()->subDays(5), // Sudah expired 5 hari lalu
                'nomor_bpkb' => 'BPKB444555666',
                'atas_nama_bpkb' => 'PT Nice Patrol Indonesia',
                'perusahaan_asuransi' => 'Asuransi Allianz',
                'nomor_polis_asuransi' => 'POL444555666',
                'tanggal_berlaku_asuransi' => Carbon::now()->subMonths(1), // Sudah expired 1 bulan lalu
                'nilai_pajak_tahunan' => 2800000,
                'jatuh_tempo_pajak' => Carbon::now()->addMonths(3),
                'kilometer_terakhir' => 95000,
                'tanggal_service_terakhir' => Carbon::now()->subMonths(4),
                'tanggal_service_berikutnya' => Carbon::now()->subMonths(1), // Sudah lewat jadwal service
                'driver_utama' => 'Rudi Hartono',
                'lokasi_parkir' => 'Parkir Utama',
                'status_kendaraan' => 'rusak',
                'catatan' => 'Perlu perpanjangan STNK dan asuransi, mesin bermasalah',
            ],
        ];

        foreach ($kendaraans as $kendaraanData) {
            AsetKendaraan::create($kendaraanData);
        }

        $this->command->info('AsetKendaraan seeder completed successfully!');
        $this->command->info('Created ' . count($kendaraans) . ' vehicle records with various document expiry scenarios.');
    }
}