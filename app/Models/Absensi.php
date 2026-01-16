<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Absensi extends Model
{
    protected $table = 'absensis';
    
    protected $fillable = [
        'user_id',
        'perusahaan_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'keterangan',
        'lokasi_masuk',
        'lokasi_keluar',
        'foto_masuk',
        'foto_keluar',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Global scope untuk multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id && !auth()->user()->isSuperAdmin()) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }
}
