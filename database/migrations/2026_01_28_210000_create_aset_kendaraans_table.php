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
        Schema::create('aset_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Informasi Dasar Kendaraan
            $table->string('kode_kendaraan')->unique();
            $table->enum('jenis_kendaraan', ['mobil', 'motor']);
            $table->string('merk');
            $table->string('model');
            $table->string('tahun_pembuatan', 4);
            $table->string('warna');
            $table->string('nomor_polisi')->unique();
            $table->string('nomor_rangka')->unique();
            $table->string('nomor_mesin')->unique();
            
            // Informasi Pembelian
            $table->date('tanggal_pembelian');
            $table->decimal('harga_pembelian', 15, 2);
            $table->decimal('nilai_penyusutan', 15, 2)->default(0);
            
            // Dokumen Kendaraan
            $table->string('nomor_stnk');
            $table->date('tanggal_berlaku_stnk');
            $table->string('nomor_bpkb');
            $table->string('atas_nama_bpkb');
            
            // Asuransi
            $table->string('perusahaan_asuransi')->nullable();
            $table->string('nomor_polis_asuransi')->nullable();
            $table->date('tanggal_berlaku_asuransi')->nullable();
            
            // Pajak
            $table->decimal('nilai_pajak_tahunan', 12, 2)->nullable();
            $table->date('jatuh_tempo_pajak')->nullable();
            
            // Maintenance
            $table->integer('kilometer_terakhir')->default(0);
            $table->date('tanggal_service_terakhir')->nullable();
            $table->date('tanggal_service_berikutnya')->nullable();
            
            // Operasional
            $table->string('driver_utama')->nullable();
            $table->string('lokasi_parkir')->nullable();
            $table->enum('status_kendaraan', ['aktif', 'maintenance', 'rusak', 'dijual', 'hilang'])->default('aktif');
            
            // Files
            $table->string('foto_kendaraan')->nullable();
            $table->string('file_stnk')->nullable();
            $table->string('file_bpkb')->nullable();
            $table->string('file_asuransi')->nullable();
            
            // Additional Info
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'jenis_kendaraan']);
            $table->index(['perusahaan_id', 'status_kendaraan']);
            $table->index(['created_by', 'created_at']);
            $table->index('kode_kendaraan');
            $table->index('nomor_polisi');
            $table->index('jatuh_tempo_pajak');
            $table->index('tanggal_berlaku_stnk');
            $table->index('tanggal_berlaku_asuransi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_kendaraans');
    }
};