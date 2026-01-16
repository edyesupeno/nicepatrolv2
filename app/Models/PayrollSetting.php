<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PayrollSetting extends Model
{
    protected $fillable = [
        'perusahaan_id',
        // BPJS
        'bpjs_kesehatan_perusahaan',
        'bpjs_kesehatan_karyawan',
        'bpjs_jht_perusahaan',
        'bpjs_jht_karyawan',
        'bpjs_jp_perusahaan',
        'bpjs_jp_karyawan',
        'bpjs_jkk_perusahaan',
        'bpjs_jkm_perusahaan',
        // PPh 21
        'pph21_bracket1_rate',
        'pph21_bracket2_rate',
        'pph21_bracket3_rate',
        'pph21_bracket4_rate',
        'pph21_bracket5_rate',
        // PTKP
        'ptkp_tk0',
        'ptkp_tk1',
        'ptkp_tk2',
        'ptkp_tk3',
        'ptkp_k0',
        'ptkp_k1',
        'ptkp_k2',
        'ptkp_k3',
        // Lembur
        'lembur_hari_kerja',
        'lembur_akhir_pekan',
        'lembur_hari_libur',
        'lembur_max_jam_per_hari',
        // Periode
        'periode_cutoff_tanggal',
        'periode_pembayaran_tanggal',
        'periode_auto_generate',
    ];

    protected $casts = [
        'periode_auto_generate' => 'boolean',
    ];

    /**
     * Global scope untuk multi-tenancy
     */
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    /**
     * Relasi ke Perusahaan
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
