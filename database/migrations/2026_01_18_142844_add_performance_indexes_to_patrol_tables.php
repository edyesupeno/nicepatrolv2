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
        // Add indexes for patrolis table
        Schema::table('patrolis', function (Blueprint $table) {
            $table->index(['perusahaan_id', 'waktu_mulai'], 'idx_patrolis_perusahaan_waktu');
            $table->index(['status', 'waktu_mulai'], 'idx_patrolis_status_waktu');
            $table->index(['user_id', 'waktu_mulai'], 'idx_patrolis_user_waktu');
        });

        // Add indexes for patroli_details table
        Schema::table('patroli_details', function (Blueprint $table) {
            $table->index(['checkpoint_id', 'waktu_scan'], 'idx_patroli_details_checkpoint_waktu');
            $table->index(['patroli_id', 'checkpoint_id'], 'idx_patroli_details_patroli_checkpoint');
        });

        // Add indexes for aset_checks table
        Schema::table('aset_checks', function (Blueprint $table) {
            $table->index(['patroli_detail_id', 'status'], 'idx_aset_checks_detail_status');
            $table->index(['status', 'created_at'], 'idx_aset_checks_status_created');
        });

        // Add indexes for checkpoints table
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->index(['rute_patrol_id', 'is_active'], 'idx_checkpoints_rute_active');
            $table->index(['perusahaan_id', 'is_active'], 'idx_checkpoints_perusahaan_active');
        });

        // Add indexes for rute_patrols table
        Schema::table('rute_patrols', function (Blueprint $table) {
            $table->index(['area_patrol_id', 'is_active'], 'idx_rute_patrols_area_active');
            $table->index(['perusahaan_id', 'is_active'], 'idx_rute_patrols_perusahaan_active');
        });

        // Add indexes for area_patrols table
        Schema::table('area_patrols', function (Blueprint $table) {
            $table->index(['perusahaan_id', 'is_active'], 'idx_area_patrols_perusahaan_active');
            $table->index(['project_id', 'is_active'], 'idx_area_patrols_project_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patrolis', function (Blueprint $table) {
            $table->dropIndex('idx_patrolis_perusahaan_waktu');
            $table->dropIndex('idx_patrolis_status_waktu');
            $table->dropIndex('idx_patrolis_user_waktu');
        });

        Schema::table('patroli_details', function (Blueprint $table) {
            $table->dropIndex('idx_patroli_details_checkpoint_waktu');
            $table->dropIndex('idx_patroli_details_patroli_checkpoint');
        });

        Schema::table('aset_checks', function (Blueprint $table) {
            $table->dropIndex('idx_aset_checks_detail_status');
            $table->dropIndex('idx_aset_checks_status_created');
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropIndex('idx_checkpoints_rute_active');
            $table->dropIndex('idx_checkpoints_perusahaan_active');
        });

        Schema::table('rute_patrols', function (Blueprint $table) {
            $table->dropIndex('idx_rute_patrols_area_active');
            $table->dropIndex('idx_rute_patrols_perusahaan_active');
        });

        Schema::table('area_patrols', function (Blueprint $table) {
            $table->dropIndex('idx_area_patrols_perusahaan_active');
            $table->dropIndex('idx_area_patrols_project_active');
        });
    }
};