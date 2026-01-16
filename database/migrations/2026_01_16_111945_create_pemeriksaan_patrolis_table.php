<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemeriksaan_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->enum('frekuensi', ['harian', 'mingguan', 'bulanan'])->default('harian');
            $table->date('pemeriksaan_terakhir')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['perusahaan_id', 'is_active']);
            $table->index(['perusahaan_id', 'frekuensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_patrolis');
    }
};
