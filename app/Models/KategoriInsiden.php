<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class KategoriInsiden extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $appends = ['hash_id'];

    protected $casts = [
        'is_active' => 'boolean',
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

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'kategori_insiden_project')
                    ->withTimestamps();
    }
}
