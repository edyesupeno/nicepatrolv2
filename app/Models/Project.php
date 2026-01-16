<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    ];

    protected $casts = [
        'is_active' => 'boolean',
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

    /**
     * Get struktur jabatan dengan jumlah karyawan per jabatan
     */
    public function getStrukturJabatanAttribute()
    {
        try {
            // Get jabatan yang terkait dengan project ini via pivot table
            $jabatans = $this->jabatans;
            
            if ($jabatans->isEmpty()) {
                return [];
            }

            $struktur = [];

            foreach ($jabatans as $jabatan) {
                // Hitung jumlah karyawan dengan jabatan ini di project ini
                $jumlah = $this->users()
                    ->wherePivot('jabatan_id', $jabatan->id)
                    ->wherePivot('is_active', true)
                    ->count();

                $struktur[] = [
                    'jabatan' => $jabatan->nama,
                    'jumlah' => $jumlah,
                ];
            }

            return $struktur;
        } catch (\Exception $e) {
            // Return empty array if table doesn't exist or any error
            return [];
        }
    }

    /**
     * Get total karyawan di project
     */
    public function getTotalKaryawanAttribute()
    {
        try {
            return $this->users()
                ->wherePivot('is_active', true)
                ->count();
        } catch (\Exception $e) {
            // Return 0 if table doesn't exist or any error
            return 0;
        }
    }
}
