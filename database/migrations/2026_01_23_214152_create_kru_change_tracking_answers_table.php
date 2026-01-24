<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kru_change_tracking_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kru_change_id')->constrained()->onDelete('cascade');
            $table->enum('tipe_tracking', ['kuesioner', 'pemeriksaan']);
            $table->unsignedBigInteger('tracking_id'); // kuesioner_id or pemeriksaan_id
            $table->unsignedBigInteger('pertanyaan_id');
            $table->text('jawaban');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Indexes
            $table->index(['kru_change_id', 'tipe_tracking']);
            $table->index(['tracking_id', 'tipe_tracking']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kru_change_tracking_answers');
    }
};
