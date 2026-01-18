<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class TimPatroli extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'nama_tim',
        'shift_id',
        'leader_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Shift::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(AreaPatrol::class, 'area_tim_patroli');
    }

    public function rutes(): BelongsToMany
    {
        return $this->belongsToMany(RutePatrol::class, 'rute_tim_patroli');
    }

    public function inventaris(): BelongsToMany
    {
        return $this->belongsToMany(InventarisPatroli::class, 'inventaris_tim_patroli');
    }

    public function kuesioners(): BelongsToMany
    {
        return $this->belongsToMany(KuesionerPatroli::class, 'kuesioner_tim_patroli');
    }

    public function pemeriksaans(): BelongsToMany
    {
        return $this->belongsToMany(PemeriksaanPatroli::class, 'pemeriksaan_tim_patroli');
    }

    public function checkpoints(): BelongsToMany
    {
        return $this->belongsToMany(Checkpoint::class, 'checkpoint_tim_patroli')
            ->withPivot('urutan')
            ->orderBy('checkpoint_tim_patroli.urutan');
    }

    public function anggota(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnggotaTimPatroli::class);
    }

    public function anggotaAktif(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AnggotaTimPatroli::class)->where('is_active', true);
    }
}
