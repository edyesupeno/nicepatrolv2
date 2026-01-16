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
        Schema::create('aset_kawasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->string('kode_aset')->unique();
            $table->string('nama');
            $table->string('kategori');
            $table->string('merk')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['perusahaan_id', 'is_active']);
            $table->index('kategori');
            $table->index('kode_aset');
        });
        
        // Pivot table untuk relasi many-to-many dengan checkpoint
        Schema::create('aset_checkpoint', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aset_kawasan_id')->constrained('aset_kawasans')->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained()->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['aset_kawasan_id', 'checkpoint_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aset_checkpoint');
        Schema::dropIfExists('aset_kawasans');
    }
};
