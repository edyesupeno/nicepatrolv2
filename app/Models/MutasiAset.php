<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class MutasiAset extends Model
{
    use HasFactory, HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'nomor_mutasi',
        'tanggal_mutasi',
        'asset_type',
        'asset_id',
        'karyawan_id',
        'project_asal_id',
        'project_tujuan_id',
        'keterangan',
        'alasan_mutasi',
        'status',
        'disetujui_oleh',
        'tanggal_persetujuan',
        'catatan_persetujuan',
        'dokumen_pendukung'
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'tanggal_persetujuan' => 'datetime'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });

        static::creating(function ($model) {
            if (!$model->perusahaan_id && auth()->check()) {
                $model->perusahaan_id = auth()->user()->perusahaan_id;
            }
            
            if (!$model->nomor_mutasi) {
                $model->nomor_mutasi = static::generateNomorMutasi();
            }
        });
    }

    public static function generateNomorMutasi(): string
    {
        $prefix = 'MUT';
        $date = now()->format('Ymd');
        $perusahaanId = auth()->user()->perusahaan_id ?? 1;
        
        $lastNumber = static::where('nomor_mutasi', 'like', "{$prefix}/{$date}/%")
            ->where('perusahaan_id', $perusahaanId)
            ->orderBy('nomor_mutasi', 'desc')
            ->first();
        
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber->nomor_mutasi, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return sprintf('%s/%s/%04d', $prefix, $date, $newSequence);
    }

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function projectAsal()
    {
        return $this->belongsTo(Project::class, 'project_asal_id');
    }

    public function projectTujuan()
    {
        return $this->belongsTo(Project::class, 'project_tujuan_id');
    }

    // Polymorphic relationship untuk asset
    public function asset()
    {
        if ($this->asset_type === 'data_aset') {
            return $this->belongsTo(DataAset::class, 'asset_id');
        } elseif ($this->asset_type === 'aset_kendaraan') {
            return $this->belongsTo(AsetKendaraan::class, 'asset_id');
        }
        
        return null;
    }

    public function dataAset()
    {
        return $this->belongsTo(DataAset::class, 'asset_id');
    }

    public function asetKendaraan()
    {
        return $this->belongsTo(AsetKendaraan::class, 'asset_id');
    }

    public function getAssetAttribute()
    {
        if ($this->asset_type === 'data_aset') {
            return DataAset::find($this->asset_id);
        } elseif ($this->asset_type === 'aset_kendaraan') {
            return AsetKendaraan::find($this->asset_id);
        }
        
        return null;
    }

    public function getAssetNameAttribute()
    {
        try {
            if ($this->asset_type === 'data_aset') {
                $asset = $this->dataAset ?? DataAset::find($this->asset_id);
                return $asset ? ($asset->nama_aset ?? 'Nama aset tidak tersedia') : 'Asset tidak ditemukan';
            } elseif ($this->asset_type === 'aset_kendaraan') {
                $asset = $this->asetKendaraan ?? AsetKendaraan::find($this->asset_id);
                if (!$asset) return 'Asset tidak ditemukan';
                
                $merk = $asset->merk ?? 'Unknown';
                $model = $asset->model ?? 'Unknown';
                $nopol = $asset->nomor_polisi ?? 'Unknown';
                return "{$merk} {$model} ({$nopol})";
            }
            
            return 'Unknown Asset';
        } catch (\Exception $e) {
            \Log::error('Error getting asset name: ' . $e->getMessage());
            return 'Error loading asset';
        }
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'disetujui' => '<span class="badge bg-success">Disetujui</span>',
            'ditolak' => '<span class="badge bg-danger">Ditolak</span>',
            'selesai' => '<span class="badge bg-primary">Selesai</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }
}