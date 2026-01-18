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
        Schema::table('buku_tamus', function (Blueprint $table) {
            // Add area relationship
            $table->foreignId('area_id')->nullable()->after('project_id')->constrained()->onDelete('set null');
            
            // Add identity document photo
            $table->text('foto_identitas')->nullable()->after('foto')->comment('Path to KTP/SIM photo');
            
            // Add emergency contact information
            $table->string('kontak_darurat_nama')->nullable()->after('bertemu');
            $table->string('kontak_darurat_telepon')->nullable()->after('kontak_darurat_nama');
            $table->string('kontak_darurat_hubungan')->nullable()->after('kontak_darurat_telepon')->comment('Hubungan dengan tamu');
            
            // Add card number for borrowed access card
            $table->string('no_kartu_pinjam')->nullable()->after('qr_code')->comment('Nomor kartu akses yang dipinjamkan');
            
            // Add additional notes/description
            $table->text('keterangan_tambahan')->nullable()->after('catatan')->comment('Keterangan tambahan dari petugas');
            
            // Add indexes for new fields
            $table->index('area_id');
            $table->index('no_kartu_pinjam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_tamus', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropIndex(['area_id']);
            $table->dropIndex(['no_kartu_pinjam']);
            
            $table->dropColumn([
                'area_id',
                'foto_identitas',
                'kontak_darurat_nama',
                'kontak_darurat_telepon',
                'kontak_darurat_hubungan',
                'no_kartu_pinjam',
                'keterangan_tambahan'
            ]);
        });
    }
};