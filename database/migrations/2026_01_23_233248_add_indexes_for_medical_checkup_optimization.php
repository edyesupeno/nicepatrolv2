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
        Schema::table('karyawans', function (Blueprint $table) {
            // Index untuk query karyawan aktif
            $table->index('is_active', 'idx_karyawans_is_active');
            
            // Composite index untuk filter project dan status aktif
            $table->index(['is_active', 'project_id'], 'idx_karyawans_active_project');
            
            // Index untuk search nama dan NIK
            $table->index('nama_lengkap', 'idx_karyawans_nama_lengkap');
            $table->index('nik_karyawan', 'idx_karyawans_nik_karyawan');
            
            // Composite index untuk multi-tenancy
            $table->index(['perusahaan_id', 'is_active'], 'idx_karyawans_perusahaan_active');
        });

        Schema::table('medical_checkups', function (Blueprint $table) {
            // Index untuk tanggal checkup (untuk sorting dan filtering)
            $table->index('tanggal_checkup', 'idx_medical_checkups_tanggal');
            
            // Composite index untuk karyawan dan tanggal (untuk latest checkup queries)
            $table->index(['karyawan_id', 'tanggal_checkup'], 'idx_medical_checkups_karyawan_tanggal');
            
            // Index untuk foreign key jika belum ada
            if (!Schema::hasIndex('medical_checkups', 'medical_checkups_karyawan_id_foreign')) {
                $table->index('karyawan_id', 'idx_medical_checkups_karyawan_id');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            // Index untuk nama project (untuk sorting dropdown)
            $table->index('nama', 'idx_projects_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropIndex('idx_karyawans_is_active');
            $table->dropIndex('idx_karyawans_active_project');
            $table->dropIndex('idx_karyawans_nama_lengkap');
            $table->dropIndex('idx_karyawans_nik_karyawan');
            $table->dropIndex('idx_karyawans_perusahaan_active');
        });

        Schema::table('medical_checkups', function (Blueprint $table) {
            $table->dropIndex('idx_medical_checkups_tanggal');
            $table->dropIndex('idx_medical_checkups_karyawan_tanggal');
            
            // Only drop if we created it
            if (Schema::hasIndex('medical_checkups', 'idx_medical_checkups_karyawan_id')) {
                $table->dropIndex('idx_medical_checkups_karyawan_id');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_nama');
        });
    }
};