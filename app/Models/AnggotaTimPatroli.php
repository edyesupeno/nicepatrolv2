<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class AnggotaTimPatroli extends Model
{
    use HasHashId;

    protected $table = 'anggota_tim_patroli';

    protected $fillable = [
        'tim_patroli_id',
        'user_id',
        'role',
        'tanggal_bergabung',
        'tanggal_keluar',
        'is_active',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bergabung' => 'date',
        'tanggal_keluar' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Relationships
    public function timPatroli(): BelongsTo
    {
        return $this->belongsTo(TimPatroli::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    // Accessors
    public function getRoleNameAttribute(): string
    {
        return match($this->role) {
            'leader' => 'Danru (Komandan Regu)',
            'wakil_leader' => 'Wakil Leader',
            'anggota' => 'Anggota',
            default => 'Unknown'
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_active) {
            return '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Aktif</span>';
        } else {
            return '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Nonaktif</span>';
        }
    }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'leader' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Danru (Komandan Regu)</span>',
            'wakil_leader' => '<span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">Wakil Leader</span>',
            'anggota' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">Anggota</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Unknown</span>'
        };
    }
}