<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class KategoriPerlengkapan extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'kategori_perlengkapans';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'created_by',
        'nama_kategori',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->id()) {
                // Get perusahaan_id without loading full user model
                $perusahaanId = auth()->user()->perusahaan_id ?? null;
                if ($perusahaanId) {
                    $builder->where('perusahaan_id', $perusahaanId);
                }
            }
        });
    }

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(ItemPerlengkapan::class);
    }

    public function activeItems()
    {
        return $this->hasMany(ItemPerlengkapan::class)->where('is_active', true);
    }

    public function penyerahans()
    {
        return $this->hasMany(PenyerahanPerlengkapan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    // Accessors
    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getTotalStokAttribute()
    {
        return $this->items()->sum('stok_tersedia');
    }

    public function getLowStockItemsAttribute()
    {
        return $this->items()
            ->whereColumn('stok_tersedia', '<=', 'stok_minimum')
            ->where('stok_minimum', '>', 0)
            ->count();
    }
}
