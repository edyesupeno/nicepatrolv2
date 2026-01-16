<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            // Status Perkawinan untuk PTKP
            $table->enum('status_perkawinan', ['TK', 'K'])->default('TK')->after('jenis_kelamin')
                ->comment('TK = Tidak Kawin, K = Kawin');
            
            // Jumlah Tanggungan untuk PTKP
            $table->integer('jumlah_tanggungan')->default(0)->after('status_perkawinan')
                ->comment('0, 1, 2, 3 (maksimal 3 tanggungan)');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['status_perkawinan', 'jumlah_tanggungan']);
        });
    }
};
