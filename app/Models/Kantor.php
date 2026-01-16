<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Kantor extends Model
{
    use HasHashId;

    protected $table = 'kantors';

    protected $fillable = [
        'perusahaan_id',
        'nama',
        'alamat',
        'telepon',
        'email',
        'is_pusat',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pusat' => 'boolean',
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

    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class, 'lokasi_id');
    }

    public function patrolis(): HasMany
    {
        return $this->hasMany(Patroli::class, 'lokasi_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
