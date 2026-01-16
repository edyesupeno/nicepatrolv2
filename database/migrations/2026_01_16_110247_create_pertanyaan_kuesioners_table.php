<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pertanyaan_kuesioners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kuesioner_patroli_id')->constrained('kuesioner_patrolis')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->text('pertanyaan');
            $table->enum('tipe_jawaban', ['pilihan', 'text'])->default('pilihan');
            // Untuk tipe pilihan: JSON array dengan opsi jawaban, contoh: ["Ya", "Tidak"] atau ["Baik", "Tidak Baik"]
            $table->json('opsi_jawaban')->nullable();
            $table->boolean('is_required')->default(true);
            $table->timestamps();
            
            $table->index(['kuesioner_patroli_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pertanyaan_kuesioners');
    }
};
