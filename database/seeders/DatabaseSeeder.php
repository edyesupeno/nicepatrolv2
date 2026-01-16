<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class,
            StatusKaryawanSeeder::class,
            PerusahaanDataSeeder::class,
            PayrollSettingSeeder::class,
            KategoriInsidenSeeder::class,
            AreaPatrolSeeder::class,
            RutePatrolSeeder::class,
        ]);
    }
}
