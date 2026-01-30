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
        Schema::create('penyerahan_perlengkapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('kategori_perlengkapan_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('nomor_penyerahan')->unique(); // PP202601270001
            $table->date('tanggal_penyerahan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'diserahkan', 'dikembalikan'])->default('draft');
            $table->date('tanggal_pengembalian')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['perusahaan_id', 'status']);
            $table->index(['karyawan_id', 'status']);
            $table->index(['tanggal_penyerahan', 'status']);
            $table->index('nomor_penyerahan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyerahan_perlengkapans');
    }
};
