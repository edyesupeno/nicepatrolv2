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
        // Drop the check constraint for pengirim column
        DB::statement('ALTER TABLE penerimaan_barangs DROP CONSTRAINT IF EXISTS penerimaan_barangs_pengirim_check');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the check constraint (if needed for rollback)
        DB::statement("ALTER TABLE penerimaan_barangs ADD CONSTRAINT penerimaan_barangs_pengirim_check CHECK (pengirim IN ('Kurir', 'Client', 'Lainnya'))");
    }
};