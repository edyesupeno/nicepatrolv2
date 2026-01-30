<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAset;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\User;

class DataAsetSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get perusahaan untuk user abb@nicepatrol.id
        $userAbb = User::where('email', 'abb@nicepatrol.id')->first();
        
        if (!$userAbb) {
            $this->command->info('User abb@nicepatrol.id not found, skipping DataAset seeder');
            return;
        }

        $perusahaan = $userAbb->perusahaan;
        $project = Project::where('perusahaan_id', $perusahaan->id)->first();
        
        if (!$perusahaan || !$project) {
            $this->command->info('Skipping DataAset seeder - no perusahaan or project found for abb@nicepatrol.id');
            return;
        }

        // Delete existing data untuk perusahaan ini
        DataAset::where('perusahaan_id', $perusahaan->id)->delete();

        $dataAsets = [
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Laptop Dell Latitude 5520',
                'kategori' => 'IT',
                'tanggal_beli' => '2023-01-15',
                'harga_beli' => 15000000,
                'nilai_penyusutan' => 3000000,
                'pic_penanggung_jawab' => 'Ahmad Rizki',
                'catatan_tambahan' => 'Laptop untuk keperluan administrasi kantor',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Meja Kerja Kayu Jati',
                'kategori' => 'Furnitur',
                'tanggal_beli' => '2022-06-10',
                'harga_beli' => 2500000,
                'nilai_penyusutan' => 500000,
                'pic_penanggung_jawab' => 'Siti Nurhaliza',
                'catatan_tambahan' => 'Meja kerja untuk ruang manager',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Toyota Avanza 2021',
                'kategori' => 'Kendaraan',
                'tanggal_beli' => '2021-03-20',
                'harga_beli' => 220000000,
                'nilai_penyusutan' => 50000000,
                'pic_penanggung_jawab' => 'Budi Santoso',
                'catatan_tambahan' => 'Kendaraan operasional untuk patroli',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Printer Canon Pixma G3010',
                'kategori' => 'IT',
                'tanggal_beli' => '2023-08-05',
                'harga_beli' => 2200000,
                'nilai_penyusutan' => 200000,
                'pic_penanggung_jawab' => 'Dewi Sartika',
                'catatan_tambahan' => 'Printer untuk keperluan cetak dokumen',
                'status' => 'rusak',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'AC Split Daikin 1.5 PK',
                'kategori' => 'Elektronik',
                'tanggal_beli' => '2022-12-01',
                'harga_beli' => 4500000,
                'nilai_penyusutan' => 900000,
                'pic_penanggung_jawab' => 'Eko Prasetyo',
                'catatan_tambahan' => 'AC untuk ruang meeting',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Server HP ProLiant DL380',
                'kategori' => 'IT',
                'tanggal_beli' => '2020-09-15',
                'harga_beli' => 45000000,
                'nilai_penyusutan' => 18000000,
                'pic_penanggung_jawab' => 'Fajar Nugroho',
                'catatan_tambahan' => 'Server utama untuk sistem informasi',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'Kursi Kantor Ergonomis',
                'kategori' => 'Furnitur',
                'tanggal_beli' => '2023-02-28',
                'harga_beli' => 1800000,
                'nilai_penyusutan' => 180000,
                'pic_penanggung_jawab' => 'Gita Savitri',
                'catatan_tambahan' => 'Kursi untuk workstation karyawan',
                'status' => 'ada',
            ],
            [
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => 'CCTV Hikvision 4MP',
                'kategori' => 'Keamanan',
                'tanggal_beli' => '2023-05-10',
                'harga_beli' => 3500000,
                'nilai_penyusutan' => 350000,
                'pic_penanggung_jawab' => 'Hendra Wijaya',
                'catatan_tambahan' => 'Kamera pengawas untuk area parkir',
                'status' => 'ada',
            ],
        ];

        foreach ($dataAsets as $asetData) {
            DataAset::create($asetData);
        }

        $this->command->info('DataAset seeder completed successfully for PT ABB (abb@nicepatrol.id)!');
    }
}