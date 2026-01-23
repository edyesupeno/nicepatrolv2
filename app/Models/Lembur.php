<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Lembur extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'approved_by',
        'tanggal_lembur',
        'jam_mulai',
        'jam_selesai',
        'total_jam',
        'alasan_lembur',
        'deskripsi_pekerjaan',
        'status',
        'catatan_approval',
        'approved_at',
        'tarif_lembur_per_jam',
        'total_upah_lembur',
    ];

    protected $casts = [
        'tanggal_lembur' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'total_jam' => 'decimal:2',
        'tarif_lembur_per_jam' => 'decimal:2',
        'total_upah_lembur' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['hash_id'];

    /**
     * Global scope untuk multi-tenancy
     */
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id && !auth()->user()->isSuperAdmin()) {
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
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relasi ke Karyawan
     */
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    /**
     * Relasi ke User yang approve
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan project
     */
    public function scopeProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope untuk filter berdasarkan karyawan
     */
    public function scopeKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_lembur', $tanggal);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeBulan($query, $tahun, $bulan)
    {
        return $query->whereYear('tanggal_lembur', $tahun)
                    ->whereMonth('tanggal_lembur', $bulan);
    }

    /**
     * Hitung total jam lembur otomatis
     */
    public function hitungTotalJam()
    {
        if ($this->jam_mulai && $this->jam_selesai) {
            $mulai = \Carbon\Carbon::parse($this->jam_mulai);
            $selesai = \Carbon\Carbon::parse($this->jam_selesai);
            
            // Jika jam selesai lebih kecil dari jam mulai, berarti lewat tengah malam
            if ($selesai->lt($mulai)) {
                $selesai->addDay();
            }
            
            $this->total_jam = $selesai->diffInHours($mulai, true);
        }
    }

    /**
     * Hitung total upah lembur
     */
    public function hitungTotalUpah()
    {
        if ($this->total_jam && $this->tarif_lembur_per_jam) {
            $this->total_upah_lembur = $this->total_jam * $this->tarif_lembur_per_jam;
        }
    }

    /**
     * Approve lembur
     */
    public function approve($approvedBy, $catatan = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'catatan_approval' => $catatan,
        ]);

        // Hitung upah lembur jika belum dihitung
        if (!$this->total_upah_lembur) {
            $this->hitungTotalUpah();
            $this->save();
        }
    }

    /**
     * Reject lembur
     */
    public function reject($approvedBy, $catatan)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'catatan_approval' => $catatan,
        ]);
    }

    /**
     * Check apakah bisa diedit
     */
    public function canEdit()
    {
        return $this->status === 'pending';
    }

    /**
     * Check apakah bisa dihapus
     */
    public function canDelete()
    {
        return $this->status === 'pending';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Unknown'
        };
    }
}