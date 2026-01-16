<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PertanyaanPemeriksaan extends Model
{
    protected $fillable = [
        'pemeriksaan_patroli_id',
        'urutan',
        'pertanyaan',
        'tipe_jawaban',
        'opsi_jawaban',
        'is_required',
    ];

    protected $casts = [
        'opsi_jawaban' => 'array',
        'is_required' => 'boolean',
        'urutan' => 'integer',
    ];

    public function pemeriksaanPatroli(): BelongsTo
    {
        return $this->belongsTo(PemeriksaanPatroli::class);
    }
}
