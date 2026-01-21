<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Area extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'nama',
        'alamat',
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
                // Admin dan Superadmin bisa lihat semua area di perusahaan mereka
                if (!$user->isSuperAdmin() && !$user->isAdmin()) {
                    $projectIds = $user->getAccessibleProjectIds();
                    if (!empty($projectIds)) {
                        $builder->whereIn('project_id', $projectIds);
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function kuesionerTamus(): HasMany
    {
        return $this->hasMany(KuesionerTamu::class);
    }
}
