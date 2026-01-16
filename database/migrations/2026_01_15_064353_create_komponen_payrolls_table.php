<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('komponen_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama_komponen');
            $table->string('kode')->unique();
            $table->enum('jenis', ['Tunjangan', 'Potongan'])->default('Tunjangan');
            $table->enum('kategori', ['Fixed', 'Variable'])->default('Fixed');
            $table->enum('tipe_perhitungan', ['Tetap', 'Persentase'])->default('Tetap');
            $table->decimal('nilai', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->boolean('kena_pajak')->default(false);
            $table->boolean('boleh_edit')->default(true);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            
            $table->index('perusahaan_id');
            $table->index('kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('komponen_payrolls');
    }
};
