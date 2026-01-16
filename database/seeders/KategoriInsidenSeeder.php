<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KategoriInsiden;
use App\Models\Project;
use App\Models\Perusahaan;

class KategoriInsidenSeeder extends Seeder
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

            $kategoris = [
                [
                    'nama' => 'Kerusakan Fasilitas',
                    'deskripsi' => 'Kerusakan pada fasilitas umum seperti lampu, pintu, jendela, dll',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Gangguan Keamanan',
                    'deskripsi' => 'Insiden terkait keamanan seperti pencurian, vandalisme, dll',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Kebersihan',
                    'deskripsi' => 'Masalah kebersihan dan sanitasi area',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Kecelakaan',
                    'deskripsi' => 'Kecelakaan kerja atau insiden yang menyebabkan cedera',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Kebakaran',
                    'deskripsi' => 'Insiden kebakaran atau potensi bahaya kebakaran',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Bencana Alam',
                    'deskripsi' => 'Bencana alam seperti banjir, gempa, angin kencang, dll',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Gangguan Listrik',
                    'deskripsi' => 'Masalah kelistrikan seperti mati lampu, korsleting, dll',
                    'is_active' => true,
                ],
                [
                    'nama' => 'Lain-lain',
                    'deskripsi' => 'Insiden lain yang tidak termasuk kategori di atas',
                    'is_active' => false,
                ],
            ];

            foreach ($kategoris as $kategori) {
                $kategoriInsiden = KategoriInsiden::create([
                    'perusahaan_id' => $perusahaan->id,
                    'nama' => $kategori['nama'],
                    'deskripsi' => $kategori['deskripsi'],
                    'is_active' => $kategori['is_active'],
                ]);

                // Attach to all projects
                $kategoriInsiden->projects()->attach($projects->pluck('id'));
            }
        }
    }
}
