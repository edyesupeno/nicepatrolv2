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
            // Step 1: Data Diri
            $table->string('nik', 16)->nullable()->after('nama_tamu');
            $table->date('tanggal_lahir')->nullable()->after('nik');
            $table->text('domisili')->nullable()->after('tanggal_lahir');
            $table->string('jabatan')->nullable()->after('perusahaan_tamu');
            
            // Step 2: Kontak Tamu
            $table->string('email')->nullable()->after('jabatan');
            $table->string('no_whatsapp', 20)->nullable()->after('email');
            
            // Step 3: Data Kunjungan
            $table->string('lokasi_dituju')->nullable()->after('bertemu');
            $table->datetime('mulai_kunjungan')->nullable()->after('lokasi_dituju');
            $table->datetime('selesai_kunjungan')->nullable()->after('mulai_kunjungan');
            $table->string('lama_kunjungan')->nullable()->after('selesai_kunjungan');
            
            // Step 4: Kuesioner
            $table->string('pertanyaan_1')->nullable()->after('keterangan_tambahan');
            $table->json('pertanyaan_2')->nullable()->after('pertanyaan_1');
            $table->string('pertanyaan_3')->nullable()->after('pertanyaan_2');
            
            // Add indexes for new searchable fields
            $table->index('nik');
            $table->index('email');
            $table->index('no_whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_tamus', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropIndex(['email']);
            $table->dropIndex(['no_whatsapp']);
            
            $table->dropColumn([
                'nik',
                'tanggal_lahir',
                'domisili',
                'jabatan',
                'email',
                'no_whatsapp',
                'lokasi_dituju',
                'mulai_kunjungan',
                'selesai_kunjungan',
                'lama_kunjungan',
                'pertanyaan_1',
                'pertanyaan_2',
                'pertanyaan_3',
            ]);
        });
    }
};