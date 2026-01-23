<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class Cuti extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'created_by',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_hari',
        'jenis_cuti',
        'alasan',
        'catatan_approval',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
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

    public function getJenisCutiBadgeAttribute()
    {
        return match($this->jenis_cuti) {
            'tahunan' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Cuti Tahunan</span>',
            'sakit' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Sakit</span>',
            'melahirkan' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">Melahirkan</span>',
            'menikah' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Menikah</span>',
            'khitan' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">Khitan</span>',
            'baptis' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">Baptis</span>',
            'keluarga_meninggal' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Keluarga Meninggal</span>',
            'lainnya' => '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">Lainnya</span>',
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
        
        // Roles that can approve leave requests (excluding superadmin as they can't access perusahaan routes)
        $approverRoles = [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER_PROJECT,
            User::ROLE_ADMIN_PROJECT,
            User::ROLE_ADMIN_BRANCH,
            User::ROLE_ADMIN_HSSE,
        ];

        return $user->hasRole($approverRoles);
    }

    public function calculateTotalHari(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    /**
     * Calculate remaining annual leave for the employee in current year
     */
    public function calculateSisaCutiTahunan(): array
    {
        if ($this->jenis_cuti !== 'tahunan') {
            return [
                'sisa_cuti' => null,
                'batas_cuti' => null,
                'cuti_terpakai' => null
            ];
        }

        $currentYear = now()->year;
        
        // Get total approved annual leave for this year
        $cutiTahunanTerpakai = static::where('karyawan_id', $this->karyawan_id)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $currentYear)
            ->sum('total_hari');
        
        // Get project's annual leave limit
        $batasCutiTahunan = $this->project->batas_cuti_tahunan ?? 12; // Default 12 days
        
        return [
            'sisa_cuti' => $batasCutiTahunan - $cutiTahunanTerpakai,
            'batas_cuti' => $batasCutiTahunan,
            'cuti_terpakai' => $cutiTahunanTerpakai
        ];
    }

    /**
     * Get remaining annual leave for a specific employee and project
     */
    public static function getSisaCutiTahunan($karyawanId, $projectId = null): array
    {
        $currentYear = now()->year;
        
        // Get total approved annual leave for this year
        $cutiTahunanTerpakai = static::where('karyawan_id', $karyawanId)
            ->where('jenis_cuti', 'tahunan')
            ->where('status', 'approved')
            ->whereYear('tanggal_mulai', $currentYear)
            ->sum('total_hari');
        
        // Get project's annual leave limit
        if ($projectId) {
            $project = Project::find($projectId);
            $batasCutiTahunan = $project->batas_cuti_tahunan ?? 12;
        } else {
            // Get from employee's current project
            $karyawan = Karyawan::find($karyawanId);
            $batasCutiTahunan = $karyawan->project->batas_cuti_tahunan ?? 12;
        }
        
        return [
            'sisa_cuti' => $batasCutiTahunan - $cutiTahunanTerpakai,
            'batas_cuti' => $batasCutiTahunan,
            'cuti_terpakai' => $cutiTahunanTerpakai
        ];
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

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
                    ->orWhereBetween('tanggal_selesai', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('tanggal_mulai', '<=', $startDate)
                          ->where('tanggal_selesai', '>=', $endDate);
                    });
    }
}