<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('komponen_payrolls', function (Blueprint $table) {
            // Project ID - nullable, untuk future use jika diperlukan filter project
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            
            // Nilai maksimal - nullable, hanya digunakan untuk tipe perhitungan "Per Hari"
            $table->decimal('nilai_maksimal', 15, 2)->nullable()->comment('Nilai maksimal per bulan untuk tipe perhitungan per hari');
            
            // Index untuk performance
            $table->index(['project_id', 'aktif']);
            $table->index(['tipe_perhitungan', 'aktif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('komponen_payrolls', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex(['project_id', 'aktif']);
            $table->dropIndex(['tipe_perhitungan', 'aktif']);
            $table->dropColumn(['project_id', 'nilai_maksimal']);
        });
    }
};