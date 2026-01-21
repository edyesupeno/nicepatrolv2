<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->enum('jenis_regu', ['POS JAGA', 'PATROLI MOBIL', 'PATROLI MOTOR'])
                ->default('POS JAGA')
                ->after('nama_tim')
                ->comment('Jenis regu patroli');
        });
    }

    public function down(): void
    {
        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->dropColumn('jenis_regu');
        });
    }
};