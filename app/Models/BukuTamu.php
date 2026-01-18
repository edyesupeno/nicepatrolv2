<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class BukuTamu extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'input_by',
        'nama_tamu',
        'perusahaan_tamu',
        'keperluan',
        'bertemu',
        'foto',
        'foto_identitas',
        'kontak_darurat_nama',
        'kontak_darurat_telepon',
        'kontak_darurat_hubungan',
        'status',
        'check_in',
        'check_out',
        'qr_code',
        'no_kartu_pinjam',
        'catatan',
        'keterangan_tambahan',
        'is_active',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
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

        // Auto-generate QR code when creating
        static::creating(function ($bukuTamu) {
            if (!$bukuTamu->qr_code) {
                $bukuTamu->qr_code = 'GT-' . strtoupper(uniqid());
            }
            if (!$bukuTamu->check_in) {
                $bukuTamu->check_in = now();
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

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'sedang_berkunjung' => 'Sedang Berkunjung',
            'sudah_keluar' => 'Sudah Keluar',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'sedang_berkunjung' => 'green',
            'sudah_keluar' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        $icons = [
            'sedang_berkunjung' => 'fas fa-user-check',
            'sudah_keluar' => 'fas fa-user-times',
        ];

        return $icons[$this->status] ?? 'fas fa-user';
    }

    /**
     * Get duration of visit
     */
    public function getDurationAttribute(): ?string
    {
        if (!$this->check_in) {
            return null;
        }

        $endTime = $this->check_out ?? now();
        $duration = $this->check_in->diff($endTime);

        if ($duration->days > 0) {
            return $duration->days . ' hari ' . $duration->h . ' jam ' . $duration->i . ' menit';
        } elseif ($duration->h > 0) {
            return $duration->h . ' jam ' . $duration->i . ' menit';
        } else {
            return $duration->i . ' menit';
        }
    }

    /**
     * Check if guest is currently visiting
     */
    public function getIsVisitingAttribute(): bool
    {
        return $this->status === 'sedang_berkunjung';
    }

    /**
     * Get identity photo URL
     */
    public function getFotoIdentitasUrlAttribute(): ?string
    {
        if (!$this->foto_identitas) {
            return null;
        }
        return config('app.url') . '/storage/' . $this->foto_identitas;
    }

    /**
     * Get photo URL
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }
        return config('app.url') . '/storage/' . $this->foto;
    }

    /**
     * Scope for currently visiting guests
     */
    public function scopeVisiting($query)
    {
        return $query->where('status', 'sedang_berkunjung');
    }

    /**
     * Scope for guests who have left
     */
    public function scopeLeft($query)
    {
        return $query->where('status', 'sudah_keluar');
    }

    /**
     * Scope for today's visitors
     */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    /**
     * Scope for this week's visitors
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('check_in', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Mark guest as checked out
     */
    public function checkOut($catatan = null)
    {
        $this->update([
            'status' => 'sudah_keluar',
            'check_out' => now(),
            'catatan' => $catatan ?: $this->catatan,
        ]);
    }
}