<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            // Add Google Maps URLs for comparison
            $table->text('map_absen_masuk')->nullable()->after('longitude_keluar');
            $table->text('map_absen_keluar')->nullable()->after('map_absen_masuk');
        });
    }

    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropColumn(['map_absen_masuk', 'map_absen_keluar']);
        });
    }
};