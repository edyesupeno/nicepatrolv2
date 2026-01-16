<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_checkups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            
            // Informasi Dasar
            $table->string('jenis_checkup');
            $table->date('tanggal_checkup');
            $table->string('status_kesehatan');
            
            // Pengukuran Fisik
            $table->decimal('tinggi_badan', 5, 2)->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->string('golongan_darah')->nullable();
            $table->string('tekanan_darah')->nullable();
            
            // Hasil Lab
            $table->decimal('gula_darah', 6, 2)->nullable();
            $table->decimal('kolesterol', 6, 2)->nullable();
            
            // Informasi Medis
            $table->string('rumah_sakit')->nullable();
            $table->string('nama_dokter')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('catatan_tambahan')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_checkups');
    }
};
