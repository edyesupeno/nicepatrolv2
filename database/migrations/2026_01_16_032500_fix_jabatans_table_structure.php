<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            // Drop unique constraint on kode
            $table->dropUnique(['kode']);
            
            // Add perusahaan_id
            $table->foreignId('perusahaan_id')->after('id')->constrained('perusahaans')->onDelete('cascade');
            
            // Make kode nullable and not unique
            $table->string('kode')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('jabatans', function (Blueprint $table) {
            $table->dropForeign(['perusahaan_id']);
            $table->dropColumn('perusahaan_id');
            $table->string('kode')->unique()->change();
        });
    }
};
