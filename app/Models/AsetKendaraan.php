<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class AsetKendaraan extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'aset_kendaraans';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'created_by',
        'kode_kendaraan',
        'jenis_kendaraan',
        'merk',
        'model',
        'tahun_pembuatan',
        'warna',
        'nomor_polisi',
        'nomor_rangka',
        'nomor_mesin',
        'tanggal_pembelian',
        'harga_pembelian',
        'nilai_penyusutan',
        'nomor_stnk',
        'tanggal_berlaku_stnk',
        'nomor_bpkb',
        'atas_nama_bpkb',
        'perusahaan_asuransi',
        'nomor_polis_asuransi',
        'tanggal_berlaku_asuransi',
        'nilai_pajak_tahunan',
        'jatuh_tempo_pajak',
        'kilometer_terakhir',
        'tanggal_service_terakhir',
        'tanggal_service_berikutnya',
        'driver_utama',
        'lokasi_parkir',
        'status_kendaraan',
        'foto_kendaraan',
        'file_stnk',
        'file_bpkb',
        'file_asuransi',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'tanggal_berlaku_stnk' => 'date',
        'tanggal_berlaku_asuransi' => 'date',
        'jatuh_tempo_pajak' => 'date',
        'tanggal_service_terakhir' => 'date',
        'tanggal_service_berikutnya' => 'date',
        'harga_pembelian' => 'decimal:2',
        'nilai_penyusutan' => 'decimal:2',
        'nilai_pajak_tahunan' => 'decimal:2',
        'kilometer_terakhir' => 'integer',
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

        // Auto-generate kode kendaraan saat creating
        static::creating(function ($model) {
            if (empty($model->kode_kendaraan)) {
                $model->kode_kendaraan = $model->generateKodeKendaraan();
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_kendaraan', $jenis);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_kendaraan', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kode_kendaraan', 'like', "%{$search}%")
              ->orWhere('nomor_polisi', 'like', "%{$search}%")
              ->orWhere('merk', 'like', "%{$search}%")
              ->orWhere('model', 'like', "%{$search}%")
              ->orWhere('driver_utama', 'like', "%{$search}%");
        });
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        $date = now()->addDays($days);
        return $query->where(function ($q) use ($date) {
            $q->where('tanggal_berlaku_stnk', '<=', $date)
              ->orWhere('tanggal_berlaku_asuransi', '<=', $date)
              ->orWhere('jatuh_tempo_pajak', '<=', $date);
        });
    }

    // Accessors
    public function getTahunAttribute()
    {
        return $this->tahun_pembuatan;
    }

    public function getNamaKendaraanAttribute()
    {
        return "{$this->merk} {$this->model} ({$this->tahun_pembuatan})";
    }
    public function getFormattedHargaPembelianAttribute()
    {
        return $this->harga_pembelian ? 'Rp ' . number_format($this->harga_pembelian, 0, ',', '.') : '-';
    }

    public function getFormattedNilaiPenyusutanAttribute()
    {
        return $this->nilai_penyusutan ? 'Rp ' . number_format($this->nilai_penyusutan, 0, ',', '.') : '-';
    }

    public function getNilaiSekarangAttribute()
    {
        return $this->harga_pembelian - $this->nilai_penyusutan;
    }

    public function getFormattedNilaiSekarangAttribute()
    {
        return 'Rp ' . number_format($this->nilai_sekarang, 0, ',', '.');
    }

    public function getFormattedNilaiPajakTahunanAttribute()
    {
        return $this->nilai_pajak_tahunan ? 'Rp ' . number_format($this->nilai_pajak_tahunan, 0, ',', '.') : '-';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status_kendaraan) {
            'aktif' => 'green',
            'maintenance' => 'yellow',
            'rusak' => 'red',
            'dijual' => 'blue',
            'hilang' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status_kendaraan) {
            'aktif' => 'Aktif',
            'maintenance' => 'Maintenance',
            'rusak' => 'Rusak',
            'dijual' => 'Dijual',
            'hilang' => 'Hilang',
            default => 'Unknown'
        };
    }

    public function getJenisLabelAttribute()
    {
        return match($this->jenis_kendaraan) {
            'mobil' => 'Mobil',
            'motor' => 'Motor',
            default => 'Unknown'
        };
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_kendaraan) {
            return asset('storage/' . $this->foto_kendaraan);
        }
        return null;
    }

    public function getFileStnkUrlAttribute()
    {
        if ($this->file_stnk) {
            return asset('storage/' . $this->file_stnk);
        }
        return null;
    }

    public function getFileBpkbUrlAttribute()
    {
        if ($this->file_bpkb) {
            return asset('storage/' . $this->file_bpkb);
        }
        return null;
    }

    public function getFileAsuransiUrlAttribute()
    {
        if ($this->file_asuransi) {
            return asset('storage/' . $this->file_asuransi);
        }
        return null;
    }

    public function getUmurKendaraanAttribute()
    {
        if ($this->tahun_pembuatan) {
            return now()->year - (int) $this->tahun_pembuatan;
        }
        return null;
    }

    // Status checks
    public function getStnkExpiredAttribute()
    {
        return $this->tanggal_berlaku_stnk && $this->tanggal_berlaku_stnk->isPast();
    }

    public function getAsuransiExpiredAttribute()
    {
        return $this->tanggal_berlaku_asuransi && $this->tanggal_berlaku_asuransi->isPast();
    }

    public function getPajakExpiredAttribute()
    {
        return $this->jatuh_tempo_pajak && $this->jatuh_tempo_pajak->isPast();
    }

    public function getStnkExpiringSoonAttribute()
    {
        return $this->tanggal_berlaku_stnk && 
               $this->tanggal_berlaku_stnk->isFuture() && 
               $this->tanggal_berlaku_stnk->diffInDays(now()) <= 30;
    }

    public function getAsuransiExpiringSoonAttribute()
    {
        return $this->tanggal_berlaku_asuransi && 
               $this->tanggal_berlaku_asuransi->isFuture() && 
               $this->tanggal_berlaku_asuransi->diffInDays(now()) <= 30;
    }

    public function getPajakExpiringSoonAttribute()
    {
        return $this->jatuh_tempo_pajak && 
               $this->jatuh_tempo_pajak->isFuture() && 
               $this->jatuh_tempo_pajak->diffInDays(now()) <= 30;
    }

    // Methods
    public function generateKodeKendaraan()
    {
        $perusahaanId = auth()->user()->perusahaan_id ?? $this->perusahaan_id;
        $projectId = $this->project_id;
        
        // Format: KND-{PROJECT_ID}-{YEAR}-{SEQUENCE}
        $year = now()->year;
        $prefix = "KND-{$projectId}-{$year}-";
        
        // Get last sequence number
        $lastKendaraan = static::withoutGlobalScope('perusahaan')
            ->where('perusahaan_id', $perusahaanId)
            ->where('project_id', $projectId)
            ->where('kode_kendaraan', 'like', $prefix . '%')
            ->orderBy('kode_kendaraan', 'desc')
            ->first();
        
        if ($lastKendaraan) {
            $lastSequence = (int) substr($lastKendaraan->kode_kendaraan, strlen($prefix));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }
        
        return $prefix . $sequence;
    }

    // Static methods untuk dropdown
    public static function getJenisOptions()
    {
        return [
            'mobil' => 'Mobil',
            'motor' => 'Motor'
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'aktif' => 'Aktif',
            'maintenance' => 'Maintenance',
            'rusak' => 'Rusak',
            'dijual' => 'Dijual',
            'hilang' => 'Hilang'
        ];
    }

    public static function getMerkList()
    {
        return static::select('merk')
            ->distinct()
            ->orderBy('merk')
            ->pluck('merk')
            ->toArray();
    }
}