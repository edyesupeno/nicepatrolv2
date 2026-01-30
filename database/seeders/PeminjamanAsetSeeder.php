<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PeminjamanAset;
use App\Models\DataAset;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\User;
use Carbon\Carbon;

class PeminjamanAsetSeeder extends Seeder
{
    public function run(): void
    {
        // Get sample data
        $dataAsets = DataAset::take(5)->get();
        $projects = Project::take(3)->get();
        $karyawans = Karyawan::where('is_active', true)->take(3)->get();
        $users = User::where('role', '!=', 'superadmin')->take(2)->get();

        if ($dataAsets->isEmpty() || $projects->isEmpty()) {
            $this->command->warn('Tidak ada data aset atau project. Jalankan DataAsetSeeder dan seeder lainnya terlebih dahulu.');
            return;
        }

        $peminjamans = [
            // Peminjaman Pending
            [
                'perusahaan_id' => 1, // Assuming perusahaan ID 1
                'project_id' => $projects->first()->id,
                'data_aset_id' => $dataAsets->first()->id,
                'peminjam_karyawan_id' => $karyawans->first()->id ?? null,
                'peminjam_user_id' => null,
                'created_by' => 1, // Assuming admin user ID 1
                'tanggal_peminjaman' => Carbon::today(),
                'tanggal_rencana_kembali' => Carbon::today()->addDays(7),
                'jumlah_dipinjam' => 1,
                'status_peminjaman' => 'pending',
                'keperluan' => 'Untuk keperluan maintenance rutin peralatan kantor',
                'kondisi_saat_dipinjam' => 'baik',
                'catatan_peminjaman' => 'Peminjaman untuk project maintenance bulanan',
            ],

            // Peminjaman Approved
            [
                'perusahaan_id' => 1,
                'project_id' => $projects->skip(1)->first()->id,
                'data_aset_id' => $dataAsets->skip(1)->first()->id,
                'peminjam_user_id' => $users->first()->id ?? null,
                'peminjam_karyawan_id' => null,
                'created_by' => 1,
                'tanggal_peminjaman' => Carbon::today()->addDay(),
                'tanggal_rencana_kembali' => Carbon::today()->addDays(5),
                'jumlah_dipinjam' => 2,
                'status_peminjaman' => 'approved',
                'keperluan' => 'Untuk keperluan training karyawan baru',
                'kondisi_saat_dipinjam' => 'baik',
                'catatan_peminjaman' => 'Digunakan untuk sesi training 3 hari',
                'approved_at' => Carbon::now(),
                'approved_by' => 1,
            ],

            // Peminjaman Sedang Dipinjam
            [
                'perusahaan_id' => 1,
                'project_id' => $projects->first()->id,
                'data_aset_id' => $dataAsets->skip(2)->first()->id,
                'peminjam_karyawan_id' => $karyawans->skip(1)->first()->id ?? null,
                'peminjam_user_id' => null,
                'created_by' => 1,
                'tanggal_peminjaman' => Carbon::today()->subDays(3),
                'tanggal_rencana_kembali' => Carbon::today()->addDays(4),
                'jumlah_dipinjam' => 1,
                'status_peminjaman' => 'dipinjam',
                'keperluan' => 'Untuk keperluan instalasi sistem keamanan',
                'kondisi_saat_dipinjam' => 'baik',
                'catatan_peminjaman' => 'Peminjaman untuk project instalasi CCTV',
                'approved_at' => Carbon::now()->subDays(3),
                'approved_by' => 1,
                'borrowed_at' => Carbon::now()->subDays(3),
            ],

            // Peminjaman Terlambat
            [
                'perusahaan_id' => 1,
                'project_id' => $projects->skip(2)->first()->id,
                'data_aset_id' => $dataAsets->skip(3)->first()->id,
                'peminjam_karyawan_id' => $karyawans->skip(2)->first()->id ?? null,
                'peminjam_user_id' => null,
                'created_by' => 1,
                'tanggal_peminjaman' => Carbon::today()->subDays(10),
                'tanggal_rencana_kembali' => Carbon::today()->subDays(3),
                'jumlah_dipinjam' => 1,
                'status_peminjaman' => 'dipinjam',
                'keperluan' => 'Untuk keperluan perbaikan sistem jaringan',
                'kondisi_saat_dipinjam' => 'baik',
                'catatan_peminjaman' => 'Peminjaman untuk troubleshooting network',
                'approved_at' => Carbon::now()->subDays(10),
                'approved_by' => 1,
                'borrowed_at' => Carbon::now()->subDays(10),
            ],

            // Peminjaman Sudah Dikembalikan
            [
                'perusahaan_id' => 1,
                'project_id' => $projects->first()->id,
                'data_aset_id' => $dataAsets->skip(4)->first()->id,
                'peminjam_user_id' => $users->skip(1)->first()->id ?? null,
                'peminjam_karyawan_id' => null,
                'created_by' => 1,
                'tanggal_peminjaman' => Carbon::today()->subDays(15),
                'tanggal_rencana_kembali' => Carbon::today()->subDays(8),
                'tanggal_kembali_aktual' => Carbon::today()->subDays(7),
                'jumlah_dipinjam' => 1,
                'status_peminjaman' => 'dikembalikan',
                'keperluan' => 'Untuk keperluan audit internal',
                'kondisi_saat_dipinjam' => 'baik',
                'kondisi_saat_dikembalikan' => 'baik',
                'catatan_peminjaman' => 'Peminjaman untuk proses audit',
                'catatan_pengembalian' => 'Aset dikembalikan dalam kondisi baik',
                'approved_at' => Carbon::now()->subDays(15),
                'approved_by' => 1,
                'borrowed_at' => Carbon::now()->subDays(15),
                'returned_at' => Carbon::now()->subDays(7),
                'returned_by' => 1,
            ],

            // Peminjaman Akan Jatuh Tempo
            [
                'perusahaan_id' => 1,
                'project_id' => $projects->skip(1)->first()->id,
                'data_aset_id' => $dataAsets->first()->id,
                'peminjam_karyawan_id' => $karyawans->first()->id ?? null,
                'peminjam_user_id' => null,
                'created_by' => 1,
                'tanggal_peminjaman' => Carbon::today()->subDays(5),
                'tanggal_rencana_kembali' => Carbon::today()->addDays(2),
                'jumlah_dipinjam' => 1,
                'status_peminjaman' => 'dipinjam',
                'keperluan' => 'Untuk keperluan presentasi client',
                'kondisi_saat_dipinjam' => 'baik',
                'catatan_peminjaman' => 'Peminjaman untuk meeting dengan client penting',
                'approved_at' => Carbon::now()->subDays(5),
                'approved_by' => 1,
                'borrowed_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($peminjamans as $peminjamanData) {
            // Skip if required relationships don't exist
            if (!$peminjamanData['peminjam_karyawan_id'] && !$peminjamanData['peminjam_user_id']) {
                continue;
            }

            PeminjamanAset::create($peminjamanData);
        }

        $this->command->info('PeminjamanAsetSeeder completed successfully!');
    }
}