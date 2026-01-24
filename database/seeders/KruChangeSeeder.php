<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KruChange;
use App\Models\TimPatroli;
use App\Models\AreaPatrol;
use App\Models\User;
use App\Models\Shift;
use Carbon\Carbon;

class KruChangeSeeder extends Seeder
{
    public function run(): void
    {
        // Get sample data
        $timPatrolis = TimPatroli::with('shift')->take(4)->get();
        $areaPatrols = AreaPatrol::take(2)->get();
        $users = User::where('role', 'security_officer')->take(4)->get();
        $shifts = Shift::take(2)->get();

        if ($timPatrolis->count() >= 2 && $areaPatrols->count() >= 1 && $users->count() >= 2 && $shifts->count() >= 2) {
            // Create sample kru changes
            KruChange::create([
                'perusahaan_id' => $timPatrolis->first()->perusahaan_id,
                'project_id' => $timPatrolis->first()->project_id,
                'area_patrol_id' => $areaPatrols->first()->id,
                'tim_keluar_id' => $timPatrolis->first()->id,
                'shift_keluar_id' => $timPatrolis->first()->shift_id ?? $shifts->first()->id,
                'tim_masuk_id' => $timPatrolis->skip(1)->first()->id,
                'shift_masuk_id' => $timPatrolis->skip(1)->first()->shift_id ?? $shifts->skip(1)->first()->id,
                'waktu_mulai_handover' => Carbon::now()->addHours(2),
                'status' => 'pending',
                'petugas_keluar_id' => $users->first()->id,
                'petugas_masuk_id' => $users->skip(1)->first()->id,
                'catatan_keluar' => 'Handover shift malam ke shift pagi',
            ]);

            if ($timPatrolis->count() >= 4) {
                KruChange::create([
                    'perusahaan_id' => $timPatrolis->first()->perusahaan_id,
                    'project_id' => $timPatrolis->first()->project_id,
                    'area_patrol_id' => $areaPatrols->first()->id,
                    'tim_keluar_id' => $timPatrolis->skip(2)->first()->id,
                    'shift_keluar_id' => $timPatrolis->skip(2)->first()->shift_id ?? $shifts->first()->id,
                    'tim_masuk_id' => $timPatrolis->skip(3)->first()->id,
                    'shift_masuk_id' => $timPatrolis->skip(3)->first()->shift_id ?? $shifts->skip(1)->first()->id,
                    'waktu_mulai_handover' => Carbon::now()->subHours(1),
                    'waktu_selesai_handover' => Carbon::now(),
                    'status' => 'completed',
                    'petugas_keluar_id' => $users->skip(2)->first()->id,
                    'petugas_masuk_id' => $users->skip(3)->first()->id,
                    'approved_keluar' => true,
                    'approved_masuk' => true,
                    'approved_supervisor' => true,
                    'catatan_keluar' => 'Handover rutin shift siang',
                    'catatan_masuk' => 'Area dalam kondisi normal',
                    'catatan_supervisor' => 'Handover berjalan lancar',
                ]);
            }
        }
    }
}