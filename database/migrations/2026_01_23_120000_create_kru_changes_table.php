<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kru_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('area_patrol_id')->constrained('area_patrols')->onDelete('cascade');
            
            // Tim yang keluar (outgoing)
            $table->foreignId('tim_keluar_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('shift_keluar_id')->constrained('shifts')->onDelete('cascade');
            
            // Tim yang masuk (incoming)
            $table->foreignId('tim_masuk_id')->constrained('tim_patrolis')->onDelete('cascade');
            $table->foreignId('shift_masuk_id')->constrained('shifts')->onDelete('cascade');
            
            // Waktu pergantian
            $table->datetime('waktu_mulai_handover');
            $table->datetime('waktu_selesai_handover')->nullable();
            
            // Status pergantian
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            // Catatan handover
            $table->text('catatan_keluar')->nullable(); // Catatan dari tim yang keluar
            $table->text('catatan_masuk')->nullable();  // Catatan dari tim yang masuk
            $table->text('catatan_supervisor')->nullable(); // Catatan supervisor
            
            // Kondisi area saat handover
            $table->json('kondisi_area')->nullable(); // JSON untuk kondisi area
            $table->json('inventaris_serah_terima')->nullable(); // JSON untuk inventaris
            
            // User yang terlibat
            $table->foreignId('petugas_keluar_id')->constrained('users')->onDelete('cascade'); // Yang serah
            $table->foreignId('petugas_masuk_id')->nullable()->constrained('users')->onDelete('set null'); // Yang terima
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null'); // Supervisor
            
            // Approval
            $table->boolean('approved_keluar')->default(false); // Approval tim keluar
            $table->boolean('approved_masuk')->default(false);  // Approval tim masuk
            $table->boolean('approved_supervisor')->default(false); // Approval supervisor
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['area_patrol_id', 'waktu_mulai_handover']);
            $table->index(['status', 'waktu_mulai_handover']);
            $table->index(['tim_keluar_id', 'tim_masuk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kru_changes');
    }
};