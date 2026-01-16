<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            // Add JKK and JKM karyawan columns (default 0 karena biasanya ditanggung perusahaan)
            $table->decimal('bpjs_jkk_karyawan', 5, 2)->default(0.00)->after('bpjs_jkk_perusahaan');
            $table->decimal('bpjs_jkm_karyawan', 5, 2)->default(0.00)->after('bpjs_jkm_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropColumn(['bpjs_jkk_karyawan', 'bpjs_jkm_karyawan']);
        });
    }
};
