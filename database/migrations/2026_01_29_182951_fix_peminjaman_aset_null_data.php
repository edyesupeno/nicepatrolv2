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
        // Fix records that have null peminjam_karyawan_id
        // These records should be deleted or fixed manually by admin
        
        // Option 1: Delete records with null peminjam_karyawan_id (safer)
        $deletedCount = DB::table('peminjaman_asets')
            ->whereNull('peminjam_karyawan_id')
            ->delete();
            
        if ($deletedCount > 0) {
            echo "Deleted {$deletedCount} peminjaman records with null peminjam_karyawan_id\n";
        }
        
        // Fix records that have null aset references
        $fixedAsetCount = DB::table('peminjaman_asets')
            ->where(function($query) {
                $query->where('aset_type', 'data_aset')
                      ->whereNull('data_aset_id');
            })
            ->orWhere(function($query) {
                $query->where('aset_type', 'aset_kendaraan')
                      ->whereNull('aset_kendaraan_id');
            })
            ->delete();
            
        if ($fixedAsetCount > 0) {
            echo "Deleted {$fixedAsetCount} peminjaman records with invalid aset references\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse deletion
    }
};