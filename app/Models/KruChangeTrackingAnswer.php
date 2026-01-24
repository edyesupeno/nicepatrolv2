<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class KruChangeTrackingAnswer extends Model
{
    protected $fillable = [
        'kru_change_id',
        'tipe_tracking',
        'tracking_id',
        'pertanyaan_id',
        'jawaban',
        'user_id'
    ];

    // Global Scope - Multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->whereHas('kruChange', function ($query) {
                    $query->where('perusahaan_id', auth()->user()->perusahaan_id);
                });
            }
        });
    }

    // Relationships
    public function kruChange(): BelongsTo
    {
        return $this->belongsTo(KruChange::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pertanyaanKuesioner(): BelongsTo
    {
        return $this->belongsTo(PertanyaanKuesioner::class, 'pertanyaan_id');
    }

    public function pertanyaanPemeriksaan(): BelongsTo
    {
        return $this->belongsTo(PertanyaanPemeriksaan::class, 'pertanyaan_id');
    }

    public function kuesionerPatroli(): BelongsTo
    {
        return $this->belongsTo(KuesionerPatroli::class, 'tracking_id');
    }

    public function pemeriksaanPatroli(): BelongsTo
    {
        return $this->belongsTo(PemeriksaanPatroli::class, 'tracking_id');
    }

    // Scopes
    public function scopeKuesioner(Builder $query): Builder
    {
        return $query->where('tipe_tracking', 'kuesioner');
    }

    public function scopePemeriksaan(Builder $query): Builder
    {
        return $query->where('tipe_tracking', 'pemeriksaan');
    }
}
