<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            
            // BPJS Settings
            $table->decimal('bpjs_kesehatan_perusahaan', 5, 2)->default(4.00); // %
            $table->decimal('bpjs_kesehatan_karyawan', 5, 2)->default(1.00); // %
            $table->decimal('bpjs_jht_perusahaan', 5, 2)->default(3.70); // %
            $table->decimal('bpjs_jht_karyawan', 5, 2)->default(2.00); // %
            $table->decimal('bpjs_jp_perusahaan', 5, 2)->default(2.00); // %
            $table->decimal('bpjs_jp_karyawan', 5, 2)->default(1.00); // %
            $table->decimal('bpjs_jkk_perusahaan', 5, 2)->default(0.24); // %
            $table->decimal('bpjs_jkm_perusahaan', 5, 2)->default(0.30); // %
            
            // PPh 21 Settings (Tarif Progresif)
            $table->decimal('pph21_bracket1_rate', 5, 2)->default(5.00); // 0-60 juta
            $table->decimal('pph21_bracket2_rate', 5, 2)->default(15.00); // 60-250 juta
            $table->decimal('pph21_bracket3_rate', 5, 2)->default(25.00); // 250-500 juta
            $table->decimal('pph21_bracket4_rate', 5, 2)->default(30.00); // 500 juta - 5 miliar
            $table->decimal('pph21_bracket5_rate', 5, 2)->default(35.00); // > 5 miliar
            
            // PTKP Settings
            $table->bigInteger('ptkp_tk0')->default(54000000); // TK/0
            $table->bigInteger('ptkp_tk1')->default(58500000); // TK/1
            $table->bigInteger('ptkp_tk2')->default(63000000); // TK/2
            $table->bigInteger('ptkp_tk3')->default(67500000); // TK/3
            $table->bigInteger('ptkp_k0')->default(58500000); // K/0
            $table->bigInteger('ptkp_k1')->default(63000000); // K/1
            $table->bigInteger('ptkp_k2')->default(67500000); // K/2
            $table->bigInteger('ptkp_k3')->default(72000000); // K/3
            
            // Lembur Settings
            $table->decimal('lembur_hari_kerja', 5, 2)->default(1.5); // multiplier
            $table->decimal('lembur_akhir_pekan', 5, 2)->default(2.0); // multiplier
            $table->decimal('lembur_hari_libur', 5, 2)->default(3.0); // multiplier
            $table->integer('lembur_max_jam_per_hari')->default(4); // jam
            
            // Periode Payroll Settings
            $table->integer('periode_cutoff_tanggal')->default(25); // tanggal cutoff (1-31)
            $table->integer('periode_pembayaran_tanggal')->default(1); // tanggal pembayaran (1-31)
            $table->boolean('periode_auto_generate')->default(false); // auto generate payroll
            
            $table->timestamps();
            
            // Unique constraint: one setting per perusahaan
            $table->unique('perusahaan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
