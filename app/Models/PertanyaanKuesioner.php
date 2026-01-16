<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PertanyaanKuesioner extends Model
{
    protected $fillable = [
        'kuesioner_patroli_id',
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

    public function kuesionerPatroli(): BelongsTo
    {
        return $this->belongsTo(KuesionerPatroli::class);
    }
}
