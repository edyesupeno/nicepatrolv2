<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patroli_mandiri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('area_patrol_id')->nullable()->constrained('area_patrols')->onDelete('set null');
            $table->foreignId('petugas_id')->constrained('users')->onDelete('cascade');
            
            // Lokasi dan waktu
            $table->string('nama_lokasi');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('maps_url')->nullable();
            $table->datetime('waktu_laporan');
            
            // Status lokasi
            $table->enum('status_lokasi', ['aman', 'tidak_aman'])->default('aman');
            
            // Kondisi jika tidak aman
            $table->enum('jenis_kendala', [
                'kebakaran',
                'aset_rusak', 
                'aset_hilang',
                'orang_mencurigakan',
                'kabel_terbuka',
                'pencurian',
                'sabotase',
                'demo'
            ])->nullable();
            
            // Deskripsi dan catatan
            $table->text('deskripsi_kendala')->nullable();
            $table->text('catatan_petugas')->nullable();
            $table->text('tindakan_yang_diambil')->nullable();
            
            // Foto
            $table->string('foto_lokasi')->nullable(); // Foto wajib untuk semua kondisi
            $table->string('foto_kendala')->nullable(); // Foto tambahan jika tidak aman
            
            // Status laporan
            $table->enum('status_laporan', ['draft', 'submitted', 'reviewed', 'resolved'])->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('reviewed_at')->nullable();
            $table->text('review_catatan')->nullable();
            
            // Prioritas berdasarkan jenis kendala
            $table->enum('prioritas', ['rendah', 'sedang', 'tinggi', 'kritis'])->default('rendah');
            
            $table->timestamps();
            
            // Indexes untuk performance
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['area_patrol_id', 'waktu_laporan']);
            $table->index(['status_lokasi', 'prioritas']);
            $table->index(['petugas_id', 'waktu_laporan']);
            $table->index(['status_laporan', 'waktu_laporan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patroli_mandiri');
    }
};