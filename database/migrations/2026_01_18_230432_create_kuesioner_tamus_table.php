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
        Schema::create('kuesioner_tamus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_patrol_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performance
            $table->index(['perusahaan_id', 'is_active']);
            $table->index(['project_id', 'area_patrol_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuesioner_tamus');
    }
};
