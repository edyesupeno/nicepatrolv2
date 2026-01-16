<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_komponen_gajis', function (Blueprint $table) {
            $table->string('nama_template')->nullable()->after('perusahaan_id');
            $table->text('deskripsi')->nullable()->after('nama_template');
        });
    }

    public function down(): void
    {
        Schema::table('template_komponen_gajis', function (Blueprint $table) {
            $table->dropColumn(['nama_template', 'deskripsi']);
        });
    }
};
