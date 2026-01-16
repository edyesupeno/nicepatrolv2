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
        Schema::table('payrolls', function (Blueprint $table) {
            // Composite index untuk filter yang sering digunakan
            $table->index(['perusahaan_id', 'periode', 'status'], 'payrolls_perusahaan_periode_status_index');
            $table->index(['project_id', 'periode', 'status'], 'payrolls_project_periode_status_index');
            
            // Index untuk created_at (untuk ORDER BY)
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropIndex('payrolls_perusahaan_periode_status_index');
            $table->dropIndex('payrolls_project_periode_status_index');
            $table->dropIndex(['created_at']);
        });
    }
};
