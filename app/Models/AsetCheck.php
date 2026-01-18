<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetCheck extends Model
{
    protected $fillable = [
        'patroli_detail_id',
        'aset_kawasan_id',
        'status',
        'catatan',
        'foto',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function patroliDetail(): BelongsTo
    {
        return $this->belongsTo(PatroliDetail::class);
    }

    public function asetKawasan(): BelongsTo
    {
        return $this->belongsTo(AsetKawasan::class);
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
