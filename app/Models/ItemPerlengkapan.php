<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class ItemPerlengkapan extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'item_perlengkapans';

    protected $fillable = [
        'kategori_perlengkapan_id',
        'created_by',
        'nama_item',
        'deskripsi',
        'satuan',
        'stok_awal',
        'stok_tersedia',
        'stok_minimum',
        'harga_satuan',
        'foto_item',
        'is_active',
    ];

    protected $casts = [
        'stok_awal' => 'integer',
        'stok_tersedia' => 'integer',
        'stok_minimum' => 'integer',
        'harga_satuan' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope - filter through kategori relationship
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->whereHas('kategori', function($query) {
                    $query->where('perusahaan_id', auth()->user()->perusahaan_id);
                });
            }
        });
    }

    // Relationships
    public function kategori()
    {
        return $this->belongsTo(KategoriPerlengkapan::class, 'kategori_perlengkapan_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function penyerahanItems()
    {
        return $this->hasMany(PenyerahanPerlengkapanItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stok_tersedia', '<=', 'stok_minimum')
                    ->where('stok_minimum', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stok_tersedia', '<=', 0);
    }

    // Accessors
    public function getIsLowStockAttribute()
    {
        return $this->stok_minimum > 0 && $this->stok_tersedia <= $this->stok_minimum;
    }

    public function getIsOutOfStockAttribute()
    {
        return $this->stok_tersedia <= 0;
    }

    public function getStokStatusAttribute()
    {
        if ($this->is_out_of_stock) {
            return 'out_of_stock';
        } elseif ($this->is_low_stock) {
            return 'low_stock';
        } else {
            return 'available';
        }
    }

    public function getStokStatusColorAttribute()
    {
        return match($this->stok_status) {
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'available' => 'green',
            default => 'gray'
        };
    }

    public function getFormattedHargaSatuanAttribute()
    {
        return $this->harga_satuan ? 'Rp ' . number_format($this->harga_satuan, 0, ',', '.') : '-';
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_item) {
            return asset('storage/' . $this->foto_item);
        }
        return null;
    }

    public function getTotalDiserahkanAttribute()
    {
        return $this->penyerahanItems()->sum('jumlah_diserahkan');
    }

    public function getTotalDikembalikanAttribute()
    {
        return $this->penyerahanItems()->sum('jumlah_dikembalikan');
    }

    // Methods
    public function updateStok($jumlah, $operasi = 'kurang', $keterangan = null, $referensiTipe = null, $referensiId = null)
    {
        $stokSebelum = $this->stok_tersedia;
        
        if ($operasi === 'kurang') {
            $this->stok_tersedia = max(0, $this->stok_tersedia - $jumlah);
            $tipeTransaksi = 'keluar';
        } else {
            $this->stok_tersedia += $jumlah;
            $tipeTransaksi = 'masuk';
        }
        
        $stokSesudah = $this->stok_tersedia;
        $this->save();
        
        // Record stock history
        $this->recordStockHistory(
            $tipeTransaksi,
            $jumlah,
            $stokSebelum,
            $stokSesudah,
            $keterangan ?? ($operasi === 'kurang' ? 'Pengurangan stok' : 'Penambahan stok'),
            $referensiTipe,
            $referensiId
        );
    }

    public function recordStockHistory($tipeTransaksi, $jumlah, $stokSebelum, $stokSesudah, $keterangan, $referensiTipe = null, $referensiId = null)
    {
        \App\Models\ItemStockHistory::create([
            'item_perlengkapan_id' => $this->id,
            'perusahaan_id' => $this->kategori->perusahaan_id,
            'tipe_transaksi' => $tipeTransaksi,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'keterangan' => $keterangan,
            'referensi_tipe' => $referensiTipe,
            'referensi_id' => $referensiId,
            'created_by' => auth()->id() ?? 1
        ]);
    }
}
