<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtensiRecipient extends Model
{
    protected $fillable = [
        'atensi_id',
        'user_id',
        'read_at',
        'acknowledged_at',
        'acknowledgment_note',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function atensi(): BelongsTo
    {
        return $this->belongsTo(Atensi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark as acknowledged
     */
    public function acknowledge(string $note = null): void
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledgment_note' => $note,
            'read_at' => $this->read_at ?? now(), // Also mark as read
        ]);
    }

    /**
     * Check if read
     */
    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if acknowledged
     */
    public function getIsAcknowledgedAttribute(): bool
    {
        return !is_null($this->acknowledged_at);
    }
}