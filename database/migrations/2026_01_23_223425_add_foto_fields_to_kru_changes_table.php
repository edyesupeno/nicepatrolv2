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
        Schema::table('kru_changes', function (Blueprint $table) {
            $table->string('foto_tim_keluar')->nullable()->after('catatan_supervisor');
            $table->string('foto_tim_masuk')->nullable()->after('foto_tim_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kru_changes', function (Blueprint $table) {
            $table->dropColumn(['foto_tim_keluar', 'foto_tim_masuk']);
        });
    }
};