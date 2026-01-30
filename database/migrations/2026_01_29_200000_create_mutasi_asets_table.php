<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_asets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->string('nomor_mutasi')->unique();
            $table->date('tanggal_mutasi');
            
            // Asset info
            $table->enum('asset_type', ['data_aset', 'aset_kendaraan']);
            $table->unsignedBigInteger('asset_id');
            
            // Pemindahan info
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_asal_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('project_tujuan_id')->constrained('projects')->onDelete('cascade');
            $table->text('keterangan')->nullable();
            $table->text('alasan_mutasi');
            
            // Status
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'selesai'])->default('pending');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->text('catatan_persetujuan')->nullable();
            
            // Dokumen
            $table->string('dokumen_pendukung')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'status']);
            $table->index(['asset_type', 'asset_id']);
            $table->index('tanggal_mutasi');
            $table->index('nomor_mutasi');
            $table->index(['project_asal_id', 'project_tujuan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_asets');
    }
};