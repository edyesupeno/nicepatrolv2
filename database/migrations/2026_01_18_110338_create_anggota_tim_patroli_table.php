<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anggota_tim_patroli', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tim_patroli_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['anggota', 'wakil_leader'])->default('anggota');
            $table->date('tanggal_bergabung');
            $table->date('tanggal_keluar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Unique constraint - satu user hanya bisa jadi anggota satu tim aktif
            $table->unique(['tim_patroli_id', 'user_id']);
            
            // Index untuk query yang sering digunakan
            $table->index(['tim_patroli_id', 'is_active']);
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anggota_tim_patroli');
    }
};