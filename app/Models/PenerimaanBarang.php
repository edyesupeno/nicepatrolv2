<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class PenerimaanBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'penerimaan_barangs';

    protected $fillable = [
        'perusahaan_id',
        'created_by',
        'project_id',
        'area_id',
        'pos',
        'nomor_penerimaan',
        'nama_barang',
        'kategori_barang',
        'jumlah_barang',
        'satuan',
        'kondisi_barang',
        'pengirim',
        'tujuan_departemen',
        'foto_barang',
        'tanggal_terima',
        'status',
        'petugas_penerima',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terima' => 'datetime',
        'jumlah_barang' => 'integer',
    ];

    protected $appends = ['hash_id'];

    // Hash ID untuk URL obfuscation (sesuai project standards)
    public function getHashIdAttribute()
    {
        return \Vinkla\Hashids\Facades\Hashids::encode($this->id);
    }

    public function getRouteKeyName()
    {
        return 'hash_id';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($value)[0] ?? null;
        return $this->where('id', $id)->firstOrFail();
    }

    // Multi-tenancy global scope (CRITICAL - sesuai project standards)
    protected static function booted(): void
    {
        // CRITICAL: Company scope - semua user hanya bisa lihat data perusahaan mereka
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
        
        // CRITICAL: User ownership scope - user hanya bisa lihat data yang mereka input sendiri
        static::addGlobalScope('user_ownership', function (Builder $builder) {
            if (auth()->check()) {
                $user = auth()->user();
                
                // HANYA admin dan superadmin yang bisa lihat semua data
                // User biasa HANYA bisa lihat data yang mereka input sendiri
                if (!$user->isSuperAdmin() && !$user->isAdmin()) {
                    $builder->where('created_by', $user->id);
                }
                // Admin dan Superadmin bisa lihat semua data di perusahaan mereka
            }
        });
    }

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_barang', $kategori);
    }

    public function scopeByKondisi($query, $kondisi)
    {
        return $query->where('kondisi_barang', $kondisi);
    }

    public function scopeByPengirim($query, $pengirim)
    {
        return $query->where('pengirim', $pengirim);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_terima', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_terima', now()->month)
                    ->whereYear('tanggal_terima', now()->year);
    }

    // Accessors
    public function getFormattedTanggalTerimaAttribute()
    {
        return $this->tanggal_terima ? $this->tanggal_terima->format('d/m/Y H:i') : '-';
    }

    public function getKondisiColorAttribute()
    {
        return match($this->kondisi_barang) {
            'Baik' => 'green',
            'Rusak' => 'red',
            'Segel Terbuka' => 'yellow',
            default => 'gray'
        };
    }

    public function getKategoriColorAttribute()
    {
        return match($this->kategori_barang) {
            'Dokumen' => 'blue',
            'Material' => 'green',
            'Elektronik' => 'purple',
            'Logistik' => 'orange',
            default => 'gray'
        };
    }
}