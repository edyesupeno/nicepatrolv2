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
        Schema::create('disposal_asets', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_disposal')->unique();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            
            // Asset information
            $table->enum('asset_type', ['data_aset', 'aset_kendaraan']);
            $table->unsignedBigInteger('asset_id'); // ID of the asset being disposed
            $table->string('asset_code'); // Store asset code for reference
            $table->string('asset_name'); // Store asset name for reference
            
            // Disposal information
            $table->date('tanggal_disposal');
            $table->enum('jenis_disposal', ['dijual', 'rusak', 'hilang', 'tidak_layak', 'expired']);
            $table->text('alasan_disposal');
            $table->decimal('nilai_buku', 15, 2)->default(0); // Book value at disposal
            $table->decimal('nilai_disposal', 15, 2)->nullable(); // Sale value if sold
            $table->string('pembeli')->nullable(); // Buyer if sold
            $table->text('catatan')->nullable();
            
            // Approval workflow
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('diajukan_oleh')->constrained('users')->onDelete('cascade');
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->text('catatan_approval')->nullable();
            
            // Documents
            $table->json('dokumen_pendukung')->nullable(); // Supporting documents
            $table->string('foto_kondisi')->nullable(); // Photo of asset condition
            
            $table->timestamps();
            
            // Indexes
            $table->index(['perusahaan_id', 'status']);
            $table->index(['project_id', 'tanggal_disposal']);
            $table->index(['asset_type', 'asset_id']);
            $table->index('jenis_disposal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_asets');
    }
};