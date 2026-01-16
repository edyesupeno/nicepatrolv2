<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_pemeriksaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemeriksaan_patroli_id')->constrained('pemeriksaan_patrolis')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->text('pertanyaan');
            $table->enum('tipe_jawaban', ['pilihan', 'text'])->default('pilihan');
            $table->json('opsi_jawaban')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            
            $table->index(['pemeriksaan_patroli_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_pemeriksaans');
    }
};
