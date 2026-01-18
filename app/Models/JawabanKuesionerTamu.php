<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanKuesionerTamu extends Model
{
    protected $fillable = [
        'buku_tamu_id',
        'pertanyaan_tamu_id',
        'jawaban',
    ];

    public function bukuTamu(): BelongsTo
    {
        return $this->belongsTo(BukuTamu::class);
    }

    public function pertanyaanTamu(): BelongsTo
    {
        return $this->belongsTo(PertanyaanTamu::class);
    }
}
