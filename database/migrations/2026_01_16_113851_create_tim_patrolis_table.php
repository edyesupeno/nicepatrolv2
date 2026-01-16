<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tim_patrolis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('nama_tim');
            $table->enum('shift', ['pagi', 'siang', 'malam'])->default('pagi');
            $table->foreignId('leader_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['perusahaan_id', 'is_active']);
        });

        // Pivot table untuk area tanggung jawab
        Schema::create('area_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('area_patrol_id')->constrained('area_patrols')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table untuk rute patroli
        Schema::create('rute_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('rute_patrol_id')->constrained('rute_patrols')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table untuk inventaris
        Schema::create('inventaris_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('inventaris_patroli_id')->constrained('inventaris_patrolis')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table untuk kuesioner
        Schema::create('kuesioner_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('kuesioner_patroli_id')->constrained('kuesioner_patrolis')->onDelete('cascade');
            $table->timestamps();
        });

        // Pivot table untuk pemeriksaan
        Schema::create('pemeriksaan_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('pemeriksaan_patroli_id')->constrained('pemeriksaan_patrolis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemeriksaan_tim_patroli');
        Schema::dropIfExists('kuesioner_tim_patroli');
        Schema::dropIfExists('inventaris_tim_patroli');
        Schema::dropIfExists('rute_tim_patroli');
        Schema::dropIfExists('area_tim_patroli');
        Schema::dropIfExists('tim_patrolis');
    }
};
