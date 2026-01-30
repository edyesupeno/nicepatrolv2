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
        // Update existing peminjaman aset data that doesn't have aset_type
        DB::table('peminjaman_asets')
            ->whereNull('aset_type')
            ->whereNotNull('data_aset_id')
            ->update(['aset_type' => 'data_aset']);
            
        // Set default aset_type for any remaining null values
        DB::table('peminjaman_asets')
            ->whereNull('aset_type')
            ->update(['aset_type' => 'data_aset']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration
    }
};