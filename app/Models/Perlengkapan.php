<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Perlengkapan extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'perlengkapans';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_id',
        'created_by',
        'kode_perlengkapan',
        'nama_perlengkapan',
        'kategori',
        'merk',
        'model',
        'tahun_pembelian',
        'harga_pembelian',
        'kondisi',
        'lokasi_penyimpanan',
        'keterangan',
        'foto_perlengkapan',
    ];

    protected $casts = [
        'tahun_pembelian' => 'integer',
        'harga_pembelian' => 'decimal:2',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope (CRITICAL - sesuai project standards)
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
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

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByKondisi($query, $kondisi)
    {
        return $query->where('kondisi', $kondisi);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByArea($query, $areaId)
    {
        return $query->where('area_id', $areaId);
    }

    // Accessors
    public function getFormattedHargaPembelianAttribute()
    {
        return $this->harga_pembelian ? 'Rp ' . number_format($this->harga_pembelian, 0, ',', '.') : '-';
    }

    public function getKondisiColorAttribute()
    {
        return match($this->kondisi) {
            'Baik' => 'green',
            'Rusak' => 'red',
            'Maintenance' => 'yellow',
            default => 'gray'
        };
    }

    public function getKategoriColorAttribute()
    {
        return match($this->kategori) {
            'Elektronik' => 'blue',
            'Peralatan' => 'green',
            'Kendaraan' => 'purple',
            'Furniture' => 'orange',
            default => 'gray'
        };
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_perlengkapan) {
            return asset('storage/' . $this->foto_perlengkapan);
        }
        return null;
    }

    public function getUmurAttribute()
    {
        if ($this->tahun_pembelian) {
            return now()->year - $this->tahun_pembelian;
        }
        return null;
    }
}