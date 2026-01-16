<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class RutePatrol extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'area_patrol_id',
        'nama',
        'deskripsi',
        'estimasi_waktu',
        'is_active',
    ];

    protected $appends = ['hash_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'estimasi_waktu' => 'integer',
    ];

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

    public function areaPatrol(): BelongsTo
    {
        return $this->belongsTo(AreaPatrol::class);
    }

    public function checkpoints()
    {
        return $this->hasMany(Checkpoint::class);
    }
}
