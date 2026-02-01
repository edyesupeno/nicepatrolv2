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
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_reimbursement')->unique();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Yang mengajukan
            $table->string('judul_pengajuan');
            $table->text('deskripsi');
            $table->decimal('jumlah_pengajuan', 15, 2);
            $table->decimal('jumlah_disetujui', 15, 2)->nullable();
            $table->enum('kategori', [
                'transportasi',
                'akomodasi', 
                'konsumsi',
                'komunikasi',
                'peralatan',
                'medis',
                'training',
                'operasional',
                'lainnya'
            ]);
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_kejadian');
            $table->enum('status', [
                'draft',
                'submitted', 
                'reviewed',
                'approved',
                'rejected',
                'paid',
                'cancelled'
            ])->default('draft');
            $table->enum('prioritas', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->json('bukti_dokumen')->nullable(); // Array of file paths
            $table->text('catatan_pengaju')->nullable();
            $table->text('catatan_reviewer')->nullable();
            $table->text('catatan_approver')->nullable();
            $table->text('alasan_penolakan')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('rekening_id')->nullable()->constrained()->onDelete('set null'); // Rekening untuk pembayaran
            $table->string('nomor_transaksi_pembayaran')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurring_period')->nullable(); // monthly, quarterly, yearly
            $table->timestamps();

            // Indexes
            $table->index(['perusahaan_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['karyawan_id', 'tanggal_pengajuan']);
            $table->index(['status', 'tanggal_pengajuan']);
            $table->index('nomor_reimbursement');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};