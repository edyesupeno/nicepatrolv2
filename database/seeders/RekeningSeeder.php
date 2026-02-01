<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rekening;
use App\Models\Project;
use App\Models\Perusahaan;

class RekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first perusahaan and its projects
        $perusahaan = Perusahaan::first();
        if (!$perusahaan) {
            $this->command->info('No perusahaan found. Please run PerusahaanDataSeeder first.');
            return;
        }

        $projects = Project::where('perusahaan_id', $perusahaan->id)->get();
        if ($projects->isEmpty()) {
            $this->command->info('No projects found. Please create projects first.');
            return;
        }

        $colors = ['#3B82C8', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#F97316', '#06B6D4', '#84CC16', '#EC4899', '#6B7280'];
        $banks = ['Bank Mandiri', 'Bank BCA', 'Bank BNI', 'Bank BRI', 'Bank CIMB Niaga', 'Bank Danamon', 'Bank Permata', 'Bank OCBC NISP'];
        $jenisRekening = ['operasional', 'payroll', 'investasi', 'emergency', 'lainnya'];

        $rekeningData = [];
        $counter = 1;

        foreach ($projects as $index => $project) {
            // Create 2-4 rekening per project
            $jumlahRekening = rand(2, 4);
            $isPrimarySet = false;

            for ($i = 0; $i < $jumlahRekening; $i++) {
                $bank = $banks[array_rand($banks)];
                $jenis = $jenisRekening[array_rand($jenisRekening)];
                $color = $colors[array_rand($colors)];
                $saldoAwal = rand(10000000, 500000000); // 10 juta - 500 juta
                
                // Generate nomor rekening
                $nomorRekening = '';
                if ($bank === 'Bank BCA') {
                    $nomorRekening = '123' . str_pad($counter, 7, '0', STR_PAD_LEFT);
                } elseif ($bank === 'Bank Mandiri') {
                    $nomorRekening = '900' . str_pad($counter, 10, '0', STR_PAD_LEFT);
                } elseif ($bank === 'Bank BNI') {
                    $nomorRekening = '046' . str_pad($counter, 7, '0', STR_PAD_LEFT);
                } elseif ($bank === 'Bank BRI') {
                    $nomorRekening = '002' . str_pad($counter, 12, '0', STR_PAD_LEFT);
                } else {
                    $nomorRekening = rand(1000000000, 9999999999);
                }

                $rekeningData[] = [
                    'perusahaan_id' => $perusahaan->id,
                    'project_id' => $project->id,
                    'nama_rekening' => "Rekening {$this->getJenisLabel($jenis)} - {$project->nama}",
                    'nomor_rekening' => $nomorRekening,
                    'nama_bank' => $bank,
                    'nama_pemilik' => $perusahaan->nama,
                    'jenis_rekening' => $jenis,
                    'saldo_awal' => $saldoAwal,
                    'saldo_saat_ini' => $saldoAwal + rand(-5000000, 50000000), // Variasi saldo saat ini
                    'mata_uang' => 'IDR',
                    'keterangan' => $this->generateKeterangan($jenis, $project->nama),
                    'is_active' => rand(0, 10) > 1, // 90% aktif
                    'is_primary' => !$isPrimarySet && $i === 0, // First rekening is primary
                    'warna_card' => $color,
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(0, 5)),
                ];

                if (!$isPrimarySet && $i === 0) {
                    $isPrimarySet = true;
                }

                $counter++;
            }
        }

        // Insert data
        Rekening::insert($rekeningData);

        $this->command->info('Rekening seeder completed. Created ' . count($rekeningData) . ' rekening records.');
    }

    private function getJenisLabel($jenis)
    {
        $labels = [
            'operasional' => 'Operasional',
            'payroll' => 'Payroll',
            'investasi' => 'Investasi',
            'emergency' => 'Emergency',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$jenis] ?? 'Operasional';
    }

    private function generateKeterangan($jenis, $projectName)
    {
        $keteranganTemplates = [
            'operasional' => [
                "Rekening untuk kebutuhan operasional harian project {$projectName}",
                "Digunakan untuk pembayaran vendor dan supplier project {$projectName}",
                "Rekening utama untuk transaksi operasional project {$projectName}"
            ],
            'payroll' => [
                "Khusus untuk pembayaran gaji karyawan project {$projectName}",
                "Rekening payroll bulanan project {$projectName}",
                "Digunakan untuk transfer gaji dan tunjangan project {$projectName}"
            ],
            'investasi' => [
                "Dana investasi dan pengembangan project {$projectName}",
                "Rekening untuk investasi jangka panjang project {$projectName}",
                "Dana cadangan untuk ekspansi project {$projectName}"
            ],
            'emergency' => [
                "Dana darurat untuk situasi mendesak project {$projectName}",
                "Emergency fund project {$projectName}",
                "Cadangan dana untuk kondisi darurat project {$projectName}"
            ],
            'lainnya' => [
                "Rekening untuk keperluan khusus project {$projectName}",
                "Digunakan untuk transaksi miscellaneous project {$projectName}",
                "Rekening serbaguna project {$projectName}"
            ]
        ];

        $templates = $keteranganTemplates[$jenis] ?? $keteranganTemplates['lainnya'];
        return $templates[array_rand($templates)];
    }
}