<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class KuesionerTamu extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_patrol_id',
        'judul',
        'deskripsi',
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

    public function areaPatrol(): BelongsTo
    {
        return $this->belongsTo(AreaPatrol::class);
    }

    public function pertanyaans(): HasMany
    {
        return $this->hasMany(PertanyaanTamu::class)->orderBy('urutan');
    }
}
