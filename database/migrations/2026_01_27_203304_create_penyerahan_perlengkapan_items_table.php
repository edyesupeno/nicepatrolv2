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
        Schema::create('penyerahan_perlengkapan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyerahan_perlengkapan_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_perlengkapan_id')->constrained()->onDelete('cascade');
            
            $table->integer('jumlah_diserahkan');
            $table->integer('jumlah_dikembalikan')->default(0);
            $table->text('keterangan_item')->nullable();
            $table->enum('kondisi_saat_diserahkan', ['Baik', 'Rusak', 'Bekas'])->default('Baik');
            $table->enum('kondisi_saat_dikembalikan', ['Baik', 'Rusak', 'Hilang'])->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['penyerahan_perlengkapan_id', 'item_perlengkapan_id'], 'penyerahan_item_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyerahan_perlengkapan_items');
    }
};
