<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Checkpoint extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'rute_patrol_id',
        'nama',
        'qr_code',
        'deskripsi',
        'urutan',
        'alamat',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $appends = ['hash_id'];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
        
        // Auto-generate QR code if empty
        static::creating(function ($checkpoint) {
            if (empty($checkpoint->qr_code)) {
                $checkpoint->qr_code = 'CP-' . strtoupper(uniqid());
            }
            
            // Auto-set urutan if empty
            if (empty($checkpoint->urutan)) {
                $maxUrutan = static::where('rute_patrol_id', $checkpoint->rute_patrol_id)->max('urutan');
                $checkpoint->urutan = ($maxUrutan ?? 0) + 1;
            }
        });
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function rutePatrol(): BelongsTo
    {
        return $this->belongsTo(RutePatrol::class);
    }

    public function asets()
    {
        return $this->belongsToMany(AsetKawasan::class, 'aset_checkpoint')
            ->withPivot('catatan')
            ->withTimestamps();
    }
}
