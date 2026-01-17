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
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            
            // Basic task info
            $table->string('judul');
            $table->text('deskripsi');
            $table->enum('prioritas', ['low', 'medium', 'high'])->default('medium');
            $table->date('batas_pengerjaan');
            $table->text('detail_lokasi')->nullable();
            
            // Target assignment
            $table->enum('target_type', ['all', 'area', 'jabatan', 'specific_users'])->default('all');
            $table->json('target_data')->nullable(); // Store jabatan_ids or user_ids
            
            // Status and flags
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('active');
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_active')->default(true);
            
            // Timestamps
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'is_active']);
            $table->index(['project_id', 'status']);
            $table->index(['created_by', 'created_at']);
            $table->index(['batas_pengerjaan', 'status']);
            $table->index(['prioritas', 'is_urgent']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};