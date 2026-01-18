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
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('perusahaan_id')->constrained()->onDelete('set null');
            $table->index(['perusahaan_id', 'area_id']); // Index untuk optimasi query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropIndex(['perusahaan_id', 'area_id']);
            $table->dropColumn('area_id');
        });
    }
};