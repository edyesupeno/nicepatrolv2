<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->string('ptkp_status', 10)->nullable()->after('hari_lembur')
                ->comment('Status PTKP: TK/0, TK/1, K/0, K/1, dll');
            $table->bigInteger('ptkp_value')->default(0)->after('ptkp_status')
                ->comment('Nilai PTKP per tahun');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['ptkp_status', 'ptkp_value']);
        });
    }
};
