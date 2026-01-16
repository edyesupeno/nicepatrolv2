<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'foto_masuk',
        'foto_keluar',
        'lokasi_masuk',
        'lokasi_keluar',
        'status',
        'keterangan',
        'durasi_kerja',
        'on_radius',
        'sumber_data',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
        'on_radius' => 'boolean',
    ];

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
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    // Helper methods
    public function isLate(): bool
    {
        return $this->status === 'terlambat';
    }

    public function isPresent(): bool
    {
        return in_array($this->status, ['hadir', 'terlambat', 'pulang_cepat']);
    }

    public function isAbsent(): bool
    {
        return $this->status === 'alpa';
    }
}
