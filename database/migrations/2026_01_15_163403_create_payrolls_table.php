<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->string('periode'); // Format: YYYY-MM (2024-01)
            $table->date('tanggal_generate');
            
            // Gaji Pokok
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            
            // Kehadiran
            $table->integer('hari_kerja')->default(0);
            $table->integer('hari_masuk')->default(0);
            $table->integer('hari_alpha')->default(0);
            $table->integer('hari_sakit')->default(0);
            $table->integer('hari_izin')->default(0);
            $table->integer('hari_cuti')->default(0);
            $table->integer('hari_lembur')->default(0);
            
            // Tunjangan (JSON untuk detail per komponen)
            $table->json('tunjangan_detail')->nullable();
            $table->decimal('total_tunjangan', 15, 2)->default(0);
            
            // BPJS
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('bpjs_ketenagakerjaan', 15, 2)->default(0);
            
            // Potongan (JSON untuk detail per komponen)
            $table->json('potongan_detail')->nullable();
            $table->decimal('total_potongan', 15, 2)->default(0);
            
            // Pajak
            $table->decimal('pajak_pph21', 15, 2)->default(0);
            
            // Total
            $table->decimal('gaji_bruto', 15, 2)->default(0); // Gaji Pokok + Tunjangan + BPJS
            $table->decimal('gaji_netto', 15, 2)->default(0); // Gaji Bruto - Potongan - Pajak
            
            // Status
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->text('catatan')->nullable();
            
            // Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // Payment
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'periode']);
            $table->index(['project_id', 'periode']);
            $table->index('status');
            $table->unique(['karyawan_id', 'periode']); // Prevent duplicate payroll for same karyawan & periode
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
