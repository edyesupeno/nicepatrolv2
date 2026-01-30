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
        Schema::create('perlengkapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('kode_perlengkapan')->unique();
            $table->string('nama_perlengkapan');
            $table->enum('kategori', ['Elektronik', 'Peralatan', 'Kendaraan', 'Furniture', 'Lainnya']);
            $table->string('merk')->nullable();
            $table->string('model')->nullable();
            $table->integer('tahun_pembelian')->nullable();
            $table->decimal('harga_pembelian', 15, 2)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak', 'Maintenance']);
            $table->string('lokasi_penyimpanan')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('foto_perlengkapan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'kategori']);
            $table->index(['perusahaan_id', 'kondisi']);
            $table->index(['created_by', 'created_at']);
            $table->index('kode_perlengkapan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perlengkapans');
    }
};
