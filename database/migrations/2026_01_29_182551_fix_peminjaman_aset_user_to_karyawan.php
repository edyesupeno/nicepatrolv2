<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find peminjaman that have peminjam_user_id but no peminjam_karyawan_id
        $peminjamansWithUser = DB::table('peminjaman_asets')
            ->whereNotNull('peminjam_user_id')
            ->whereNull('peminjam_karyawan_id')
            ->get();

        foreach ($peminjamansWithUser as $peminjaman) {
            // Try to find a karyawan with the same user_id (if there's a relationship)
            // For now, we'll just set it to null and let admin fix manually
            // Or we can set it to a default karyawan if needed
            
            // Option 1: Set to null (admin needs to fix manually)
            DB::table('peminjaman_asets')
                ->where('id', $peminjaman->id)
                ->update([
                    'peminjam_user_id' => null,
                    'peminjam_karyawan_id' => null // Will show "Karyawan tidak ditemukan"
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};