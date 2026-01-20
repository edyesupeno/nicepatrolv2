<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        
        // CRITICAL: Project scope untuk non-superadmin
        static::addGlobalScope('project', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // Jika bukan superadmin, batasi hanya area dari project mereka
                if (!$user->isSuperAdmin()) {
                    // Get project_id dari karyawan
                    if ($user->karyawan && $user->karyawan->project_id) {
                        $builder->where('project_id', $user->karyawan->project_id);
                    } else {
                        // Jika tidak ada project_id, return empty result
                        $builder->whereRaw('1 = 0');
                    }
                }
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
}
