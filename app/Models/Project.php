<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Project extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'kantor_id',
        'nama',
        'timezone',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'is_active',
        'guest_book_mode',
        'enable_questionnaire',
        'enable_guest_card',
        'batas_cuti_tahunan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'enable_questionnaire' => 'boolean',
        'enable_guest_card' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected $appends = ['hash_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
        
        // CRITICAL: Project scope - HANYA untuk non-admin dan non-superadmin
        static::addGlobalScope('project_access', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // HANYA user biasa yang dibatasi project access
                // Admin dan Superadmin bisa lihat semua project di perusahaan mereka
                if (!$user->isSuperAdmin() && !$user->isAdmin()) {
                    $projectIds = $user->getAccessibleProjectIds();
                    if (!empty($projectIds)) {
                        $builder->whereIn('id', $projectIds);
                    } else {
                        // Jika tidak ada project_id, return empty result
                        $builder->whereRaw('1 = 0');
                    }
                }
                // Admin dan Superadmin tidak ada filter project - bisa lihat semua
            }
        });
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function kantor(): BelongsTo
    {
        return $this->belongsTo(Kantor::class);
    }

    public function jabatans(): BelongsToMany
    {
        return $this->belongsToMany(Jabatan::class, 'jabatan_project')
                    ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withPivot('jabatan_id', 'tanggal_mulai', 'tanggal_selesai', 'is_active')
            ->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(ProjectContact::class);
    }

    public function activeContacts(): HasMany
    {
        return $this->hasMany(ProjectContact::class)->where('is_active', true);
    }

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }

    public function activeKaryawans(): HasMany
    {
        return $this->hasMany(Karyawan::class)->where('is_active', true);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function areaPatrols(): HasMany
    {
        return $this->hasMany(AreaPatrol::class);
    }

    public function guestCardAreas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'project_guest_card_areas')
                    ->withTimestamps();
    }

    /**
     * Get struktur jabatan dengan jumlah karyawan per jabatan
     */
    public function getStrukturJabatanAttribute()
    {
        try {
            // Get all jabatans for this project
            $jabatans = $this->jabatans;
            
            if ($jabatans->isEmpty()) {
                return [];
            }

            $struktur = [];

            foreach ($jabatans as $jabatan) {
                // Count karyawans with this jabatan in this project
                $karyawanCount = \App\Models\Karyawan::where('project_id', $this->id)
                    ->where('jabatan_id', $jabatan->id)
                    ->where('is_active', true)
                    ->count();

                // Count users assigned to this project with this jabatan
                $userCount = $this->users()
                    ->wherePivot('jabatan_id', $jabatan->id)
                    ->wherePivot('is_active', true)
                    ->count();

                $totalCount = $karyawanCount + $userCount;

                $struktur[] = [
                    'jabatan' => $jabatan->nama,
                    'jumlah' => $totalCount,
                ];
            }

            return $struktur;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get total karyawan di project
     */
    public function getTotalKaryawanAttribute()
    {
        try {
            // Count karyawans directly assigned to this project
            $karyawanCount = \App\Models\Karyawan::where('project_id', $this->id)
                ->where('is_active', true)
                ->count();

            // Count users assigned to this project via pivot
            $userCount = $this->users()
                ->wherePivot('is_active', true)
                ->count();

            return $karyawanCount + $userCount;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
