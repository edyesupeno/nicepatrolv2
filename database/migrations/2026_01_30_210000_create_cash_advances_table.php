<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_advances', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_ca')->unique(); // CA-2026-001
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->decimal('jumlah_pengajuan', 15, 2);
            $table->decimal('saldo_tersedia', 15, 2)->default(0);
            $table->decimal('total_terpakai', 15, 2)->default(0);
            $table->decimal('sisa_saldo', 15, 2)->default(0);
            
            $table->text('keperluan');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_approved')->nullable();
            $table->date('batas_pertanggungjawaban')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'active', 'need_report', 'completed', 'rejected'])
                  ->default('pending');
            
            $table->text('catatan_approval')->nullable();
            $table->text('catatan_reject')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['karyawan_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_advances');
    }
};