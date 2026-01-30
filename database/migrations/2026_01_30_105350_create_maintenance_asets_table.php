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
        Schema::create('maintenance_asets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Informasi Maintenance
            $table->string('nomor_maintenance')->unique();
            $table->enum('asset_type', ['data_aset', 'aset_kendaraan']);
            $table->unsignedBigInteger('asset_id');
            $table->enum('jenis_maintenance', ['preventive', 'corrective', 'predictive']);
            
            // Jadwal
            $table->date('tanggal_maintenance');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->integer('estimasi_durasi')->nullable(); // dalam menit
            
            // Detail Maintenance
            $table->text('deskripsi_pekerjaan');
            $table->text('catatan_sebelum')->nullable();
            $table->text('catatan_sesudah')->nullable();
            
            // Teknisi/Vendor
            $table->string('teknisi_internal')->nullable(); // nama teknisi internal
            $table->string('vendor_eksternal')->nullable(); // nama vendor/bengkel
            $table->string('kontak_vendor')->nullable();
            
            // Biaya
            $table->decimal('biaya_sparepart', 15, 2)->default(0);
            $table->decimal('biaya_jasa', 15, 2)->default(0);
            $table->decimal('biaya_lainnya', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            
            // Status
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('prioritas', ['low', 'medium', 'high', 'urgent'])->default('medium');
            
            // Hasil Maintenance
            $table->enum('hasil_maintenance', ['berhasil', 'sebagian', 'gagal'])->nullable();
            $table->text('masalah_ditemukan')->nullable();
            $table->text('tindakan_dilakukan')->nullable();
            $table->text('rekomendasi')->nullable();
            
            // Reminder
            $table->boolean('reminder_aktif')->default(true);
            $table->integer('reminder_hari')->default(7); // berapa hari sebelum maintenance
            $table->timestamp('reminder_terakhir')->nullable();
            
            // Files
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_sesudah')->nullable();
            $table->string('dokumen_pendukung')->nullable();
            $table->string('invoice_pembayaran')->nullable();
            
            // Next Maintenance
            $table->date('tanggal_maintenance_berikutnya')->nullable();
            $table->integer('interval_maintenance')->nullable(); // dalam hari
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'asset_type', 'asset_id']);
            $table->index(['perusahaan_id', 'status']);
            $table->index(['perusahaan_id', 'tanggal_maintenance']);
            $table->index(['perusahaan_id', 'jenis_maintenance']);
            $table->index(['perusahaan_id', 'prioritas']);
            $table->index('nomor_maintenance');
            $table->index('tanggal_maintenance');
            $table->index('tanggal_maintenance_berikutnya');
            $table->index('reminder_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_asets');
    }
};