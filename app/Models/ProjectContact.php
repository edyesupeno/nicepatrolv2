<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class ProjectContact extends Model
{
    use HasHashId;

    protected $fillable = [
        'project_id',
        'perusahaan_id',
        'nama_kontak',
        'jabatan_kontak',
        'nomor_telepon',
        'email',
        'jenis_kontak',
        'keterangan',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
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

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Get jenis kontak label
     */
    public function getJenisKontakLabelAttribute(): string
    {
        $labels = [
            'polisi' => 'Polisi',
            'pemadam_kebakaran' => 'Pemadam Kebakaran',
            'ambulans' => 'Ambulans/Medis',
            'security' => 'Security',
            'manager_project' => 'Manager Project',
            'supervisor' => 'Supervisor',
            'teknisi' => 'Teknisi',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis_kontak] ?? $this->jenis_kontak;
    }

    /**
     * Get jenis kontak icon
     */
    public function getJenisKontakIconAttribute(): string
    {
        $icons = [
            'polisi' => 'fas fa-shield-alt',
            'pemadam_kebakaran' => 'fas fa-fire-extinguisher',
            'ambulans' => 'fas fa-ambulance',
            'security' => 'fas fa-user-shield',
            'manager_project' => 'fas fa-user-tie',
            'supervisor' => 'fas fa-user-cog',
            'teknisi' => 'fas fa-tools',
            'lainnya' => 'fas fa-phone',
        ];

        return $icons[$this->jenis_kontak] ?? 'fas fa-phone';
    }

    /**
     * Get jenis kontak color
     */
    public function getJenisKontakColorAttribute(): string
    {
        $colors = [
            'polisi' => 'blue',
            'pemadam_kebakaran' => 'red',
            'ambulans' => 'green',
            'security' => 'purple',
            'manager_project' => 'indigo',
            'supervisor' => 'yellow',
            'teknisi' => 'gray',
            'lainnya' => 'pink',
        ];

        return $colors[$this->jenis_kontak] ?? 'gray';
    }
}