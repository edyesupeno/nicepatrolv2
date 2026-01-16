<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AreaPatrol;
use App\Models\Perusahaan;
use App\Models\Project;

class AreaPatrolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all perusahaan
        $perusahaans = Perusahaan::all();

        foreach ($perusahaans as $perusahaan) {
            // Get projects for this perusahaan
            $projects = Project::where('perusahaan_id', $perusahaan->id)->get();
            
            if ($projects->isEmpty()) {
                continue;
            }

            foreach ($projects as $project) {
                $areas = [
                    [
                        'nama' => 'Area Gedung Utama',
                        'deskripsi' => 'Area gedung utama dan sekitarnya',
                        'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                        'koordinat' => '-6.200000, 106.816666',
                        'is_active' => true,
                    ],
                    [
                        'nama' => 'Area Parkir & Gudang',
                        'deskripsi' => 'Area parkir karyawan dan gudang penyimpanan',
                        'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                        'koordinat' => '-6.201000, 106.817666',
                        'is_active' => true,
                    ],
                    [
                        'nama' => 'Area Produksi',
                        'deskripsi' => 'Area produksi dan manufaktur',
                        'alamat' => 'Jl. Sudirman No. 123, Jakarta',
                        'koordinat' => '-6.202000, 106.818666',
                        'is_active' => true,
                    ],
                ];

                foreach ($areas as $area) {
                    AreaPatrol::create([
                        'perusahaan_id' => $perusahaan->id,
                        'project_id' => $project->id,
                        'nama' => $area['nama'],
                        'deskripsi' => $area['deskripsi'],
                        'alamat' => $area['alamat'],
                        'koordinat' => $area['koordinat'],
                        'is_active' => $area['is_active'],
                    ]);
                }
            }
        }
    }
}
