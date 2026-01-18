<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KuesionerTamu;
use App\Models\PertanyaanTamu;
use App\Models\Project;
use App\Models\AreaPatrol;
use App\Models\Perusahaan;

class KuesionerTamuSeeder extends Seeder
{
    public function run(): void
    {
        // Get first perusahaan that has projects and areas
        $perusahaan = Perusahaan::whereHas('projects.areaPatrols')->first();
        if (!$perusahaan) {
            $this->command->info('No perusahaan with projects and areas found.');
            return;
        }

        // Get first project with areas
        $project = Project::where('perusahaan_id', $perusahaan->id)
            ->whereHas('areaPatrols')
            ->first();
        if (!$project) {
            $this->command->info('No project with areas found.');
            return;
        }

        // Get first area
        $area = AreaPatrol::where('project_id', $project->id)->first();
        if (!$area) {
            $this->command->info('No area patrol found.');
            return;
        }

        // Create sample kuesioner tamu
        $kuesioner = KuesionerTamu::create([
            'perusahaan_id' => $perusahaan->id,
            'project_id' => $project->id,
            'area_patrol_id' => $area->id,
            'judul' => 'Kuesioner Kepuasan Tamu',
            'deskripsi' => 'Kuesioner untuk mengukur tingkat kepuasan tamu terhadap layanan keamanan',
            'is_active' => true,
        ]);

        // Create sample questions
        $pertanyaans = [
            [
                'urutan' => 1,
                'pertanyaan' => 'Bagaimana penilaian Anda terhadap keramahan petugas keamanan?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Sangat Baik', 'Baik', 'Cukup', 'Kurang', 'Sangat Kurang'],
                'is_required' => true,
            ],
            [
                'urutan' => 2,
                'pertanyaan' => 'Apakah Anda merasa aman selama berada di area ini?',
                'tipe_jawaban' => 'pilihan',
                'opsi_jawaban' => ['Sangat Aman', 'Aman', 'Cukup Aman', 'Kurang Aman', 'Tidak Aman'],
                'is_required' => true,
            ],
            [
                'urutan' => 3,
                'pertanyaan' => 'Saran atau masukan untuk peningkatan layanan keamanan:',
                'tipe_jawaban' => 'text',
                'opsi_jawaban' => null,
                'is_required' => false,
            ],
        ];

        foreach ($pertanyaans as $pertanyaanData) {
            PertanyaanTamu::create([
                'kuesioner_tamu_id' => $kuesioner->id,
                ...$pertanyaanData
            ]);
        }

        $this->command->info("Sample kuesioner tamu created successfully for {$perusahaan->nama}!");
    }
}