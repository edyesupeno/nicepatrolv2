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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('kode_shift', 10); // SP, SS, SM, dll
            $table->string('nama_shift', 100); // Shift Pagi, Shift Siang, dll
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('durasi_istirahat')->default(60); // dalam menit
            $table->integer('toleransi_keterlambatan')->default(15); // dalam menit
            $table->text('deskripsi')->nullable();
            $table->timestamps();
            
            // Kode shift harus unik per project
            $table->unique(['project_id', 'kode_shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
