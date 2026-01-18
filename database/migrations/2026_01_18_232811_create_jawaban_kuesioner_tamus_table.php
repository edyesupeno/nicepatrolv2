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
        Schema::create('jawaban_kuesioner_tamus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buku_tamu_id')->constrained()->onDelete('cascade');
            $table->foreignId('pertanyaan_tamu_id')->constrained()->onDelete('cascade');
            $table->text('jawaban');
            $table->timestamps();
            
            // Index untuk performance
            $table->index(['buku_tamu_id', 'pertanyaan_tamu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_kuesioner_tamus');
    }
};
