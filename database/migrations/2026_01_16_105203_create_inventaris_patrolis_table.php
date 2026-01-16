<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama');
            $table->string('kategori');
            $table->string('foto')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['perusahaan_id', 'kategori']);
            $table->index(['perusahaan_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_patrolis');
    }
};
