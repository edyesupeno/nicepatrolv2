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
            $table->string('nomor_penyerahan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            $table->string('nomor_penyerahan')->nullable(false)->change();
        });
    }
};