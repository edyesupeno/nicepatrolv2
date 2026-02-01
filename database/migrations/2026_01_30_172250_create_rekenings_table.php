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
        Schema::create('rekenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('nama_rekening');
            $table->string('nomor_rekening');
            $table->string('nama_bank');
            $table->string('nama_pemilik');
            $table->enum('jenis_rekening', ['operasional', 'payroll', 'investasi', 'emergency', 'lainnya'])->default('operasional');
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);
            $table->string('mata_uang', 3)->default('IDR');
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false); // Rekening utama per project
            $table->string('warna_card', 7)->default('#3B82C8'); // Hex color untuk card
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'is_active']);
            $table->index(['project_id', 'is_primary']);
            $table->index('nomor_rekening');
            
            // Unique constraint: hanya satu rekening primary per project
            $table->unique(['project_id', 'is_primary'], 'unique_primary_per_project');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekenings');
    }
};