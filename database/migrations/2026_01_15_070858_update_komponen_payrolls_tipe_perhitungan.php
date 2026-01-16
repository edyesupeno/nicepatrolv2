<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old constraint
        DB::statement("ALTER TABLE komponen_payrolls DROP CONSTRAINT IF EXISTS komponen_payrolls_tipe_perhitungan_check");
        
        // Change column to varchar
        Schema::table('komponen_payrolls', function (Blueprint $table) {
            $table->string('tipe_perhitungan', 50)->default('Tetap')->change();
        });
        
        // Add new constraint
        DB::statement("ALTER TABLE komponen_payrolls ADD CONSTRAINT komponen_payrolls_tipe_perhitungan_check CHECK (tipe_perhitungan IN ('Tetap', 'Persentase', 'Per Hari Masuk', 'Lembur Per Hari'))");
    }

    public function down(): void
    {
        // Drop the new constraint
        DB::statement("ALTER TABLE komponen_payrolls DROP CONSTRAINT IF EXISTS komponen_payrolls_tipe_perhitungan_check");
        
        // Change back to varchar
        Schema::table('komponen_payrolls', function (Blueprint $table) {
            $table->string('tipe_perhitungan', 50)->default('Tetap')->change();
        });
        
        // Add old constraint
        DB::statement("ALTER TABLE komponen_payrolls ADD CONSTRAINT komponen_payrolls_tipe_perhitungan_check CHECK (tipe_perhitungan IN ('Tetap', 'Persentase'))");
    }
};
