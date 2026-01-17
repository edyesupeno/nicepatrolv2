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
        Schema::create('atensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained('areas')->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['low', 'medium', 'high'])->default('medium');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            
            // Target audience
            $table->enum('target_type', ['all', 'area', 'jabatan', 'specific_users'])->default('all');
            $table->json('target_data')->nullable(); // Store specific user IDs, jabatan IDs, etc.
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_urgent')->default(false);
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();

            // Index untuk performance
            $table->index(['project_id', 'is_active']);
            $table->index(['perusahaan_id', 'prioritas']);
            $table->index(['area_id', 'is_active']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atensis');
    }
};