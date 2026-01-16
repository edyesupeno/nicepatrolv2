<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->string('jenjang_pendidikan'); // SD, SMP, SMA, D3, S1, S2, S3
            $table->string('nama_institusi');
            $table->string('jurusan')->nullable();
            $table->string('ipk')->nullable();
            $table->year('tahun_mulai');
            $table->year('tahun_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendidikans');
    }
};
