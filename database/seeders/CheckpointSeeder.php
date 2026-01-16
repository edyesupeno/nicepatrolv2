<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Checkpoint;
use App\Models\RutePatrol;

class CheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rutePatrols = RutePatrol::all();

        if ($rutePatrols->isEmpty()) {
            $this->command->warn('Tidak ada rute patrol. Jalankan AreaPatrolSeeder terlebih dahulu.');
            return;
        }

        foreach ($rutePatrols as $rute) {
            // Buat 3-5 checkpoint per rute
            $jumlahCheckpoint = rand(3, 5);
            
            for ($i = 1; $i <= $jumlahCheckpoint; $i++) {
                Checkpoint::create([
                    'perusahaan_id' => $rute->perusahaan_id,
                    'rute_patrol_id' => $rute->id,
                    'nama' => "Checkpoint {$i} - {$rute->nama}",
                    'qr_code' => 'CP-' . strtoupper(uniqid()),
                    'deskripsi' => "Checkpoint ke-{$i} pada rute {$rute->nama}",
                    'urutan' => $i,
                    'alamat' => "Lokasi checkpoint {$i}",
                    'latitude' => -6.2 + (rand(-100, 100) / 1000),
                    'longitude' => 106.8 + (rand(-100, 100) / 1000),
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Checkpoint seeder berhasil dijalankan!');
    }
}
