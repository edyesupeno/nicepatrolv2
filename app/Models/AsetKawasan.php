<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class AsetKawasan extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'kode_aset',
        'nama',
        'kategori',
        'merk',
        'model',
        'serial_number',
        'foto',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
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
        
        // Auto-generate kode aset if empty
        static::creating(function ($aset) {
            if (empty($aset->kode_aset)) {
                $aset->kode_aset = 'AST-' . strtoupper(uniqid());
            }
        });
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function checkpoints(): BelongsToMany
    {
        return $this->belongsToMany(Checkpoint::class, 'aset_checkpoint')
            ->withPivot('catatan')
            ->withTimestamps();
    }
}
