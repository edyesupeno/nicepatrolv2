<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Atensi extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'created_by',
        'judul',
        'deskripsi',
        'prioritas',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_type',
        'target_data',
        'is_active',
        'is_urgent',
        'published_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'target_data' => 'array',
        'is_active' => 'boolean',
        'is_urgent' => 'boolean',
        'published_at' => 'datetime',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AtensiRecipient::class);
    }

    public function readRecipients(): HasMany
    {
        return $this->hasMany(AtensiRecipient::class)->whereNotNull('read_at');
    }

    public function acknowledgedRecipients(): HasMany
    {
        return $this->hasMany(AtensiRecipient::class)->whereNotNull('acknowledged_at');
    }

    /**
     * Get prioritas label
     */
    public function getPrioritasLabelAttribute(): string
    {
        $labels = [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
        ];

        return $labels[$this->prioritas] ?? $this->prioritas;
    }

    /**
     * Get prioritas color
     */
    public function getPrioritasColorAttribute(): string
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
        ];

        return $colors[$this->prioritas] ?? 'gray';
    }

    /**
     * Get prioritas icon
     */
    public function getPrioritasIconAttribute(): string
    {
        $icons = [
            'low' => 'fas fa-arrow-down',
            'medium' => 'fas fa-minus',
            'high' => 'fas fa-arrow-up',
        ];

        return $icons[$this->prioritas] ?? 'fas fa-info';
    }

    /**
     * Get target type label
     */
    public function getTargetTypeLabelAttribute(): string
    {
        $labels = [
            'all' => 'Semua Orang',
            'area' => 'Berdasarkan Area',
            'jabatan' => 'Berdasarkan Jabatan',
            'specific_users' => 'Orang Tertentu',
        ];

        return $labels[$this->target_type] ?? $this->target_type;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }

        $now = now()->toDateString();
        
        if ($this->tanggal_mulai > $now) {
            return 'Belum Dimulai';
        }
        
        if ($this->tanggal_selesai < $now) {
            return 'Berakhir';
        }
        
        return 'Aktif';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'gray';
        }

        $now = now()->toDateString();
        
        if ($this->tanggal_mulai > $now) {
            return 'blue';
        }
        
        if ($this->tanggal_selesai < $now) {
            return 'gray';
        }
        
        return 'green';
    }

    /**
     * Get read percentage (optimized)
     */
    public function getReadPercentageAttribute(): float
    {
        // Use cached counts if available from withCount()
        if (isset($this->attributes['recipients_count']) && isset($this->attributes['read_recipients_count'])) {
            $totalRecipients = $this->attributes['recipients_count'];
            $readCount = $this->attributes['read_recipients_count'];
        } else {
            // Fallback to direct queries
            $totalRecipients = $this->recipients()->count();
            $readCount = $this->recipients()->whereNotNull('read_at')->count();
        }
        
        if ($totalRecipients === 0) {
            return 0;
        }
        
        return round(($readCount / $totalRecipients) * 100, 1);
    }

    /**
     * Get acknowledgment percentage (optimized)
     */
    public function getAcknowledgmentPercentageAttribute(): float
    {
        // Use cached counts if available from withCount()
        if (isset($this->attributes['recipients_count']) && isset($this->attributes['acknowledged_recipients_count'])) {
            $totalRecipients = $this->attributes['recipients_count'];
            $acknowledgedCount = $this->attributes['acknowledged_recipients_count'];
        } else {
            // Fallback to direct queries
            $totalRecipients = $this->recipients()->count();
            $acknowledgedCount = $this->recipients()->whereNotNull('acknowledged_at')->count();
        }
        
        if ($totalRecipients === 0) {
            return 0;
        }
        
        return round(($acknowledgedCount / $totalRecipients) * 100, 1);
    }

    /**
     * Scope for active atensi
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current atensi (within date range)
     */
    public function scopeCurrent($query)
    {
        $now = now()->toDateString();
        return $query->where('tanggal_mulai', '<=', $now)
                    ->where('tanggal_selesai', '>=', $now);
    }

    /**
     * Scope for urgent atensi
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }
}