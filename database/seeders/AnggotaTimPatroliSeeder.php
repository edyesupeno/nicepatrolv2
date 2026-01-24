<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnggotaTimPatroli;
use App\Models\TimPatroli;
use App\Models\User;
use Carbon\Carbon;

class AnggotaTimPatroliSeeder extends Seeder
{
    public function run(): void
    {
        $timPatrolis = TimPatroli::take(3)->get();
        $securityOfficers = User::where('role', 'security_officer')->take(10)->get();

        if ($timPatrolis->count() > 0 && $securityOfficers->count() >= 6) {
            $userIndex = 0;
            
            foreach ($timPatrolis as $tim) {
                // Add 3-4 members per team (1 leader, 1 wakil leader, 1-2 anggota)
                $memberCount = rand(3, 4);
                
                for ($i = 0; $i < $memberCount && $userIndex < $securityOfficers->count(); $i++) {
                    $user = $securityOfficers[$userIndex];
                    
                    // Assign roles: first = leader (Danru), second = wakil_leader, rest = anggota
                    $role = match($i) {
                        0 => 'leader',
                        1 => 'wakil_leader',
                        default => 'anggota'
                    };
                    
                    AnggotaTimPatroli::create([
                        'tim_patroli_id' => $tim->id,
                        'user_id' => $user->id,
                        'role' => $role,
                        'tanggal_bergabung' => Carbon::now()->subDays(rand(30, 90)),
                        'is_active' => true,
                        'catatan' => 'Anggota tim patroli ' . $tim->nama_tim,
                    ]);
                    
                    $userIndex++;
                }
            }
        }
    }
}