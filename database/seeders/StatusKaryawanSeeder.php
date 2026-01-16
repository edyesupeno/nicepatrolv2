<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusKaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['nama' => 'BURUH HARIAN LEPAS'],
            ['nama' => 'KARYAWAN KONTRAK'],
            ['nama' => 'KARYAWAN KONTRAK (TRAINING)'],
            ['nama' => 'KARYAWAN TETAP'],
            ['nama' => 'MAGANG'],
            ['nama' => 'On Job Training'],
        ];

        foreach ($statuses as $status) {
            \App\Models\StatusKaryawan::create($status);
        }
    }
}
