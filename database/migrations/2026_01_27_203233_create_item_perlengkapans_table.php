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
        Schema::create('item_perlengkapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_perlengkapan_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('nama_item'); // Contoh: "Seragam", "Topi", "Kacamata"
            $table->text('deskripsi')->nullable();
            $table->string('satuan')->default('Pcs'); // Pcs, Set, Pasang, dll
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_minimum')->default(0); // Alert jika stok di bawah ini
            $table->decimal('harga_satuan', 15, 2)->nullable();
            $table->string('foto_item')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['kategori_perlengkapan_id', 'is_active']);
            $table->index(['stok_tersedia', 'stok_minimum']); // For low stock alerts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_perlengkapans');
    }
};
