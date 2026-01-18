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
        Schema::create('pertanyaan_tamus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_tamu_id')->constrained()->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->text('pertanyaan');
            $table->enum('tipe_jawaban', ['pilihan', 'text']);
            $table->json('opsi_jawaban')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
            
            // Index untuk performance
            $table->index(['kuesioner_tamu_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_tamus');
    }
};
