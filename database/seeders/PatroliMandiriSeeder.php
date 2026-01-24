<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PatroliMandiri;
use App\Models\Project;
use App\Models\AreaPatrol;
use App\Models\User;
use Carbon\Carbon;

class PatroliMandiriSeeder extends Seeder
{
    public function run(): void
    {
        // Get sample data
        $projects = Project::take(3)->get();
        $securityOfficers = User::where('role', 'security_officer')->take(5)->get();
        
        if ($projects->isEmpty() || $securityOfficers->isEmpty()) {
            $this->command->info('Skipping PatroliMandiri seeder - no projects or security officers found');
            return;
        }

        $jenisKendala = [
            'kebakaran',
            'aset_rusak', 
            'aset_hilang',
            'orang_mencurigakan',
            'kabel_terbuka',
            'pencurian',
            'sabotase',
            'demo'
        ];

        $lokasiContoh = [
            'Pos Jaga Utama',
            'Area Parkir',
            'Gedung Kantor',
            'Gudang Penyimpanan',
            'Area Produksi',
            'Kantin Karyawan',
            'Toilet Umum',
            'Ruang Server',
            'Area Loading Dock',
            'Taman Depan'
        ];

        $deskripsiAman = [
            'Kondisi area aman dan terkendali',
            'Tidak ada aktivitas mencurigakan',
            'Semua fasilitas dalam kondisi baik',
            'Area bersih dan tertata rapi',
            'Pencahayaan berfungsi dengan baik'
        ];

        $deskripsiKendala = [
            'Ditemukan kerusakan pada fasilitas',
            'Ada aktivitas mencurigakan di area ini',
            'Kondisi keamanan perlu perhatian khusus',
            'Fasilitas tidak berfungsi dengan baik',
            'Perlu tindakan perbaikan segera'
        ];

        $tindakanDiambil = [
            'Melakukan patroli tambahan di area tersebut',
            'Menghubungi tim maintenance untuk perbaikan',
            'Melapor ke supervisor untuk tindak lanjut',
            'Melakukan koordinasi dengan pihak terkait',
            'Memasang tanda peringatan sementara'
        ];

        // Create sample data
        foreach ($projects as $project) {
            $areas = AreaPatrol::where('project_id', $project->id)->take(3)->get();
            
            for ($i = 0; $i < 15; $i++) {
                $isAman = rand(0, 100) < 70; // 70% kemungkinan aman
                $waktuLaporan = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                $data = [
                    'perusahaan_id' => $project->perusahaan_id,
                    'project_id' => $project->id,
                    'area_patrol_id' => $areas->isNotEmpty() ? $areas->random()->id : null,
                    'petugas_id' => $securityOfficers->random()->id,
                    'nama_lokasi' => $lokasiContoh[array_rand($lokasiContoh)],
                    'latitude' => -6.2000000 + (rand(-1000, 1000) / 10000), // Jakarta area
                    'longitude' => 106.8000000 + (rand(-1000, 1000) / 10000),
                    'waktu_laporan' => $waktuLaporan,
                    'status_lokasi' => $isAman ? 'aman' : 'tidak_aman',
                    'catatan_petugas' => $isAman ? 
                        $deskripsiAman[array_rand($deskripsiAman)] : 
                        $deskripsiKendala[array_rand($deskripsiKendala)],
                ];

                if (!$isAman) {
                    $data['jenis_kendala'] = $jenisKendala[array_rand($jenisKendala)];
                    $data['deskripsi_kendala'] = $deskripsiKendala[array_rand($deskripsiKendala)];
                    $data['tindakan_yang_diambil'] = $tindakanDiambil[array_rand($tindakanDiambil)];
                }

                // Set status laporan
                $statusOptions = ['submitted', 'reviewed', 'resolved'];
                $data['status_laporan'] = $statusOptions[array_rand($statusOptions)];

                // If reviewed or resolved, add review data
                if ($data['status_laporan'] !== 'submitted') {
                    $data['reviewed_by'] = $securityOfficers->random()->id;
                    $data['reviewed_at'] = $waktuLaporan->addHours(rand(1, 48));
                    $data['review_catatan'] = 'Review completed - ' . ($data['status_laporan'] === 'resolved' ? 'issue resolved' : 'under investigation');
                }

                $patroliMandiri = PatroliMandiri::create($data);
                
                // Set prioritas and generate maps URL
                $patroliMandiri->setPrioritas();
                $patroliMandiri->generateMapsUrl();
                $patroliMandiri->save();
            }
        }

        $this->command->info('PatroliMandiri seeder completed successfully');
    }
}