<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashAdvance;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\User;

class CashAdvanceSeeder extends Seeder
{
    public function run(): void
    {
        // Get first project and karyawan for testing
        $project = Project::first();
        $karyawan = Karyawan::where('project_id', $project->id)->first();
        $user = User::where('role', 'admin_perusahaan')->first();

        if (!$project || !$karyawan || !$user) {
            $this->command->info('Skipping CashAdvanceSeeder - missing required data');
            return;
        }

        // Create sample Cash Advance
        $cashAdvance = CashAdvance::create([
            'perusahaan_id' => $project->perusahaan_id,
            'project_id' => $project->id,
            'karyawan_id' => $karyawan->id,
            'jumlah_pengajuan' => 5000000, // 5 juta
            'keperluan' => 'Cash Advance untuk keperluan operasional project ' . $project->nama . ' selama 1 bulan. Meliputi biaya transportasi, konsumsi, dan keperluan mendadak lainnya.',
            'tanggal_pengajuan' => now(),
            'batas_pertanggungjawaban' => now()->addDays(30),
            'status' => 'pending',
        ]);

        $this->command->info("Created Cash Advance: {$cashAdvance->nomor_ca}");

        // Create another one that's approved
        $karyawan2 = Karyawan::where('project_id', $project->id)
            ->where('id', '!=', $karyawan->id)
            ->first();

        if ($karyawan2) {
            $cashAdvance2 = CashAdvance::create([
                'perusahaan_id' => $project->perusahaan_id,
                'project_id' => $project->id,
                'karyawan_id' => $karyawan2->id,
                'jumlah_pengajuan' => 3000000, // 3 juta
                'keperluan' => 'Cash Advance untuk keperluan pembelian supplies dan maintenance equipment.',
                'tanggal_pengajuan' => now()->subDays(2),
                'batas_pertanggungjawaban' => now()->addDays(28),
                'status' => 'approved',
                'approved_by' => $user->id,
                'tanggal_approved' => now()->subDay(),
                'catatan_approval' => 'Disetujui untuk keperluan operasional.',
            ]);

            $this->command->info("Created approved Cash Advance: {$cashAdvance2->nomor_ca}");
        }
    }
}