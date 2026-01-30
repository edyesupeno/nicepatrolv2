<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasHashId;

class ItemStockHistory extends Model
{
    use HasHashId;

    protected $fillable = [
        'item_perlengkapan_id',
        'perusahaan_id',
        'tipe_transaksi',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'referensi_tipe',
        'referensi_id',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Global scope for multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function ($builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    // Relationships
    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemPerlengkapan::class, 'item_perlengkapan_id');
    }

    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getTipeTransaksiTextAttribute(): string
    {
        return match($this->tipe_transaksi) {
            'masuk' => 'Stok Masuk',
            'keluar' => 'Stok Keluar',
            'adjustment' => 'Penyesuaian',
            'return' => 'Pengembalian',
            default => ucfirst($this->tipe_transaksi)
        };
    }

    public function getTipeTransaksiColorAttribute(): string
    {
        return match($this->tipe_transaksi) {
            'masuk' => 'text-green-600 bg-green-100',
            'keluar' => 'text-red-600 bg-red-100',
            'adjustment' => 'text-blue-600 bg-blue-100',
            'return' => 'text-purple-600 bg-purple-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    public function getTipeTransaksiIconAttribute(): string
    {
        return match($this->tipe_transaksi) {
            'masuk' => 'fas fa-arrow-up',
            'keluar' => 'fas fa-arrow-down',
            'adjustment' => 'fas fa-edit',
            'return' => 'fas fa-undo',
            default => 'fas fa-exchange-alt'
        };
    }

    public function getFormattedJumlahAttribute(): string
    {
        $prefix = in_array($this->tipe_transaksi, ['masuk', 'return']) ? '+' : '-';
        return $prefix . number_format($this->jumlah);
    }
}