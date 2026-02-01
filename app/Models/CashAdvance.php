<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class CashAdvance extends Model
{
    use HasFactory, HasHashId;

    protected $fillable = [
        'nomor_ca',
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'approved_by',
        'rekening_id',
        'jumlah_pengajuan',
        'saldo_tersedia',
        'total_terpakai',
        'sisa_saldo',
        'keperluan',
        'tanggal_pengajuan',
        'tanggal_approved',
        'batas_pertanggungjawaban',
        'status',
        'catatan_approval',
        'catatan_reject',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_approved' => 'date',
        'batas_pertanggungjawaban' => 'date',
        'jumlah_pengajuan' => 'decimal:2',
        'saldo_tersedia' => 'decimal:2',
        'total_terpakai' => 'decimal:2',
        'sisa_saldo' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id && !auth()->user()->isSuperAdmin()) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });

        static::creating(function ($model) {
            if (!$model->nomor_ca) {
                $model->nomor_ca = static::generateNomorCA();
            }
        });
    }

    public static function generateNomorCA(): string
    {
        $year = date('Y');
        $lastNumber = static::where('nomor_ca', 'like', "CA-{$year}-%")
            ->orderBy('nomor_ca', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_ca, -3);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return "CA-{$year}-" . str_pad($newNum, 3, '0', STR_PAD_LEFT);
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

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashAdvanceTransaction::class);
    }

    public function reports()
    {
        return $this->hasMany(CashAdvanceReport::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeNeedReport($query)
    {
        return $query->where('status', 'need_report');
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function needsReport(): bool
    {
        return $this->status === 'need_report';
    }

    public function canAddTransaction(): bool
    {
        return in_array($this->status, ['active', 'need_report']) && $this->sisa_saldo > 0;
    }

    public function updateSaldo(): void
    {
        $totalPengeluaran = $this->transactions()
            ->where('tipe', 'pengeluaran')
            ->sum('jumlah');

        $totalPengembalian = $this->transactions()
            ->where('tipe', 'pengembalian')
            ->sum('jumlah');

        $this->total_terpakai = $totalPengeluaran;
        $this->sisa_saldo = $this->saldo_tersedia - $totalPengeluaran + $totalPengembalian;
        
        // Update status jika saldo habis atau mendekati batas waktu
        if ($this->sisa_saldo <= 0 || $this->batas_pertanggungjawaban <= now()) {
            $this->status = 'need_report';
        }
        
        $this->save();
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Menunggu Approval</span>',
            'approved' => '<span class="badge bg-info">Disetujui</span>',
            'active' => '<span class="badge bg-success">Aktif</span>',
            'need_report' => '<span class="badge bg-danger">Perlu Laporan</span>',
            'completed' => '<span class="badge bg-secondary">Selesai</span>',
            'rejected' => '<span class="badge bg-dark">Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }
}