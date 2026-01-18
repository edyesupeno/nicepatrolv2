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
        Schema::create('penerimaan_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->string('nomor_penerimaan')->unique();
            $table->string('nama_barang');
            $table->enum('kategori_barang', ['Dokumen', 'Material', 'Elektronik', 'Logistik', 'Lainnya']);
            $table->integer('jumlah_barang')->unsigned();
            $table->string('satuan', 50);
            $table->enum('kondisi_barang', ['Baik', 'Rusak', 'Segel Terbuka']);
            $table->enum('pengirim', ['Kurir', 'Client', 'Lainnya']);
            $table->string('tujuan_departemen');
            $table->string('foto_barang')->nullable();
            $table->datetime('tanggal_terima');
            $table->string('status')->default('Diterima');
            $table->string('petugas_penerima');
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes untuk optimasi query (sesuai database optimization standards)
            $table->index(['perusahaan_id', 'tanggal_terima']);
            $table->index(['perusahaan_id', 'kategori_barang']);
            $table->index(['perusahaan_id', 'kondisi_barang']);
            $table->index(['perusahaan_id', 'status']);
            $table->index('nomor_penerimaan');
            $table->index('deleted_at'); // Index untuk soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_barangs');
    }
};
