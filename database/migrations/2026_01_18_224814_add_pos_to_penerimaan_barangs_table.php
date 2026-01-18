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
            $table->string('pos')->nullable()->after('area_id');
            $table->index(['perusahaan_id', 'pos']); // Index untuk search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            $table->dropIndex(['perusahaan_id', 'pos']);
            $table->dropColumn('pos');
        });
    }
};