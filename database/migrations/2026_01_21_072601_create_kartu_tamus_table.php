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
        Schema::create('kartu_tamus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->string('no_kartu')->unique();
            $table->string('nfc_kartu')->nullable();
            $table->enum('status', ['aktif', 'rusak', 'hilang'])->default('aktif');
            $table->foreignId('current_guest_id')->nullable()->constrained('buku_tamus')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'project_id', 'area_id']);
            $table->index(['status', 'is_active']);
            $table->index('no_kartu');
            $table->index('nfc_kartu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_tamus');
    }
};