<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing 'PATROLI KAKI' values to 'PATROLI MOTOR'
        DB::table('tim_patrolis')
            ->where('jenis_regu', 'PATROLI KAKI')
            ->update(['jenis_regu' => 'PATROLI MOTOR']);

        // Drop and recreate the enum column with new values
        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->dropColumn('jenis_regu');
        });

        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->enum('jenis_regu', ['POS JAGA', 'PATROLI MOBIL', 'PATROLI MOTOR'])
                ->default('POS JAGA')
                ->after('nama_tim')
                ->comment('Jenis regu patroli');
        });
    }

    public function down(): void
    {
        // Update existing 'PATROLI MOTOR' values back to 'PATROLI KAKI'
        DB::table('tim_patrolis')
            ->where('jenis_regu', 'PATROLI MOTOR')
            ->update(['jenis_regu' => 'PATROLI KAKI']);

        // Drop and recreate the enum column with old values
        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->dropColumn('jenis_regu');
        });

        Schema::table('tim_patrolis', function (Blueprint $table) {
            $table->enum('jenis_regu', ['POS JAGA', 'PATROLI MOBIL', 'PATROLI KAKI'])
                ->default('POS JAGA')
                ->after('nama_tim')
                ->comment('Jenis regu patroli');
        });
    }
};