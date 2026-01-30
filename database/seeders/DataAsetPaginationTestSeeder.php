<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataAset;
use App\Models\User;
use App\Models\Project;

class DataAsetPaginationTestSeeder extends Seeder
{
    /**
     * Run the database seeder untuk test pagination.
     */
    public function run(): void
    {
        // Get user abb@nicepatrol.id
        $userAbb = User::where('email', 'abb@nicepatrol.id')->first();
        
        if (!$userAbb) {
            $this->command->info('User abb@nicepatrol.id not found');
            return;
        }

        $perusahaan = $userAbb->perusahaan;
        $project = Project::where('perusahaan_id', $perusahaan->id)->first();
        
        if (!$perusahaan || !$project) {
            $this->command->info('No perusahaan or project found');
            return;
        }

        // Generate 50 additional test data
        $kategoriList = ['IT', 'Furnitur', 'Kendaraan', 'Elektronik', 'Keamanan', 'Peralatan Medis', 'Alat Tulis', 'Peralatan Dapur'];
        $statusList = ['ada', 'rusak', 'dijual', 'dihapus'];
        $picList = ['Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Sartika', 'Eko Prasetyo', 'Fajar Nugroho', 'Gita Savitri', 'Hendra Wijaya'];

        for ($i = 1; $i <= 50; $i++) {
            $kategori = $kategoriList[array_rand($kategoriList)];
            $status = $statusList[array_rand($statusList)];
            $pic = $picList[array_rand($picList)];
            
            // Random dates in the last 3 years
            $tanggalBeli = now()->subDays(rand(1, 1095))->format('Y-m-d');
            
            // Random prices
            $hargaBeli = rand(500000, 50000000);
            $nilaiPenyusutan = rand(0, $hargaBeli * 0.5);

            DataAset::create([
                'perusahaan_id' => $perusahaan->id,
                'project_id' => $project->id,
                'created_by' => $userAbb->id,
                'nama_aset' => "Test Aset {$kategori} #{$i}",
                'kategori' => $kategori,
                'tanggal_beli' => $tanggalBeli,
                'harga_beli' => $hargaBeli,
                'nilai_penyusutan' => $nilaiPenyusutan,
                'pic_penanggung_jawab' => $pic,
                'catatan_tambahan' => "Test data untuk pagination #{$i}",
                'status' => $status,
            ]);
        }

        $this->command->info('Successfully created 50 additional test data for pagination!');
    }
}