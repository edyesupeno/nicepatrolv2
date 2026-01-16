<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@nicepatrol.id',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'perusahaan_id' => null,
            'is_active' => true,
        ]);

        // Buat Perusahaan ABB
        $abb = Perusahaan::create([
            'nama' => 'PT ABB',
            'kode' => 'ABB',
            'alamat' => 'Jakarta',
            'telepon' => '021-1234567',
            'email' => 'info@abb.co.id',
            'is_active' => true,
        ]);

        // Buat Admin ABB
        User::create([
            'perusahaan_id' => $abb->id,
            'name' => 'Admin ABB',
            'email' => 'abb@nicepatrol.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Buat Perusahaan BSP
        $bsp = Perusahaan::create([
            'nama' => 'PT BSP',
            'kode' => 'BSP',
            'alamat' => 'Bandung',
            'telepon' => '022-7654321',
            'email' => 'info@bsp.co.id',
            'is_active' => true,
        ]);

        // Buat Admin BSP
        User::create([
            'perusahaan_id' => $bsp->id,
            'name' => 'Admin BSP',
            'email' => 'bsp@nicepatrol.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }
}
