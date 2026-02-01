<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Reimbursement extends Model
{
    use HasFactory, HasHashId;

    protected $fillable = [
        'nomor_reimbursement',
        'perusahaan_id',
        'project_id',
        'karyawan_id',
        'user_id',
        'judul_pengajuan',
        'deskripsi',
        'jumlah_pengajuan',
        'jumlah_disetujui',
        'kategori',
        'tanggal_pengajuan',
        'tanggal_kejadian',
        'status',
        'prioritas',
        'bukti_dokumen',
        'catatan_pengaju',
        'catatan_reviewer',
        'catatan_approver',
        'alasan_penolakan',
        'reviewed_by',
        'reviewed_at',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'rekening_id',
        'nomor_transaksi_pembayaran',
        'is_urgent',
        'is_recurring',
        'recurring_period'
    ];

    protected $casts = [
        'bukti_dokumen' => 'array',
        'jumlah_pengajuan' => 'decimal:2',
        'jumlah_disetujui' => 'decimal:2',
        'tanggal_pengajuan' => 'date',
        'tanggal_kejadian' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'is_urgent' => 'boolean',
        'is_recurring' => 'boolean'
    ];

    protected $appends = ['hash_id', 'status_label', 'kategori_label', 'prioritas_label'];

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

        static::creating(function ($model) {
            if (!$model->perusahaan_id && auth()->check()) {
                $model->perusahaan_id = auth()->user()->perusahaan_id;
            }
            if (!$model->user_id && auth()->check()) {
                $model->user_id = auth()->id();
            }
            if (!$model->nomor_reimbursement) {
                $model->nomor_reimbursement = static::generateNomorReimbursement();
            }
        });
    }

    /**
     * Generate nomor reimbursement otomatis
     */
    public static function generateNomorReimbursement()
    {
        $prefix = 'RMB';
        $year = date('Y');
        $month = date('m');
        
        $lastNumber = static::where('nomor_reimbursement', 'like', $prefix . $year . $month . '%')
            ->orderBy('nomor_reimbursement', 'desc')
            ->first();

        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber->nomor_reimbursement, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }

        return $prefix . $year . $month . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    /**
     * Scopes
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    public function scopeByPeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['submitted', 'reviewed']);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Accessors
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'reviewed' => 'Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'paid' => 'Dibayar',
            'cancelled' => 'Dibatalkan'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getKategoriLabelAttribute()
    {
        $labels = [
            'transportasi' => 'Transportasi',
            'akomodasi' => 'Akomodasi',
            'konsumsi' => 'Konsumsi',
            'komunikasi' => 'Komunikasi',
            'peralatan' => 'Peralatan',
            'medis' => 'Medis',
            'training' => 'Training',
            'operasional' => 'Operasional',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$this->kategori] ?? $this->kategori;
    }

    public function getPrioritasLabelAttribute()
    {
        $labels = [
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak'
        ];

        return $labels[$this->prioritas] ?? $this->prioritas;
    }

    /**
     * Static methods untuk dropdown options
     */
    public static function getAvailableStatus()
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Diajukan',
            'reviewed' => 'Direview',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'paid' => 'Dibayar',
            'cancelled' => 'Dibatalkan'
        ];
    }

    public static function getAvailableKategori()
    {
        return [
            'transportasi' => 'Transportasi',
            'akomodasi' => 'Akomodasi',
            'konsumsi' => 'Konsumsi',
            'komunikasi' => 'Komunikasi',
            'peralatan' => 'Peralatan',
            'medis' => 'Medis',
            'training' => 'Training',
            'operasional' => 'Operasional',
            'lainnya' => 'Lainnya'
        ];
    }

    public static function getAvailablePrioritas()
    {
        return [
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak'
        ];
    }

    /**
     * Helper methods
     */
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'submitted']);
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft';
    }

    public function canBeReviewed()
    {
        return $this->status === 'submitted';
    }

    public function canBeApproved()
    {
        return in_array($this->status, ['submitted', 'reviewed']);
    }

    public function canBeRejected()
    {
        return in_array($this->status, ['submitted', 'reviewed']);
    }

    public function canBePaid()
    {
        return $this->status === 'approved';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['draft', 'submitted', 'reviewed']);
    }

    public function getTotalDokumen()
    {
        return is_array($this->bukti_dokumen) ? count($this->bukti_dokumen) : 0;
    }

    public function getStatusBadgeClass()
    {
        $classes = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'reviewed' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'paid' => 'bg-purple-100 text-purple-800',
            'cancelled' => 'bg-gray-100 text-gray-800'
        ];

        return $classes[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPrioritasBadgeClass()
    {
        $classes = [
            'low' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800'
        ];

        return $classes[$this->prioritas] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Workflow methods
     */
    public function approve($approverId, $jumlahDisetujui = null, $catatan = null)
    {
        if (!$this->canBeApproved()) {
            throw new \Exception('Reimbursement tidak dapat disetujui dengan status saat ini: ' . $this->status_label);
        }

        $this->update([
            'status' => 'approved',
            'jumlah_disetujui' => $jumlahDisetujui ?? $this->jumlah_pengajuan,
            'catatan_approver' => $catatan,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);

        return $this;
    }

    public function reject($approverId, $alasanPenolakan)
    {
        if (!$this->canBeRejected()) {
            throw new \Exception('Reimbursement tidak dapat ditolak dengan status saat ini: ' . $this->status_label);
        }

        $this->update([
            'status' => 'rejected',
            'alasan_penolakan' => $alasanPenolakan,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);

        return $this;
    }

    public function review($reviewerId, $catatan = null)
    {
        if (!$this->canBeReviewed()) {
            throw new \Exception('Reimbursement tidak dapat direview dengan status saat ini: ' . $this->status_label);
        }

        $this->update([
            'status' => 'reviewed',
            'catatan_reviewer' => $catatan,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now()
        ]);

        return $this;
    }

    public function markAsPaid($paidById, $rekeningId = null, $nomorTransaksi = null)
    {
        if (!$this->canBePaid()) {
            throw new \Exception('Reimbursement tidak dapat dibayar dengan status saat ini: ' . $this->status_label);
        }

        $this->update([
            'status' => 'paid',
            'paid_by' => $paidById,
            'paid_at' => now(),
            'rekening_id' => $rekeningId,
            'nomor_transaksi_pembayaran' => $nomorTransaksi
        ]);

        return $this;
    }
}