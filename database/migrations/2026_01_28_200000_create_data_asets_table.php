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
        Schema::create('data_asets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('kode_aset')->unique();
            $table->string('nama_aset');
            $table->string('kategori'); // IT, Furnitur, dll - bisa ditambah dinamis
            $table->date('tanggal_beli');
            $table->decimal('harga_beli', 15, 2);
            $table->decimal('nilai_penyusutan', 15, 2)->default(0);
            $table->string('pic_penanggung_jawab'); // Nama PIC
            $table->string('foto_aset')->nullable();
            $table->text('catatan_tambahan')->nullable();
            $table->enum('status', ['ada', 'rusak', 'dijual', 'dihapus'])->default('ada');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'kategori']);
            $table->index(['perusahaan_id', 'status']);
            $table->index(['created_by', 'created_at']);
            $table->index('kode_aset');
            $table->index('tanggal_beli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_asets');
    }
};