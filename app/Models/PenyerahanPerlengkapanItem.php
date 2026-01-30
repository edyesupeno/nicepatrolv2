<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class PenyerahanPerlengkapanItem extends Model
{
    use HasFactory, HasHashId;

    protected $table = 'penyerahan_perlengkapan_items';

    protected $fillable = [
        'penyerahan_perlengkapan_id',
        'item_perlengkapan_id',
        'karyawan_id',
        'jumlah_diserahkan',
        'jumlah_dikembalikan',
        'kondisi_saat_diserahkan',
        'kondisi_saat_dikembalikan',
        'tanggal_pengembalian',
        'keterangan_item',
        'is_diserahkan',
        'tanggal_diserahkan',
        'status',
    ];

    protected $casts = [
        'jumlah_diserahkan' => 'integer',
        'jumlah_dikembalikan' => 'integer',
        'tanggal_pengembalian' => 'date',
        'tanggal_diserahkan' => 'datetime',
        'is_diserahkan' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope - filter through penyerahan relationship
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->whereHas('penyerahan', function($query) {
                    $query->where('perusahaan_id', auth()->user()->perusahaan_id);
                });
            }
        });
    }

    // Relationships
    public function penyerahan()
    {
        return $this->belongsTo(PenyerahanPerlengkapan::class, 'penyerahan_perlengkapan_id');
    }

    public function item()
    {
        return $this->belongsTo(ItemPerlengkapan::class, 'item_perlengkapan_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDiserahkan($query)
    {
        return $query->where('status', 'diserahkan');
    }

    public function scopeDikembalikan($query)
    {
        return $query->where('status', 'dikembalikan');
    }

    // Accessors
    public function getSisaBelumKembaliAttribute()
    {
        return $this->jumlah_diserahkan - $this->jumlah_dikembalikan;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'diserahkan' => 'blue',
            'sebagian_dikembalikan' => 'yellow',
            'dikembalikan' => 'green',
            'hilang' => 'red',
            default => 'gray'
        };
    }

    public function getIsFullyReturnedAttribute()
    {
        return $this->jumlah_dikembalikan >= $this->jumlah_diserahkan;
    }

    public function getIsPartiallyReturnedAttribute()
    {
        return $this->jumlah_dikembalikan > 0 && $this->jumlah_dikembalikan < $this->jumlah_diserahkan;
    }

    // Methods
    public function updateStatus()
    {
        if ($this->jumlah_dikembalikan >= $this->jumlah_diserahkan) {
            $this->status = 'dikembalikan';
        } elseif ($this->jumlah_dikembalikan > 0) {
            $this->status = 'sebagian_dikembalikan';
        } else {
            $this->status = 'diserahkan';
        }
        $this->save();
    }
}
