<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PenyerahanPerlengkapan;
use App\Models\PenyerahanPerlengkapanItem;
use App\Models\KategoriPerlengkapan;
use App\Models\ItemPerlengkapan;
use App\Models\Karyawan;
use App\Models\Project;

class PenyerahanPerlengkapanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get sample data
        $project = Project::first();
        
        if (!$project) {
            $this->command->info('Skipping PenyerahanPerlengkapanSeeder - no project found');
            return;
        }

        // Create sample penyerahan
        $penyerahan = PenyerahanPerlengkapan::create([
            'perusahaan_id' => $project->perusahaan_id,
            'project_id' => $project->id,
            'created_by' => 1, // Assuming user ID 1 exists
            'tanggal_mulai' => now()->subDays(2),
            'tanggal_selesai' => now()->addDays(5),
            'status' => 'draft',
            'keterangan' => 'Jadwal penyerahan perlengkapan untuk karyawan baru',
        ]);

        // Create another penyerahan with different status
        $penyerahan2 = PenyerahanPerlengkapan::create([
            'perusahaan_id' => $project->perusahaan_id,
            'project_id' => $project->id,
            'created_by' => 1,
            'tanggal_mulai' => now()->subDays(10),
            'tanggal_selesai' => now()->subDays(3),
            'status' => 'draft',
            'keterangan' => 'Jadwal penyerahan seragam dan perlengkapan kerja',
        ]);

        $this->command->info('PenyerahanPerlengkapanSeeder completed successfully');
    }
}