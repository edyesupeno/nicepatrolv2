<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_advance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_advance_id')->constrained()->onDelete('cascade');
            $table->string('nomor_laporan')->unique(); // CAR-2026-001
            
            $table->date('tanggal_laporan');
            $table->decimal('total_pengeluaran', 15, 2);
            $table->decimal('sisa_saldo', 15, 2);
            $table->decimal('jumlah_dikembalikan', 15, 2)->default(0);
            
            $table->text('ringkasan_penggunaan');
            $table->string('file_laporan')->nullable(); // PDF laporan
            
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])
                  ->default('draft');
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->date('tanggal_approved')->nullable();
            $table->text('catatan_approval')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['cash_advance_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_advance_reports');
    }
};