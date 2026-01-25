<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class KartuTamu extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'no_kartu',
        'nfc_kartu',
        'status',
        'current_guest_id',
        'assigned_at',
        'returned_at',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
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
        return $this->belongsTo(Area::class);
    }

    public function currentGuest(): BelongsTo
    {
        return $this->belongsTo(BukuTamu::class, 'current_guest_id');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'aktif' => 'Aktif',
            'rusak' => 'Rusak',
            'hilang' => 'Hilang',
        ];

        return $labels[$this->status] ?? ($this->status ?? 'Tidak Ditentukan');
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'aktif' => 'green',
            'rusak' => 'yellow',
            'hilang' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        $icons = [
            'aktif' => 'fas fa-check-circle',
            'rusak' => 'fas fa-exclamation-triangle',
            'hilang' => 'fas fa-times-circle',
        ];

        return $icons[$this->status] ?? 'fas fa-circle';
    }

    /**
     * Check if card is available for assignment
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->status === 'aktif' && !$this->current_guest_id && $this->is_active;
    }

    /**
     * Check if card is currently assigned
     */
    public function getIsAssignedAttribute(): bool
    {
        return !is_null($this->current_guest_id) && is_null($this->returned_at);
    }

    /**
     * Assign card to guest
     */
    public function assignToGuest($guestId): void
    {
        $this->update([
            'current_guest_id' => $guestId,
            'assigned_at' => now(),
            'returned_at' => null,
        ]);
    }

    /**
     * Return card from guest
     */
    public function returnFromGuest(): void
    {
        $this->update([
            'current_guest_id' => null,
            'returned_at' => now(),
        ]);
    }

    /**
     * Scope for available cards
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'aktif')
                    ->whereNull('current_guest_id')
                    ->where('is_active', true);
    }

    /**
     * Scope for assigned cards
     */
    public function scopeAssigned($query)
    {
        return $query->whereNotNull('current_guest_id')
                    ->whereNull('returned_at');
    }
}