<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Tugas extends Model
{
    use HasHashId;

    protected $table = 'tugas';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'created_by',
        'judul',
        'deskripsi',
        'prioritas',
        'batas_pengerjaan',
        'detail_lokasi',
        'target_type',
        'target_data',
        'status',
        'is_urgent',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'batas_pengerjaan' => 'date',
        'target_data' => 'array',
        'is_urgent' => 'boolean',
        'is_active' => 'boolean',
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

    public function assignments(): HasMany
    {
        return $this->hasMany(TugasAssignment::class);
    }

    public function completedAssignments(): HasMany
    {
        return $this->hasMany(TugasAssignment::class)->where('status', 'completed');
    }

    public function inProgressAssignments(): HasMany
    {
        return $this->hasMany(TugasAssignment::class)->where('status', 'in_progress');
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
        $labels = [
            'draft' => 'Draft',
            'active' => 'Aktif',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'draft' => 'gray',
            'active' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get completion percentage (optimized)
     */
    public function getCompletionPercentageAttribute(): float
    {
        // Use cached counts if available from withCount()
        if (isset($this->attributes['assignments_count']) && isset($this->attributes['completed_assignments_count'])) {
            $totalAssignments = $this->attributes['assignments_count'];
            $completedCount = $this->attributes['completed_assignments_count'];
        } else {
            // Fallback to direct queries
            $totalAssignments = $this->assignments()->count();
            $completedCount = $this->assignments()->where('status', 'completed')->count();
        }
        
        if ($totalAssignments === 0) {
            return 0;
        }
        
        return round(($completedCount / $totalAssignments) * 100, 1);
    }

    /**
     * Get progress percentage (optimized)
     */
    public function getProgressPercentageAttribute(): float
    {
        // Use cached counts if available from withCount()
        if (isset($this->attributes['assignments_count']) && isset($this->attributes['in_progress_assignments_count'])) {
            $totalAssignments = $this->attributes['assignments_count'];
            $inProgressCount = $this->attributes['in_progress_assignments_count'];
        } else {
            // Fallback to direct queries
            $totalAssignments = $this->assignments()->count();
            $inProgressCount = $this->assignments()->where('status', 'in_progress')->count();
        }
        
        if ($totalAssignments === 0) {
            return 0;
        }
        
        return round(($inProgressCount / $totalAssignments) * 100, 1);
    }

    /**
     * Check if task is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'completed') {
            return false;
        }
        
        return $this->batas_pengerjaan < now()->toDateString();
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->status === 'completed') {
            return 0;
        }
        
        return now()->diffInDays($this->batas_pengerjaan, false);
    }

    /**
     * Scope for active tugas
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope for urgent tugas
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Scope for overdue tugas
     */
    public function scopeOverdue($query)
    {
        return $query->where('batas_pengerjaan', '<', now()->toDateString())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope for due soon (within 3 days)
     */
    public function scopeDueSoon($query)
    {
        return $query->whereBetween('batas_pengerjaan', [
            now()->toDateString(),
            now()->addDays(3)->toDateString()
        ])->where('status', '!=', 'completed');
    }
}