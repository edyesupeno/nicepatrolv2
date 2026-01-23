<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class Resign extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'created_by',
        'tanggal_pengajuan',
        'tanggal_resign_efektif',
        'jenis_resign',
        'alasan_resign',
        'catatan_approval',
        'status',
        'approved_by',
        'approved_at',
        'handover_notes',
        'handover_items',
        'is_blacklist',
        'blacklist_reason',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_resign_efektif' => 'date',
        'approved_at' => 'datetime',
        'handover_items' => 'array',
        'is_blacklist' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Global scope untuk multi-tenancy
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

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Accessors & Mutators
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>',
            'approved' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>',
            'rejected' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    public function getJenisResignBadgeAttribute()
    {
        return match($this->jenis_resign) {
            'resign_pribadi' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Resign Pribadi</span>',
            'kontrak_habis' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Kontrak Habis</span>',
            'phk' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">PHK</span>',
            'pensiun' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Pensiun</span>',
            'meninggal_dunia' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Meninggal Dunia</span>',
            'lainnya' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Lainnya</span>',
            default => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>',
        };
    }

    // Methods
    public function canEdit(): bool
    {
        return $this->status === 'pending';
    }

    public function canDelete(): bool
    {
        return $this->status === 'pending';
    }

    public function canApprove(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $user = auth()->user();
        
        if (!$user) {
            return false;
        }
        
        // Roles that can approve resign requests
        $approverRoles = [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER_PROJECT,
            User::ROLE_ADMIN_PROJECT,
            User::ROLE_ADMIN_BRANCH,
            User::ROLE_ADMIN_HSSE,
        ];

        return $user->hasRole($approverRoles);
    }

    public function calculateNoticePeriod(): int
    {
        return $this->tanggal_pengajuan->diffInDays($this->tanggal_resign_efektif);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByJenisResign($query, $jenisResign)
    {
        return $query->where('jenis_resign', $jenisResign);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_resign_efektif', [$startDate, $endDate]);
    }
}