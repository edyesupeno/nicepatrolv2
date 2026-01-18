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
        Schema::create('buku_tamus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('input_by')->constrained('users')->onDelete('cascade'); // Karyawan yang menginput
            
            // Guest information
            $table->string('nama_tamu');
            $table->string('perusahaan_tamu')->nullable();
            $table->string('keperluan');
            $table->string('bertemu')->nullable(); // Siapa yang akan ditemui
            $table->text('foto')->nullable(); // Path to photo
            
            // Visit tracking
            $table->enum('status', ['sedang_berkunjung', 'sudah_keluar'])->default('sedang_berkunjung');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            
            // QR Code for tracking
            $table->string('qr_code')->unique()->nullable();
            
            // Additional info
            $table->text('catatan')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['perusahaan_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['input_by', 'created_at']);
            $table->index(['check_in', 'check_out']);
            $table->index('qr_code');
            $table->index(['nama_tamu', 'perusahaan_tamu']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_tamus');
    }
};