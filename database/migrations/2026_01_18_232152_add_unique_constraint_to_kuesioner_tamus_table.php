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
        Schema::table('kuesioner_tamus', function (Blueprint $table) {
            // Add unique constraint untuk area_patrol_id
            // Setiap area hanya boleh memiliki 1 kuesioner tamu
            $table->unique('area_patrol_id', 'unique_area_kuesioner_tamu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuesioner_tamus', function (Blueprint $table) {
            $table->dropUnique('unique_area_kuesioner_tamu');
        });
    }
};
