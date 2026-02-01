<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAdvanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_advance_id',
        'nomor_transaksi',
        'tipe',
        'jumlah',
        'tanggal_transaksi',
        'keterangan',
        'kategori_pengeluaran',
        'bukti_transaksi',
        'vendor_supplier',
        'saldo_sebelum',
        'saldo_sesudah',
        'created_by',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!$model->nomor_transaksi) {
                $model->nomor_transaksi = static::generateNomorTransaksi();
            }
        });
    }

    public static function generateNomorTransaksi(): string
    {
        $year = date('Y');
        $lastNumber = static::where('nomor_transaksi', 'like', "CAT-{$year}-%")
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_transaksi, -3);
            $newNum = $lastNum + 1;
        } else {
            $newNum = 1;
        }

        return "CAT-{$year}-" . str_pad($newNum, 3, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function cashAdvance()
    {
        return $this->belongsTo(CashAdvance::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Methods
    public function getTipeBadgeAttribute(): string
    {
        $badges = [
            'pencairan' => '<span class="badge bg-success">Pencairan</span>',
            'pengeluaran' => '<span class="badge bg-warning">Pengeluaran</span>',
            'pengembalian' => '<span class="badge bg-info">Pengembalian</span>',
        ];

        return $badges[$this->tipe] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getBuktiTransaksiUrlAttribute(): ?string
    {
        return $this->bukti_transaksi ? asset('storage/' . $this->bukti_transaksi) : null;
    }
}