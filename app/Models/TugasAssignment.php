<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasAssignment extends Model
{
    protected $fillable = [
        'tugas_id',
        'user_id',
        'status',
        'notes',
        'attachments',
        'progress_percentage',
        'started_at',
        'completed_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'assigned' => 'Ditugaskan',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
        ];

        return $labels[$this->status] ?? ($this->status ?? 'Tidak Ditentukan');
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'assigned' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'rejected' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        $icons = [
            'assigned' => 'fas fa-user-clock',
            'in_progress' => 'fas fa-spinner',
            'completed' => 'fas fa-check-circle',
            'rejected' => 'fas fa-times-circle',
        ];

        return $icons[$this->status] ?? 'fas fa-info';
    }
}