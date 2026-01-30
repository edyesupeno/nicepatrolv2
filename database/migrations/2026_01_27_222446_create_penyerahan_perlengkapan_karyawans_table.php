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
        Schema::create('penyerahan_perlengkapan_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyerahan_perlengkapan_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->enum('status_penyerahan', ['belum_diserahkan', 'sebagian_diserahkan', 'sudah_diserahkan'])->default('belum_diserahkan');
            $table->timestamps();
            
            // Prevent duplicate karyawan in same penyerahan
            $table->unique(['penyerahan_perlengkapan_id', 'karyawan_id'], 'unique_penyerahan_karyawan');
            
            // Indexes
            $table->index(['penyerahan_perlengkapan_id', 'status_penyerahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyerahan_perlengkapan_karyawans');
    }
};