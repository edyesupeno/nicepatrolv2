<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class PeminjamanAset extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'peminjaman_asets';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'aset_type',
        'data_aset_id',
        'aset_kendaraan_id',
        'peminjam_karyawan_id',
        'peminjam_user_id',
        'created_by',
        'approved_by',
        'returned_by',
        'kode_peminjaman',
        'tanggal_peminjaman',
        'tanggal_rencana_kembali',
        'tanggal_kembali_aktual',
        'jumlah_dipinjam',
        'status_peminjaman',
        'keperluan',
        'catatan_peminjaman',
        'catatan_pengembalian',
        'kondisi_saat_dipinjam',
        'kondisi_saat_dikembalikan',
        'file_bukti_peminjaman',
        'file_bukti_pengembalian',
        'approved_at',
        'borrowed_at',
        'returned_at',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_rencana_kembali' => 'date',
        'tanggal_kembali_aktual' => 'date',
        'jumlah_dipinjam' => 'integer',
        'approved_at' => 'datetime',
        'borrowed_at' => 'datetime',
        'returned_at' => 'datetime',
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

        // Auto-generate kode peminjaman saat creating
        static::creating(function ($model) {
            if (empty($model->kode_peminjaman)) {
                $model->kode_peminjaman = $model->generateKodePeminjaman();
            }
            
            // Auto-assign perusahaan_id dan created_by
            if (auth()->check()) {
                $model->perusahaan_id = auth()->user()->perusahaan_id;
                $model->created_by = auth()->id();
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

    public function dataAset()
    {
        return $this->belongsTo(DataAset::class);
    }

    public function asetKendaraan()
    {
        return $this->belongsTo(AsetKendaraan::class);
    }

    public function peminjamKaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'peminjam_karyawan_id');
    }

    public function peminjamUser()
    {
        return $this->belongsTo(User::class, 'peminjam_user_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_peminjaman', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByAset($query, $asetId)
    {
        return $query->where('data_aset_id', $asetId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kode_peminjaman', 'like', "%{$search}%")
              ->orWhere('keperluan', 'like', "%{$search}%")
              ->orWhereHas('dataAset', function ($subQ) use ($search) {
                  $subQ->where('nama_aset', 'like', "%{$search}%")
                       ->orWhere('kode_aset', 'like', "%{$search}%");
              })
              ->orWhereHas('asetKendaraan', function ($subQ) use ($search) {
                  $subQ->where('merk', 'like', "%{$search}%")
                       ->orWhere('model', 'like', "%{$search}%")
                       ->orWhere('kode_kendaraan', 'like', "%{$search}%")
                       ->orWhere('nomor_polisi', 'like', "%{$search}%");
              })
              ->orWhereHas('peminjamKaryawan', function ($subQ) use ($search) {
                  $subQ->where('nama_lengkap', 'like', "%{$search}%");
              });
        });
    }

    public function scopeTerlambat($query)
    {
        return $query->where('status_peminjaman', 'dipinjam')
                    ->where('tanggal_rencana_kembali', '<', now()->toDateString());
    }

    public function scopeAkanJatuhTempo($query, $days = 3)
    {
        $date = now()->addDays($days)->toDateString();
        return $query->where('status_peminjaman', 'dipinjam')
                    ->where('tanggal_rencana_kembali', '<=', $date)
                    ->where('tanggal_rencana_kembali', '>=', now()->toDateString());
    }

    // Accessors
    public function getAsetInfoAttribute()
    {
        if ($this->aset_type === 'aset_kendaraan' && $this->asetKendaraan) {
            return $this->asetKendaraan;
        } elseif ($this->aset_type === 'data_aset' && $this->dataAset) {
            return $this->dataAset;
        }
        return null;
    }

    public function getAsetNamaAttribute()
    {
        if ($this->aset_type === 'aset_kendaraan' && $this->aset_kendaraan_id) {
            if ($this->relationLoaded('asetKendaraan') && $this->asetKendaraan) {
                return "{$this->asetKendaraan->merk} {$this->asetKendaraan->model} ({$this->asetKendaraan->tahun_pembuatan})";
            }
            return 'Data kendaraan tidak tersedia';
        } elseif ($this->aset_type === 'data_aset' && $this->data_aset_id) {
            if ($this->relationLoaded('dataAset') && $this->dataAset) {
                return $this->dataAset->nama_aset;
            }
            return 'Data aset tidak tersedia';
        }
        return 'Data tidak tersedia';
    }

    public function getAsetKodeAttribute()
    {
        if ($this->aset_type === 'aset_kendaraan' && $this->aset_kendaraan_id) {
            if ($this->relationLoaded('asetKendaraan') && $this->asetKendaraan) {
                return $this->asetKendaraan->kode_kendaraan;
            }
            return 'Data tidak tersedia';
        } elseif ($this->aset_type === 'data_aset' && $this->data_aset_id) {
            if ($this->relationLoaded('dataAset') && $this->dataAset) {
                return $this->dataAset->kode_aset;
            }
            return 'Data tidak tersedia';
        }
        return 'Data tidak tersedia';
    }

    public function getAsetKategoriAttribute()
    {
        if ($this->aset_type === 'aset_kendaraan' && $this->aset_kendaraan_id) {
            if ($this->relationLoaded('asetKendaraan') && $this->asetKendaraan) {
                return 'Kendaraan - ' . ucfirst($this->asetKendaraan->jenis_kendaraan);
            }
            return 'Data tidak tersedia';
        } elseif ($this->aset_type === 'data_aset' && $this->data_aset_id) {
            if ($this->relationLoaded('dataAset') && $this->dataAset) {
                return $this->dataAset->kategori;
            }
            return 'Data tidak tersedia';
        }
        return 'Data tidak tersedia';
    }

    public function getAsetTypeLabelAttribute()
    {
        return match($this->aset_type) {
            'data_aset' => 'Aset',
            'aset_kendaraan' => 'Kendaraan',
            default => 'Unknown'
        };
    }

    public function getPeminjamNamaAttribute()
    {
        if ($this->peminjam_karyawan_id && $this->relationLoaded('peminjamKaryawan') && $this->peminjamKaryawan) {
            return $this->peminjamKaryawan->nama_lengkap;
        }
        return 'Data tidak tersedia';
    }

    public function getPeminjamTipeAttribute()
    {
        return 'karyawan';
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status_peminjaman) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'dipinjam' => 'Sedang Dipinjam',
            'dikembalikan' => 'Sudah Dikembalikan',
            'terlambat' => 'Terlambat',
            'hilang' => 'Hilang',
            'rusak' => 'Rusak',
            'ditolak' => 'Ditolak',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status_peminjaman) {
            'pending' => 'yellow',
            'approved' => 'blue',
            'dipinjam' => 'green',
            'dikembalikan' => 'gray',
            'terlambat' => 'red',
            'hilang' => 'red',
            'rusak' => 'orange',
            'ditolak' => 'red',
            default => 'gray'
        };
    }

    public function getKondisiSaatDipinjamLabelAttribute()
    {
        return match($this->kondisi_saat_dipinjam) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            default => 'Unknown'
        };
    }

    public function getKondisiSaatDikembalikanLabelAttribute()
    {
        return match($this->kondisi_saat_dikembalikan) {
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
            default => 'Belum Dikembalikan'
        };
    }

    public function getDurasiPeminjamanAttribute()
    {
        if ($this->tanggal_kembali_aktual) {
            return $this->tanggal_peminjaman->diffInDays($this->tanggal_kembali_aktual);
        } elseif ($this->status_peminjaman === 'dipinjam') {
            return $this->tanggal_peminjaman->diffInDays(now());
        }
        return null;
    }

    public function getKeterlambatanAttribute()
    {
        if ($this->status_peminjaman === 'dipinjam' && $this->tanggal_rencana_kembali->isPast()) {
            return (int) $this->tanggal_rencana_kembali->diffInDays(now());
        } elseif ($this->tanggal_kembali_aktual && $this->tanggal_kembali_aktual->gt($this->tanggal_rencana_kembali)) {
            return (int) $this->tanggal_rencana_kembali->diffInDays($this->tanggal_kembali_aktual);
        }
        return 0;
    }

    public function getIsTerlambatAttribute()
    {
        return $this->keterlambatan > 0;
    }

    public function getFileBuktiPeminjamanUrlAttribute()
    {
        if ($this->file_bukti_peminjaman) {
            return asset('storage/' . $this->file_bukti_peminjaman);
        }
        return null;
    }

    public function getFileBuktiPengembalianUrlAttribute()
    {
        if ($this->file_bukti_pengembalian) {
            return asset('storage/' . $this->file_bukti_pengembalian);
        }
        return null;
    }

    // Methods
    public function generateKodePeminjaman()
    {
        $perusahaanId = auth()->user()->perusahaan_id ?? $this->perusahaan_id;
        $projectId = $this->project_id;
        
        // Format: PJM-{PROJECT_ID}-{YEAR}-{SEQUENCE}
        $year = now()->year;
        $prefix = "PJM-{$projectId}-{$year}-";
        
        // Get last sequence number
        $lastPeminjaman = static::withoutGlobalScope('perusahaan')
            ->where('perusahaan_id', $perusahaanId)
            ->where('project_id', $projectId)
            ->where('kode_peminjaman', 'like', $prefix . '%')
            ->orderBy('kode_peminjaman', 'desc')
            ->first();
        
        if ($lastPeminjaman) {
            $lastSequence = (int) substr($lastPeminjaman->kode_peminjaman, strlen($prefix));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }
        
        return $prefix . $sequence;
    }

    public function approve($approvedBy = null)
    {
        $this->update([
            'status_peminjaman' => 'approved',
            'approved_by' => $approvedBy ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function borrow($borrowedBy = null)
    {
        $this->update([
            'status_peminjaman' => 'dipinjam',
            'borrowed_at' => now(),
        ]);
    }

    public function returnAsset($returnedBy = null, $kondisi = 'baik', $catatan = null)
    {
        $this->update([
            'status_peminjaman' => 'dikembalikan',
            'returned_by' => $returnedBy ?? auth()->id(),
            'returned_at' => now(),
            'tanggal_kembali_aktual' => now()->toDateString(),
            'kondisi_saat_dikembalikan' => $kondisi,
            'catatan_pengembalian' => $catatan,
        ]);
    }

    public function reject($rejectedBy = null, $catatan = null)
    {
        $this->update([
            'status_peminjaman' => 'ditolak',
            'approved_by' => $rejectedBy ?? auth()->id(),
            'approved_at' => now(),
            'catatan_peminjaman' => $catatan,
        ]);
    }

    // Static methods untuk dropdown
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'dipinjam' => 'Sedang Dipinjam',
            'dikembalikan' => 'Sudah Dikembalikan',
            'terlambat' => 'Terlambat',
            'hilang' => 'Hilang',
            'rusak' => 'Rusak',
            'ditolak' => 'Ditolak',
        ];
    }

    public static function getKondisiOptions()
    {
        return [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
        ];
    }

    public static function getKondisiPengembalianOptions()
    {
        return [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];
    }

    public static function getAsetTypeOptions()
    {
        return [
            'data_aset' => 'Aset',
            'aset_kendaraan' => 'Kendaraan',
        ];
    }
}