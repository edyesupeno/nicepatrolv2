<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkpoint_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('checkpoint_id')->constrained('checkpoints')->onDelete('cascade');
            $table->integer('urutan')->default(0)->comment('Urutan kunjungan checkpoint');
            $table->timestamps();

            $table->unique(['tim_patroli_id', 'checkpoint_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkpoint_tim_patroli');
    }
};
