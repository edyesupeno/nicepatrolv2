<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PatroliDetail extends Model
{
    protected $fillable = [
        'patroli_id',
        'checkpoint_id',
        'waktu_scan',
        'latitude',
        'longitude',
        'catatan',
        'foto',
        'foto_verifikasi',
        'status',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    public function patroli(): BelongsTo
    {
        return $this->belongsTo(Patroli::class);
    }

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function asetChecks(): HasMany
    {
        return $this->hasMany(AsetCheck::class);
    }
}
