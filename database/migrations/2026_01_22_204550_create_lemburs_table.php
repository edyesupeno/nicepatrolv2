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
        Schema::create('lemburs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Detail Lembur
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('total_jam', 5, 2); // Total jam lembur (misal: 3.5 jam)
            $table->text('alasan_lembur');
            $table->text('deskripsi_pekerjaan');
            
            // Status Approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('catatan_approval')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            // Perhitungan Upah
            $table->decimal('tarif_lembur_per_jam', 10, 2)->nullable(); // Tarif per jam
            $table->decimal('total_upah_lembur', 12, 2)->nullable(); // Total upah yang akan dibayar
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['karyawan_id', 'tanggal_lembur']);
            $table->index(['status', 'tanggal_lembur']);
            $table->index(['perusahaan_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lemburs');
    }
};