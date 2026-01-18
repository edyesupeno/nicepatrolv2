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
            // Add area_patrol_id untuk integrasi dengan kuesioner tamu
            $table->foreignId('area_patrol_id')->nullable()->after('area_id')->constrained('area_patrols')->onDelete('set null');
            
            // Add index untuk performance
            $table->index('area_patrol_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku_tamus', function (Blueprint $table) {
            $table->dropForeign(['area_patrol_id']);
            $table->dropIndex(['area_patrol_id']);
            $table->dropColumn('area_patrol_id');
        });
    }
};
