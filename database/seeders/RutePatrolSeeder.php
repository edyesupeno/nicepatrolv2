<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RutePatrol;
use App\Models\AreaPatrol;
use App\Models\Perusahaan;

class RutePatrolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all perusahaan
        $perusahaans = Perusahaan::all();

        foreach ($perusahaans as $perusahaan) {
            // Get area patrols for this perusahaan
            $areaPatrols = AreaPatrol::where('perusahaan_id', $perusahaan->id)->get();
            
            if ($areaPatrols->isEmpty()) {
                continue;
            }

            foreach ($areaPatrols as $area) {
                $rutes = [
                    [
                        'nama' => 'Rute Pagi - Zona Produksi',
                        'deskripsi' => 'Patroli pagi hari di zona produksi',
                        'estimasi_waktu' => 45,
                        'is_active' => true,
                    ],
                    [
                        'nama' => 'Rute Siang - Zona Kantor',
                        'deskripsi' => 'Patroli siang hari di area kantor',
                        'estimasi_waktu' => 30,
                        'is_active' => true,
                    ],
                    [
                        'nama' => 'Rute Malam - Keliling Lengkap',
                        'deskripsi' => 'Patroli malam keliling semua area',
                        'estimasi_waktu' => 60,
                        'is_active' => true,
                    ],
                ];

                foreach ($rutes as $rute) {
                    RutePatrol::create([
                        'perusahaan_id' => $perusahaan->id,
                        'area_patrol_id' => $area->id,
                        'nama' => $rute['nama'],
                        'deskripsi' => $rute['deskripsi'],
                        'estimasi_waktu' => $rute['estimasi_waktu'],
                        'is_active' => $rute['is_active'],
                    ]);
                }
            }
        }
    }
}
