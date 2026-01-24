<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class KruChange extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_patrol_id',
        'tim_keluar_id',
        'shift_keluar_id',
        'tim_masuk_id',
        'shift_masuk_id',
        'waktu_mulai_handover',
        'waktu_selesai_handover',
        'status',
        'catatan_keluar',
        'catatan_masuk',
        'catatan_supervisor',
        'kondisi_area',
        'inventaris_serah_terima',
        'petugas_keluar_id',
        'petugas_masuk_id',
        'petugas_keluar_ids',
        'petugas_masuk_ids',
        'supervisor_id',
        'approved_keluar',
        'approved_masuk',
        'approved_supervisor',
        // Tracking fields
        'inventaris_status',
        'kuesioner_status',
        'pemeriksaan_status',
        'inventaris_checked_at',
        'kuesioner_checked_at',
        'pemeriksaan_checked_at',
        'inventaris_checked_by',
        'kuesioner_checked_by',
        'pemeriksaan_checked_by',
        'inventaris_catatan',
        'kuesioner_catatan',
        'pemeriksaan_catatan',
        'foto_tim_keluar',
        'foto_tim_masuk',
    ];

    protected $casts = [
        'waktu_mulai_handover' => 'datetime',
        'waktu_selesai_handover' => 'datetime',
        'kondisi_area' => 'array',
        'inventaris_serah_terima' => 'array',
        'petugas_keluar_ids' => 'array',
        'petugas_masuk_ids' => 'array',
        'approved_keluar' => 'boolean',
        'approved_masuk' => 'boolean',
        'approved_supervisor' => 'boolean',
        // Tracking casts
        'inventaris_status' => 'array',
        'kuesioner_status' => 'array',
        'pemeriksaan_status' => 'array',
        'inventaris_checked_at' => 'datetime',
        'kuesioner_checked_at' => 'datetime',
        'pemeriksaan_checked_at' => 'datetime',
    ];

    protected $appends = ['hash_id'];

    // Global Scope - Multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    // Relationships
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function areaPatrol(): BelongsTo
    {
        return $this->belongsTo(AreaPatrol::class);
    }

    public function timKeluar(): BelongsTo
    {
        return $this->belongsTo(TimPatroli::class, 'tim_keluar_id');
    }

    public function timMasuk(): BelongsTo
    {
        return $this->belongsTo(TimPatroli::class, 'tim_masuk_id');
    }

    public function shiftKeluar(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_keluar_id');
    }

    public function shiftMasuk(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_masuk_id');
    }

    public function petugasKeluar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_keluar_id');
    }

    public function petugasMasuk(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_masuk_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Tracking relationships
    public function inventarisCheckedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inventaris_checked_by');
    }

    public function kuesionerCheckedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kuesioner_checked_by');
    }

    public function pemeriksaanCheckedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemeriksaan_checked_by');
    }

    // Multiple petugas relationships
    public function petugasKeluarMultiple()
    {
        if (!$this->petugas_keluar_ids) {
            return collect();
        }
        return User::whereIn('id', $this->petugas_keluar_ids)->get();
    }

    public function petugasMasukMultiple()
    {
        if (!$this->petugas_masuk_ids) {
            return collect();
        }
        return User::whereIn('id', $this->petugas_masuk_ids)->get();
    }

    // Multiple petugas with role information
    public function petugasKeluarWithRoles()
    {
        if (!$this->petugas_keluar_ids || !$this->tim_keluar_id) {
            return collect();
        }
        
        return AnggotaTimPatroli::where('tim_patroli_id', $this->tim_keluar_id)
            ->whereIn('user_id', $this->petugas_keluar_ids)
            ->with('user:id,name,email')
            ->orderBy('role') // leader first, then wakil_leader, then anggota
            ->get();
    }

    public function petugasMasukWithRoles()
    {
        if (!$this->petugas_masuk_ids || !$this->tim_masuk_id) {
            return collect();
        }
        
        return AnggotaTimPatroli::where('tim_patroli_id', $this->tim_masuk_id)
            ->whereIn('user_id', $this->petugas_masuk_ids)
            ->with('user:id,name,email')
            ->orderBy('role') // leader first, then wakil_leader, then anggota
            ->get();
    }

    public function questionnaireAnswers(): HasMany
    {
        return $this->hasMany(KruChangeQuestionnaireAnswer::class);
    }

    public function questionnaireAnswersKeluar(): HasMany
    {
        return $this->hasMany(KruChangeQuestionnaireAnswer::class)->where('tipe_jawaban', 'keluar');
    }

    public function questionnaireAnswersMasuk(): HasMany
    {
        return $this->hasMany(KruChangeQuestionnaireAnswer::class)->where('tipe_jawaban', 'masuk');
    }

    // Accessors
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">Pending</span>',
            'in_progress' => '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">In Progress</span>',
            'completed' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Completed</span>',
            'cancelled' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">Cancelled</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Unknown</span>'
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    public function getDurasiHandoverAttribute(): ?string
    {
        if (!$this->waktu_mulai_handover || !$this->waktu_selesai_handover) {
            return null;
        }

        $diff = $this->waktu_mulai_handover->diff($this->waktu_selesai_handover);
        return $diff->format('%H:%I:%S');
    }

    public function getIsFullyApprovedAttribute(): bool
    {
        return $this->approved_keluar && $this->approved_masuk && $this->approved_supervisor;
    }

    public function getIsTrackingCompleteAttribute(): bool
    {
        return $this->isInventarisComplete() && $this->isKuesionerComplete() && $this->isPemeriksaanComplete();
    }

    public function isInventarisComplete(): bool
    {
        return !empty($this->inventaris_status) && $this->inventaris_checked_at !== null;
    }

    public function isKuesionerComplete(): bool
    {
        return !empty($this->kuesioner_status) && $this->kuesioner_checked_at !== null;
    }

    public function isPemeriksaanComplete(): bool
    {
        return !empty($this->pemeriksaan_status) && $this->pemeriksaan_checked_at !== null;
    }

    public function getInventarisCompletionPercentage(): int
    {
        if (empty($this->inventaris_status)) return 0;
        
        $total = count($this->inventaris_status);
        $completed = count(array_filter($this->inventaris_status, function($status) {
            return $status['status'] === 'checked';
        }));
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function getKuesionerCompletionPercentage(): int
    {
        if (empty($this->kuesioner_status)) return 0;
        
        $total = count($this->kuesioner_status);
        $completed = count(array_filter($this->kuesioner_status, function($status) {
            return $status['status'] === 'completed';
        }));
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function getPemeriksaanCompletionPercentage(): int
    {
        if (empty($this->pemeriksaan_status)) return 0;
        
        $total = count($this->pemeriksaan_status);
        $completed = count(array_filter($this->pemeriksaan_status, function($status) {
            return $status['status'] === 'checked';
        }));
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    // Scopes
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('waktu_mulai_handover', today());
    }

    public function scopeByArea(Builder $query, $areaId): Builder
    {
        return $query->where('area_patrol_id', $areaId);
    }

    // Methods
    public function canBeStarted(): bool
    {
        return $this->status === 'pending' && 
               $this->waktu_mulai_handover <= now() &&
               $this->petugas_masuk_id !== null;
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress' && 
               $this->approved_keluar && 
               $this->approved_masuk &&
               $this->is_tracking_complete;
    }

    public function startHandover(): void
    {
        $this->update([
            'status' => 'in_progress',
            'waktu_mulai_handover' => now()
        ]);
        
        // Initialize tracking data
        $this->initializeTrackingData();
    }

    public function initializeTrackingData(): void
    {
        // Initialize inventaris tracking
        $inventaris = $this->timKeluar->inventaris ?? collect();
        $inventarisStatus = [];
        foreach ($inventaris as $item) {
            $inventarisStatus[] = [
                'id' => $item->id,
                'nama' => $item->nama,
                'kategori' => $item->kategori ?? 'Umum',
                'status' => 'pending', // pending, checked, missing, damaged
                'catatan' => null,
                'checked_at' => null,
                'checked_by' => null
            ];
        }

        // Initialize kuesioner tracking
        $kuesioners = $this->timKeluar->kuesioners ?? collect();
        $kuesionerStatus = [];
        foreach ($kuesioners as $kuesioner) {
            $kuesionerStatus[] = [
                'id' => $kuesioner->id,
                'judul' => $kuesioner->judul,
                'status' => 'pending', // pending, completed
                'completed_at' => null,
                'completed_by' => null
            ];
        }

        // Initialize pemeriksaan tracking
        $pemeriksaans = $this->timKeluar->pemeriksaans ?? collect();
        $pemeriksaanStatus = [];
        foreach ($pemeriksaans as $pemeriksaan) {
            $pemeriksaanStatus[] = [
                'id' => $pemeriksaan->id,
                'nama' => $pemeriksaan->nama,
                'status' => 'pending', // pending, checked, failed
                'catatan' => null,
                'checked_at' => null,
                'checked_by' => null
            ];
        }

        $this->update([
            'inventaris_status' => $inventarisStatus,
            'kuesioner_status' => $kuesionerStatus,
            'pemeriksaan_status' => $pemeriksaanStatus
        ]);
    }

    public function updateInventarisStatus(int $inventarisId, string $status, ?string $catatan = null): void
    {
        $inventarisStatus = $this->inventaris_status ?? [];
        
        foreach ($inventarisStatus as &$item) {
            if ($item['id'] == $inventarisId) {
                $item['status'] = $status;
                $item['catatan'] = $catatan;
                $item['checked_at'] = now()->toISOString();
                $item['checked_by'] = auth()->id();
                break;
            }
        }
        
        $this->update(['inventaris_status' => $inventarisStatus]);
        
        // Check if all inventaris are completed
        $allCompleted = true;
        foreach ($inventarisStatus as $item) {
            if ($item['status'] === 'pending') {
                $allCompleted = false;
                break;
            }
        }
        
        if ($allCompleted) {
            $this->update([
                'inventaris_checked_at' => now(),
                'inventaris_checked_by' => auth()->id()
            ]);
        }
    }

    public function updateKuesionerStatus(int $kuesionerId, string $status): void
    {
        $kuesionerStatus = $this->kuesioner_status ?? [];
        
        foreach ($kuesionerStatus as &$item) {
            if ($item['id'] == $kuesionerId) {
                $item['status'] = $status;
                $item['completed_at'] = now()->toISOString();
                $item['completed_by'] = auth()->id();
                break;
            }
        }
        
        $this->update(['kuesioner_status' => $kuesionerStatus]);
        
        // Check if all kuesioner are completed
        $allCompleted = true;
        foreach ($kuesionerStatus as $item) {
            if ($item['status'] === 'pending') {
                $allCompleted = false;
                break;
            }
        }
        
        if ($allCompleted) {
            $this->update([
                'kuesioner_checked_at' => now(),
                'kuesioner_checked_by' => auth()->id()
            ]);
        }
    }

    public function updatePemeriksaanStatus(int $pemeriksaanId, string $status, ?string $catatan = null): void
    {
        $pemeriksaanStatus = $this->pemeriksaan_status ?? [];
        
        foreach ($pemeriksaanStatus as &$item) {
            if ($item['id'] == $pemeriksaanId) {
                $item['status'] = $status;
                $item['catatan'] = $catatan;
                $item['checked_at'] = now()->toISOString();
                $item['checked_by'] = auth()->id();
                break;
            }
        }
        
        $this->update(['pemeriksaan_status' => $pemeriksaanStatus]);
        
        // Check if all pemeriksaan are completed
        $allCompleted = true;
        foreach ($pemeriksaanStatus as $item) {
            if ($item['status'] === 'pending') {
                $allCompleted = false;
                break;
            }
        }
        
        if ($allCompleted) {
            $this->update([
                'pemeriksaan_checked_at' => now(),
                'pemeriksaan_checked_by' => auth()->id()
            ]);
        }
    }

    public function completeHandover(): void
    {
        $this->update([
            'status' => 'completed',
            'waktu_selesai_handover' => now()
        ]);
    }

    public function cancelHandover(?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'catatan_supervisor' => $reason ? "Dibatalkan: {$reason}" : 'Dibatalkan'
        ]);
    }
}