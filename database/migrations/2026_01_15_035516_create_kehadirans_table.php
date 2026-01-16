<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadirans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();
            $table->string('lokasi_masuk')->nullable(); // lat,long
            $table->string('lokasi_keluar')->nullable(); // lat,long
            $table->enum('status', ['hadir', 'terlambat', 'pulang_cepat', 'alpa', 'izin', 'sakit', 'cuti'])->default('hadir');
            $table->text('keterangan')->nullable();
            $table->integer('durasi_kerja')->nullable(); // dalam menit
            $table->boolean('on_radius')->default(true); // apakah absen dalam radius lokasi
            $table->timestamps();
            
            // Unique constraint: 1 karyawan hanya bisa 1 kehadiran per tanggal
            $table->unique(['karyawan_id', 'tanggal']);
            
            // Index untuk query cepat
            $table->index(['perusahaan_id', 'tanggal']);
            $table->index(['project_id', 'tanggal']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadirans');
    }
};
