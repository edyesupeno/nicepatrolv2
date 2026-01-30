<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman_asets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_aset_id')->constrained()->onDelete('cascade');
            $table->foreignId('peminjam_karyawan_id')->nullable()->constrained('karyawans')->onDelete('set null');
            $table->foreignId('peminjam_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('returned_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Kode peminjaman auto-generated
            $table->string('kode_peminjaman', 50)->unique();
            
            // Data peminjaman
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_rencana_kembali');
            $table->date('tanggal_kembali_aktual')->nullable();
            $table->integer('jumlah_dipinjam')->default(1);
            
            // Status peminjaman
            $table->enum('status_peminjaman', [
                'pending',      // Menunggu persetujuan
                'approved',     // Disetujui, siap dipinjam
                'dipinjam',     // Sedang dipinjam
                'dikembalikan', // Sudah dikembalikan
                'terlambat',    // Terlambat dikembalikan
                'hilang',       // Aset hilang
                'rusak',        // Aset rusak saat dikembalikan
                'ditolak'       // Peminjaman ditolak
            ])->default('pending');
            
            // Keperluan dan catatan
            $table->text('keperluan')->nullable();
            $table->text('catatan_peminjaman')->nullable();
            $table->text('catatan_pengembalian')->nullable();
            
            // Kondisi aset
            $table->enum('kondisi_saat_dipinjam', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->enum('kondisi_saat_dikembalikan', ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])->nullable();
            
            // File bukti
            $table->string('file_bukti_peminjaman')->nullable();
            $table->string('file_bukti_pengembalian')->nullable();
            
            // Timestamps
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('borrowed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performance
            $table->index(['perusahaan_id', 'status_peminjaman']);
            $table->index(['project_id', 'tanggal_peminjaman']);
            $table->index(['data_aset_id', 'status_peminjaman']);
            $table->index(['peminjam_karyawan_id', 'status_peminjaman']);
            $table->index(['peminjam_user_id', 'status_peminjaman']);
            $table->index(['tanggal_rencana_kembali', 'status_peminjaman']);
            $table->index('kode_peminjaman');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_asets');
    }
};