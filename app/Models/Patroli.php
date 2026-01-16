<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Patroli extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'lokasi_id',
        'user_id',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    protected $appends = ['hash_id', 'tanggal'];

    // Accessor untuk tanggal (dari waktu_mulai)
    public function getTanggalAttribute()
    {
        return $this->waktu_mulai;
    }

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
        return $this->belongsTo(Kantor::class, 'lokasi_id');
    }
    
    // Alias untuk backward compatibility
    public function lokasi(): BelongsTo
    {
        return $this->kantor();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PatroliDetail::class);
    }
}
