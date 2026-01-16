<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_komponen_gajis', function (Blueprint $table) {
            $table->boolean('is_default')->default(false)->after('aktif');
            
            // Index untuk query template default
            $table->index(['perusahaan_id', 'project_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::table('template_komponen_gajis', function (Blueprint $table) {
            $table->dropIndex(['perusahaan_id', 'project_id', 'is_default']);
            $table->dropColumn('is_default');
        });
    }
};
