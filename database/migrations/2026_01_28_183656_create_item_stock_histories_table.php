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
        Schema::create('item_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_perlengkapan_id')->constrained('item_perlengkapans')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->enum('tipe_transaksi', ['masuk', 'keluar', 'adjustment', 'return']);
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('keterangan');
            $table->string('referensi_tipe')->nullable(); // 'penyerahan', 'manual', 'return', etc
            $table->unsignedBigInteger('referensi_id')->nullable(); // ID of related record
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['item_perlengkapan_id', 'created_at']);
            $table->index(['perusahaan_id', 'created_at']);
            $table->index(['tipe_transaksi', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stock_histories');
    }
};