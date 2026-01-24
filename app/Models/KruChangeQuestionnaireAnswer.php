<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KruChangeQuestionnaireAnswer extends Model
{
    protected $fillable = [
        'kru_change_id',
        'kuesioner_patroli_id',
        'pertanyaan_kuesioner_id',
        'jawaban',
        'jawaban_detail',
        'foto',
        'tipe_jawaban',
        'user_id',
    ];

    protected $casts = [
        'jawaban_detail' => 'array',
    ];

    // Relationships
    public function kruChange(): BelongsTo
    {
        return $this->belongsTo(KruChange::class);
    }

    public function kuesionerPatroli(): BelongsTo
    {
        return $this->belongsTo(KuesionerPatroli::class);
    }

    public function pertanyaanKuesioner(): BelongsTo
    {
        return $this->belongsTo(PertanyaanKuesioner::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getTipeJawabanBadgeAttribute(): string
    {
        return match($this->tipe_jawaban) {
            'keluar' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Tim Keluar</span>',
            'masuk' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Tim Masuk</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Unknown</span>'
        };
    }
}