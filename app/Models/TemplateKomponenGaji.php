<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class TemplateKomponenGaji extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'nama_template',
        'deskripsi',
        'project_id',
        'jabatan_id',
        'karyawan_id',
        'komponen_payroll_id',
        'nilai',
        'level',
        'aktif',
        'is_default',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'aktif' => 'boolean',
        'is_default' => 'boolean',
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

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function komponenPayroll()
    {
        return $this->belongsTo(KomponenPayroll::class);
    }
}
