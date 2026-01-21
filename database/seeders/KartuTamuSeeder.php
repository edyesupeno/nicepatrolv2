<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KartuTamu;
use App\Models\Project;
use App\Models\Area;

class KartuTamuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first project and areas for seeding
        $project = Project::first();
        $areas = Area::take(2)->get();

        if (!$project || $areas->count() < 2) {
            $this->command->info('Skipping KartuTamu seeder - no project or areas found');
            return;
        }

        $cards = [
            // Area 1 cards
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[0]->id,
                'no_kartu' => 'GT-001',
                'nfc_kartu' => '04:A3:B2:C1:D4:E5:F6',
                'status' => 'aktif',
                'keterangan' => 'Kartu tamu standar untuk area ' . $areas[0]->nama,
            ],
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[0]->id,
                'no_kartu' => 'GT-002',
                'nfc_kartu' => '04:B4:C3:D2:E1:F0:A5',
                'status' => 'aktif',
                'keterangan' => 'Kartu tamu cadangan untuk area ' . $areas[0]->nama,
            ],
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[0]->id,
                'no_kartu' => 'GT-003',
                'nfc_kartu' => null,
                'status' => 'rusak',
                'keterangan' => 'Kartu rusak - NFC tidak berfungsi',
            ],
            
            // Area 2 cards
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[1]->id,
                'no_kartu' => 'GT-004',
                'nfc_kartu' => '04:C5:D4:E3:F2:A1:B0',
                'status' => 'aktif',
                'keterangan' => 'Kartu tamu untuk area ' . $areas[1]->nama,
            ],
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[1]->id,
                'no_kartu' => 'GT-005',
                'nfc_kartu' => '04:D6:E5:F4:A3:B2:C1',
                'status' => 'aktif',
                'keterangan' => 'Kartu tamu VIP untuk area ' . $areas[1]->nama,
            ],
            [
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'area_id' => $areas[1]->id,
                'no_kartu' => 'GT-006',
                'nfc_kartu' => null,
                'status' => 'hilang',
                'keterangan' => 'Kartu hilang - perlu penggantian',
            ],
        ];

        foreach ($cards as $card) {
            KartuTamu::create($card);
        }

        $this->command->info('KartuTamu seeder completed - created ' . count($cards) . ' cards');
    }
}