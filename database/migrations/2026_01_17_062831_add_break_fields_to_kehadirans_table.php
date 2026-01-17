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
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->time('jam_istirahat')->nullable()->after('jam_keluar');
            $table->time('jam_kembali')->nullable()->after('jam_istirahat');
            $table->string('foto_istirahat')->nullable()->after('foto_keluar');
            $table->string('foto_kembali')->nullable()->after('foto_istirahat');
            $table->string('lokasi_istirahat')->nullable()->after('lokasi_keluar');
            $table->string('lokasi_kembali')->nullable()->after('lokasi_istirahat');
            $table->integer('durasi_istirahat')->nullable()->comment('Durasi istirahat dalam menit')->after('durasi_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropColumn([
                'jam_istirahat',
                'jam_kembali',
                'foto_istirahat',
                'foto_kembali',
                'lokasi_istirahat',
                'lokasi_kembali',
                'durasi_istirahat'
            ]);
        });
    }
};
