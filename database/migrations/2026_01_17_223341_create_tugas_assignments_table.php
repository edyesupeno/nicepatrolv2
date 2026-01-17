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
        Schema::create('tugas_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Assignment status
            $table->enum('status', ['assigned', 'in_progress', 'completed', 'rejected'])->default('assigned');
            $table->text('notes')->nullable(); // Notes from assignee
            $table->json('attachments')->nullable(); // File attachments
            
            // Progress tracking
            $table->integer('progress_percentage')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['tugas_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['tugas_id', 'user_id']); // Composite for uniqueness check
            $table->index('completed_at');
            $table->index('started_at');
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['tugas_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_assignments');
    }
};