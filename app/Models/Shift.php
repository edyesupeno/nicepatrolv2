<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Vinkla\Hashids\Facades\Hashids;

class Shift extends Model
{
    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'kode_shift',
        'nama_shift',
        'jam_mulai',
        'jam_selesai',
        'durasi_istirahat',
        'toleransi_keterlambatan',
        'deskripsi',
        'warna',
    ];

    protected $appends = ['hash_id'];

    // Hash ID
    public function getHashIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = Hashids::decode($value)[0] ?? null;
        return $this->where('id', $id)->firstOrFail();
    }

    // Global Scope - Multi-tenancy
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
}
