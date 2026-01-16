<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // BPJS Ketenagakerjaan - JKM (Jaminan Kematian)
            $table->string('bpjs_jkm_nomor')->nullable()->after('cabang_bank');
            $table->string('bpjs_jkm_npp')->nullable()->after('bpjs_jkm_nomor');
            $table->date('bpjs_jkm_tanggal_terdaftar')->nullable()->after('bpjs_jkm_npp');
            $table->string('bpjs_jkm_status')->nullable()->after('bpjs_jkm_tanggal_terdaftar');
            $table->text('bpjs_jkm_catatan')->nullable()->after('bpjs_jkm_status');
            
            // BPJS Ketenagakerjaan - JKK (Jaminan Kecelakaan Kerja)
            $table->string('bpjs_jkk_nomor')->nullable()->after('bpjs_jkm_catatan');
            $table->string('bpjs_jkk_npp')->nullable()->after('bpjs_jkk_nomor');
            $table->date('bpjs_jkk_tanggal_terdaftar')->nullable()->after('bpjs_jkk_npp');
            $table->string('bpjs_jkk_status')->nullable()->after('bpjs_jkk_tanggal_terdaftar');
            $table->text('bpjs_jkk_catatan')->nullable()->after('bpjs_jkk_status');
            
            // BPJS Ketenagakerjaan - JP (Jaminan Pensiun)
            $table->string('bpjs_jp_nomor')->nullable()->after('bpjs_jkk_catatan');
            $table->string('bpjs_jp_npp')->nullable()->after('bpjs_jp_nomor');
            $table->date('bpjs_jp_tanggal_terdaftar')->nullable()->after('bpjs_jp_npp');
            $table->string('bpjs_jp_status')->nullable()->after('bpjs_jp_tanggal_terdaftar');
            $table->text('bpjs_jp_catatan')->nullable()->after('bpjs_jp_status');
            
            // BPJS Ketenagakerjaan - JHT (Jaminan Hari Tua)
            $table->string('bpjs_jht_nomor')->nullable()->after('bpjs_jp_catatan');
            $table->string('bpjs_jht_npp')->nullable()->after('bpjs_jht_nomor');
            $table->date('bpjs_jht_tanggal_terdaftar')->nullable()->after('bpjs_jht_npp');
            $table->string('bpjs_jht_status')->nullable()->after('bpjs_jht_tanggal_terdaftar');
            $table->text('bpjs_jht_catatan')->nullable()->after('bpjs_jht_status');
            
            // BPJS Kesehatan
            $table->string('bpjs_kesehatan_nomor')->nullable()->after('bpjs_jht_catatan');
            $table->date('bpjs_kesehatan_tanggal_terdaftar')->nullable()->after('bpjs_kesehatan_nomor');
            $table->string('bpjs_kesehatan_status')->nullable()->after('bpjs_kesehatan_tanggal_terdaftar');
            $table->text('bpjs_kesehatan_catatan')->nullable()->after('bpjs_kesehatan_status');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn([
                'bpjs_jkm_nomor', 'bpjs_jkm_npp', 'bpjs_jkm_tanggal_terdaftar', 'bpjs_jkm_status', 'bpjs_jkm_catatan',
                'bpjs_jkk_nomor', 'bpjs_jkk_npp', 'bpjs_jkk_tanggal_terdaftar', 'bpjs_jkk_status', 'bpjs_jkk_catatan',
                'bpjs_jp_nomor', 'bpjs_jp_npp', 'bpjs_jp_tanggal_terdaftar', 'bpjs_jp_status', 'bpjs_jp_catatan',
                'bpjs_jht_nomor', 'bpjs_jht_npp', 'bpjs_jht_tanggal_terdaftar', 'bpjs_jht_status', 'bpjs_jht_catatan',
                'bpjs_kesehatan_nomor', 'bpjs_kesehatan_tanggal_terdaftar', 'bpjs_kesehatan_status', 'bpjs_kesehatan_catatan',
            ]);
        });
    }
};
