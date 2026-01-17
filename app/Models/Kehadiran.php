<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Kehadiran extends Model
{
    protected $fillable = [
        'karyawan_id',
        'perusahaan_id',
        'project_id',
        'shift_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'jam_istirahat',
        'jam_kembali',
        'foto_masuk',
        'foto_keluar',
        'foto_istirahat',
        'foto_kembali',
        'lokasi_masuk',
        'lokasi_keluar',
        'lokasi_istirahat',
        'lokasi_kembali',
        'status',
        'keterangan',
        'durasi_kerja',
        'durasi_istirahat',
        'on_radius',
        'on_radius_masuk',
        'on_radius_keluar',
        'jarak_masuk',
        'jarak_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'map_absen_masuk',
        'map_absen_keluar',
        'sumber_data',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'on_radius' => 'boolean',
        'on_radius_masuk' => 'boolean',
        'on_radius_keluar' => 'boolean',
        'latitude_masuk' => 'decimal:8',
        'longitude_masuk' => 'decimal:8',
        'latitude_keluar' => 'decimal:8',
        'longitude_keluar' => 'decimal:8',
    ];

    protected $appends = ['hash_id'];

    // Hash ID functionality
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
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Helper methods for Google Maps URLs
    public function getGoogleMapsUrlMasukAttribute()
    {
        if ($this->latitude_masuk && $this->longitude_masuk) {
            return "https://www.google.com/maps?q={$this->latitude_masuk},{$this->longitude_masuk}";
        }
        return null;
    }

    public function getGoogleMapsUrlKeluarAttribute()
    {
        if ($this->latitude_keluar && $this->longitude_keluar) {
            return "https://www.google.com/maps?q={$this->latitude_keluar},{$this->longitude_keluar}";
        }
        return null;
    }

    // Helper methods for radius status
    public function getRadiusStatusMasukAttribute()
    {
        return $this->on_radius_masuk ? 'Dalam Radius' : 'Luar Radius';
    }

    public function getRadiusStatusKeluarAttribute()
    {
        return $this->on_radius_keluar ? 'Dalam Radius' : 'Luar Radius';
    }
}
