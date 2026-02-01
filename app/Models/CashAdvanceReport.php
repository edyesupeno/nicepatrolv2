<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_advance_id',
        'nomor_laporan',
        'tanggal_laporan',
        'total_pengeluaran',
        'sisa_saldo',
        'jumlah_dikembalikan',
        'ringkasan_penggunaan',
        'file_laporan',
        'status',
        'approved_by',
        'tanggal_approved',
        'catatan_approval',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'tanggal_approved' => 'date',
        'total_pengeluaran' => 'decimal:2',
        'sisa_saldo' => 'decimal:2',
        'jumlah_dikembalikan' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->nomor_laporan) {
                $model->nomor_laporan = static::generateNomorLaporan();
            }
        });
    }

    public static function generateNomorLaporan(): string
    {
        $year = date('Y');
        $lastNumber = static::where('nomor_laporan', 'like', "CAR-{$year}-%")
            ->orderBy('nomor_laporan', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_laporan, -3);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return "CAR-{$year}-" . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function cashAdvance()
    {
        return $this->belongsTo(CashAdvance::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper Methods
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'draft' => '<span class="badge bg-secondary">Draft</span>',
            'submitted' => '<span class="badge bg-warning">Menunggu Approval</span>',
            'approved' => '<span class="badge bg-success">Disetujui</span>',
            'rejected' => '<span class="badge bg-danger">Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getFileLaporanUrlAttribute(): ?string
    {
        return $this->file_laporan ? asset('storage/' . $this->file_laporan) : null;
    }
}