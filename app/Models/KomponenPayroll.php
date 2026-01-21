<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class KomponenPayroll extends Model
{
    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'nama_komponen',
        'kode',
        'jenis',
        'kategori',
        'tipe_perhitungan',
        'nilai',
        'nilai_maksimal',
        'deskripsi',
        'kena_pajak',
        'boleh_edit',
        'aktif',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'nilai_maksimal' => 'decimal:2',
        'kena_pajak' => 'boolean',
        'boleh_edit' => 'boolean',
        'aktif' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    /**
     * Get hash ID
     */
    public function getHashIdAttribute()
    {
        return \Vinkla\Hashids\Facades\Hashids::encode($this->id);
    }

    /**
     * Route key name
     */
    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    /**
     * Resolve route binding
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($value)[0] ?? null;
        return $this->where('id', $id)->firstOrFail();
    }

    /**
     * Global scope untuk multi-tenancy
     */
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    /**
     * Relasi ke Perusahaan
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Relasi ke Project (nullable)
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Check if this component has maximum value limit
     */
    public function hasMaximumLimit(): bool
    {
        return !is_null($this->nilai_maksimal) && 
               in_array($this->tipe_perhitungan, ['Per Hari Masuk', 'Lembur Per Hari']);
    }

    /**
     * Calculate component value with maximum limit applied
     */
    public function calculateValue(float $baseValue): float
    {
        if ($this->hasMaximumLimit()) {
            return min($baseValue, $this->nilai_maksimal);
        }
        
        return $baseValue;
    }
}
