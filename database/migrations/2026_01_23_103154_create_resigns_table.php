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
        Schema::create('resigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_resign_efektif');
            $table->enum('jenis_resign', [
                'resign_pribadi',
                'kontrak_habis', 
                'phk',
                'pensiun',
                'meninggal_dunia',
                'lainnya'
            ]);
            $table->text('alasan_resign');
            $table->text('catatan_approval')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('handover_notes')->nullable(); // Catatan serah terima
            $table->json('handover_items')->nullable(); // Item yang diserahkan (aset, dokumen, dll)
            $table->boolean('is_blacklist')->default(false); // Apakah masuk blacklist
            $table->text('blacklist_reason')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['perusahaan_id', 'status']);
            $table->index(['project_id', 'status']);
            $table->index(['karyawan_id']);
            $table->index(['tanggal_pengajuan']);
            $table->index(['tanggal_resign_efektif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resigns');
    }
};