<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DisposalAset;
use App\Models\DataAset;
use App\Models\AsetKendaraan;
use App\Models\Project;
use App\Models\User;

class DisposalAsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample data
        $projects = Project::take(3)->get();
        $users = User::where('role', '!=', 'superadmin')->take(5)->get();
        $dataAsets = DataAset::take(10)->get();
        $asetKendaraans = AsetKendaraan::take(5)->get();

        if ($projects->isEmpty() || $users->isEmpty()) {
            $this->command->info('Skipping DisposalAsetSeeder - insufficient data');
            return;
        }

        $jenisDisposal = ['dijual', 'rusak', 'hilang', 'tidak_layak', 'expired'];
        $statuses = ['pending', 'approved', 'rejected', 'completed'];

        // Create disposal for data asets
        foreach ($dataAsets->take(8) as $index => $dataAset) {
            $project = $projects->random();
            $user = $users->random();
            $jenis = $jenisDisposal[array_rand($jenisDisposal)];
            $status = $statuses[array_rand($statuses)];

            $disposalData = [
                'nomor_disposal' => 'DSP/' . now()->subDays($index)->format('Ymd') . '/' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'asset_type' => 'data_aset',
                'asset_id' => $dataAset->id,
                'asset_code' => $dataAset->kode_aset,
                'asset_name' => $dataAset->nama_aset,
                'tanggal_disposal' => now()->subDays(rand(1, 30)),
                'jenis_disposal' => $jenis,
                'alasan_disposal' => $this->getAlasanDisposal($jenis),
                'nilai_buku' => $dataAset->nilai_sekarang,
                'nilai_disposal' => $jenis === 'dijual' ? $dataAset->nilai_sekarang * 0.7 : null,
                'pembeli' => $jenis === 'dijual' ? 'PT. Pembeli ' . ($index + 1) : null,
                'catatan' => 'Catatan disposal untuk ' . $dataAset->nama_aset,
                'status' => $status,
                'diajukan_oleh' => $user->id,
                'disetujui_oleh' => $status !== 'pending' ? $users->random()->id : null,
                'tanggal_disetujui' => $status !== 'pending' ? now()->subDays(rand(1, 10)) : null,
                'catatan_approval' => $status !== 'pending' ? 'Disposal ' . $status : null,
                'created_at' => now()->subDays($index),
                'updated_at' => now()->subDays($index),
            ];

            DisposalAset::create($disposalData);

            // Update asset status if disposal is completed
            if ($status === 'completed') {
                // Map disposal jenis to valid status values
                $statusMapping = [
                    'dijual' => 'dijual',
                    'rusak' => 'rusak', 
                    'hilang' => 'dihapus', // Map hilang to dihapus for data aset
                    'tidak_layak' => 'dihapus', // Map tidak_layak to dihapus for data aset
                    'expired' => 'dihapus' // Map expired to dihapus for data aset
                ];
                
                $dataAset->update(['status' => $statusMapping[$jenis]]);
            }
        }

        // Create disposal for aset kendaraans
        foreach ($asetKendaraans->take(5) as $index => $asetKendaraan) {
            $project = $projects->random();
            $user = $users->random();
            $jenis = $jenisDisposal[array_rand($jenisDisposal)];
            $status = $statuses[array_rand($statuses)];

            $disposalData = [
                'nomor_disposal' => 'DSP/' . now()->subDays($index + 10)->format('Ymd') . '/' . str_pad($index + 9, 4, '0', STR_PAD_LEFT),
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'asset_type' => 'aset_kendaraan',
                'asset_id' => $asetKendaraan->id,
                'asset_code' => $asetKendaraan->nomor_polisi,
                'asset_name' => $asetKendaraan->merk . ' ' . $asetKendaraan->model,
                'tanggal_disposal' => now()->subDays(rand(1, 30)),
                'jenis_disposal' => $jenis,
                'alasan_disposal' => $this->getAlasanDisposal($jenis),
                'nilai_buku' => $asetKendaraan->nilai_sekarang,
                'nilai_disposal' => $jenis === 'dijual' ? $asetKendaraan->nilai_sekarang * 0.8 : null,
                'pembeli' => $jenis === 'dijual' ? 'CV. Pembeli Kendaraan ' . ($index + 1) : null,
                'catatan' => 'Catatan disposal untuk ' . $asetKendaraan->merk . ' ' . $asetKendaraan->model,
                'status' => $status,
                'diajukan_oleh' => $user->id,
                'disetujui_oleh' => $status !== 'pending' ? $users->random()->id : null,
                'tanggal_disetujui' => $status !== 'pending' ? now()->subDays(rand(1, 10)) : null,
                'catatan_approval' => $status !== 'pending' ? 'Disposal kendaraan ' . $status : null,
                'created_at' => now()->subDays($index + 10),
                'updated_at' => now()->subDays($index + 10),
            ];

            DisposalAset::create($disposalData);

            // Update asset status if disposal is completed
            if ($status === 'completed') {
                // Map disposal jenis to valid status values for kendaraan
                $statusMapping = [
                    'dijual' => 'dijual',
                    'rusak' => 'rusak',
                    'hilang' => 'hilang',
                    'tidak_layak' => 'rusak', // Map tidak_layak to rusak for kendaraan
                    'expired' => 'rusak' // Map expired to rusak for kendaraan
                ];
                
                $asetKendaraan->update(['status_kendaraan' => $statusMapping[$jenis]]);
            }
        }

        $this->command->info('DisposalAsetSeeder completed successfully');
    }

    private function getAlasanDisposal($jenis)
    {
        $alasan = [
            'dijual' => [
                'Aset sudah tidak digunakan dan masih memiliki nilai jual yang baik',
                'Penggantian dengan aset yang lebih modern dan efisien',
                'Optimalisasi aset perusahaan untuk meningkatkan cash flow',
                'Aset sudah melewati masa produktif optimal'
            ],
            'rusak' => [
                'Aset mengalami kerusakan berat dan tidak dapat diperbaiki',
                'Biaya perbaikan melebihi nilai ekonomis aset',
                'Kerusakan total akibat kecelakaan atau bencana alam',
                'Komponen utama rusak dan tidak tersedia suku cadang'
            ],
            'hilang' => [
                'Aset hilang dan tidak dapat ditemukan setelah pencarian intensif',
                'Kemungkinan pencurian atau kehilangan saat transportasi',
                'Tidak dapat dilacak keberadaannya meskipun sudah dilakukan investigasi',
                'Hilang akibat bencana alam atau force majeure'
            ],
            'tidak_layak' => [
                'Aset sudah tidak memenuhi standar keselamatan yang berlaku',
                'Kondisi fisik sudah sangat buruk dan berbahaya untuk digunakan',
                'Tidak memenuhi regulasi atau standar industri terbaru',
                'Sudah melewati batas usia pakai yang aman'
            ],
            'expired' => [
                'Masa berlaku atau lisensi aset sudah habis',
                'Sertifikasi keselamatan sudah tidak berlaku',
                'Garansi dan dukungan teknis sudah berakhir',
                'Tidak dapat diperpanjang masa berlakunya'
            ]
        ];

        return $alasan[$jenis][array_rand($alasan[$jenis])];
    }
}