<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_advance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_advance_id')->constrained()->onDelete('cascade');
            $table->string('nomor_transaksi')->unique(); // CAT-2026-001
            
            $table->enum('tipe', ['pencairan', 'pengeluaran', 'pengembalian']);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_transaksi');
            $table->text('keterangan');
            
            // Untuk pengeluaran
            $table->string('kategori_pengeluaran')->nullable();
            $table->string('bukti_transaksi')->nullable(); // path file
            $table->string('vendor_supplier')->nullable();
            
            // Untuk tracking saldo
            $table->decimal('saldo_sebelum', 15, 2);
            $table->decimal('saldo_sesudah', 15, 2);
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes
            $table->index(['cash_advance_id', 'tipe']);
            $table->index('tanggal_transaksi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_advance_transactions');
    }
};