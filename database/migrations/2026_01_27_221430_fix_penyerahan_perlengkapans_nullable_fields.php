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
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            // Make fields nullable that are not needed for draft schedules
            $table->date('tanggal_penyerahan')->nullable()->change();
            $table->date('tanggal_pengembalian')->nullable()->change();
            $table->text('catatan_pengembalian')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            $table->date('tanggal_penyerahan')->nullable(false)->change();
            $table->date('tanggal_pengembalian')->nullable()->change();
            $table->text('catatan_pengembalian')->nullable()->change();
        });
    }
};