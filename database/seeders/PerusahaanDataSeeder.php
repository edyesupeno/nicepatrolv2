<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Perusahaan;
use App\Models\Kantor;
use App\Models\Project;
use App\Models\Jabatan;

class PerusahaanDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all perusahaan
        $perusahaans = Perusahaan::all();

        foreach ($perusahaans as $perusahaan) {
            // Create Kantor
            $kantor = Kantor::create([
                'perusahaan_id' => $perusahaan->id,
                'nama' => 'Kantor Pusat ' . $perusahaan->nama,
                'alamat' => $perusahaan->alamat,
                'telepon' => $perusahaan->telepon,
                'email' => $perusahaan->email,
                'is_pusat' => true,
                'is_active' => true,
            ]);

            // Create Projects
            $projects = [
                [
                    'nama' => 'Kantor Jakarta',
                    'timezone' => 'Asia/Jakarta',
                    'tanggal_mulai' => now(),
                    'deskripsi' => 'Project keamanan kantor Jakarta',
                    'guest_book_mode' => 'standard_migas',
                    'enable_questionnaire' => true,
                ],
                [
                    'nama' => 'Area Batang',
                    'timezone' => 'Asia/Jakarta',
                    'tanggal_mulai' => now(),
                    'deskripsi' => 'Project patroli area Batang',
                    'guest_book_mode' => 'simple',
                    'enable_questionnaire' => true,
                ],
            ];

            foreach ($projects as $projectData) {
                $project = Project::create([
                    'perusahaan_id' => $perusahaan->id,
                    'kantor_id' => $kantor->id,
                    'nama' => $projectData['nama'],
                    'timezone' => $projectData['timezone'],
                    'tanggal_mulai' => $projectData['tanggal_mulai'],
                    'deskripsi' => $projectData['deskripsi'],
                    'is_active' => true,
                    // Add guest book settings
                    'guest_book_mode' => $projectData['guest_book_mode'] ?? 'simple',
                    'enable_questionnaire' => $projectData['enable_questionnaire'] ?? true,
                ]);

                // Create Jabatan for this project (per perusahaan)
                $jabatans = [
                    ['nama' => 'Security Supervisor', 'deskripsi' => 'Supervisor keamanan'],
                    ['nama' => 'Security Officer', 'deskripsi' => 'Petugas keamanan'],
                    ['nama' => 'Patrol Leader', 'deskripsi' => 'Ketua tim patroli'],
                ];

                foreach ($jabatans as $jabatanData) {
                    $jabatan = Jabatan::create([
                        'perusahaan_id' => $perusahaan->id,
                        'nama' => $jabatanData['nama'],
                        'deskripsi' => $jabatanData['deskripsi'],
                        'is_active' => true,
                    ]);

                    // Attach jabatan to project
                    $project->jabatans()->attach($jabatan->id);
                }
            }
        }
    }
}
