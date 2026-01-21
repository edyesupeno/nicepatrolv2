<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class AreaPatrol extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'nama',
        'deskripsi',
        'alamat',
        'koordinat',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $appends = ['hash_id'];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
        
        // Auto-parse koordinat before saving
        static::saving(function ($model) {
            if ($model->koordinat && !$model->latitude && !$model->longitude) {
                $coords = explode(',', $model->koordinat);
                if (count($coords) === 2) {
                    $model->latitude = trim($coords[0]);
                    $model->longitude = trim($coords[1]);
                }
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

    public function area(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Area::class);
    }

    public function rutePatrols()
    {
        return $this->hasMany(RutePatrol::class);
    }

    // Relasi kuesionerTamus dihapus karena sekarang kuesioner terkait dengan Area, bukan AreaPatrol
}
