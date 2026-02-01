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
        Schema::create('transaksi_rekenings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('rekening_id')->constrained('rekenings')->onDelete('cascade');
            $table->string('nomor_transaksi')->unique();
            $table->date('tanggal_transaksi');
            $table->enum('jenis_transaksi', ['debit', 'kredit']);
            $table->decimal('jumlah', 15, 2);
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            $table->string('kategori_transaksi'); // transfer_masuk, transfer_keluar, pembayaran, penerimaan, dll
            $table->text('keterangan');
            $table->string('referensi')->nullable(); // Nomor referensi eksternal (cek, transfer, dll)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User yang melakukan transaksi
            $table->json('metadata')->nullable(); // Data tambahan (bank tujuan, dll)
            $table->boolean('is_verified')->default(false); // Verifikasi transaksi
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'rekening_id']);
            $table->index(['tanggal_transaksi', 'rekening_id']);
            $table->index(['jenis_transaksi', 'rekening_id']);
            $table->index(['kategori_transaksi', 'rekening_id']);
            $table->index('nomor_transaksi');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_rekenings');
    }
};