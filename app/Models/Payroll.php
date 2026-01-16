<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Payroll extends Model
{
    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'periode',
        'periode_start',
        'periode_end',
        'tanggal_generate',
        'gaji_pokok',
        'hari_kerja',
        'hari_masuk',
        'hari_alpha',
        'hari_sakit',
        'hari_izin',
        'hari_cuti',
        'hari_lembur',
        'ptkp_status',
        'ptkp_value',
        'tunjangan_detail',
        'total_tunjangan',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'potongan_detail',
        'total_potongan',
        'pajak_pph21',
        'gaji_bruto',
        'gaji_netto',
        'status',
        'catatan',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'tanggal_generate' => 'date',
        'periode_start' => 'date',
        'periode_end' => 'date',
        'tunjangan_detail' => 'array',
        'potongan_detail' => 'array',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected $appends = ['hash_id'];

    // Global scope untuk multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    public function getHashIdAttribute()
    {
        return \Vinkla\Hashids\Facades\Hashids::encode($this->id);
    }

    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($value)[0] ?? null;
        return $this->where('id', $id)->firstOrFail();
    }

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
