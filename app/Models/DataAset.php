<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class DataAset extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'data_asets';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'created_by',
        'kode_aset',
        'nama_aset',
        'kategori',
        'tanggal_beli',
        'harga_beli',
        'nilai_penyusutan',
        'pic_penanggung_jawab',
        'foto_aset',
        'catatan_tambahan',
        'status',
    ];

    protected $casts = [
        'tanggal_beli' => 'date',
        'harga_beli' => 'decimal:2',
        'nilai_penyusutan' => 'decimal:2',
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

        // Auto-generate kode aset saat creating
        static::creating(function ($model) {
            if (empty($model->kode_aset)) {
                $model->kode_aset = $model->generateKodeAset();
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

    public function peminjamanAsets()
    {
        return $this->hasMany(PeminjamanAset::class);
    }

    public function peminjamanAktif()
    {
        return $this->hasMany(PeminjamanAset::class)->whereIn('status_peminjaman', ['approved', 'dipinjam']);
    }

    public function peminjamanTerakhir()
    {
        return $this->hasOne(PeminjamanAset::class)->latest();
    }

    // Scopes
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('kode_aset', 'like', "%{$search}%")
              ->orWhere('nama_aset', 'like', "%{$search}%")
              ->orWhere('kategori', 'like', "%{$search}%")
              ->orWhere('pic_penanggung_jawab', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedHargaBeliAttribute()
    {
        return $this->harga_beli ? 'Rp ' . number_format($this->harga_beli, 0, ',', '.') : '-';
    }

    public function getFormattedNilaiPenyusutanAttribute()
    {
        return $this->nilai_penyusutan ? 'Rp ' . number_format($this->nilai_penyusutan, 0, ',', '.') : '-';
    }

    public function getNilaiSekarangAttribute()
    {
        return $this->harga_beli - $this->nilai_penyusutan;
    }

    public function getFormattedNilaiSekarangAttribute()
    {
        return 'Rp ' . number_format($this->nilai_sekarang, 0, ',', '.');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'ada' => 'green',
            'rusak' => 'red',
            'dijual' => 'blue',
            'dihapus' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'ada' => 'Ada',
            'rusak' => 'Rusak',
            'dijual' => 'Dijual',
            'dihapus' => 'Dihapus',
            default => 'Unknown'
        };
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto_aset) {
            return asset('storage/' . $this->foto_aset);
        }
        return null;
    }

    public function getUmurAsetAttribute()
    {
        if ($this->tanggal_beli) {
            return $this->tanggal_beli->diffInYears(now());
        }
        return null;
    }

    // Methods
    public function generateKodeAset()
    {
        $perusahaanId = auth()->user()->perusahaan_id ?? $this->perusahaan_id;
        $projectId = $this->project_id;
        
        // Format: AST-{PROJECT_ID}-{YEAR}-{SEQUENCE}
        $year = now()->year;
        $prefix = "AST-{$projectId}-{$year}-";
        
        // Get last sequence number
        $lastAset = static::withoutGlobalScope('perusahaan')
            ->where('perusahaan_id', $perusahaanId)
            ->where('project_id', $projectId)
            ->where('kode_aset', 'like', $prefix . '%')
            ->orderBy('kode_aset', 'desc')
            ->first();
        
        if ($lastAset) {
            $lastSequence = (int) substr($lastAset->kode_aset, strlen($prefix));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }
        
        return $prefix . $sequence;
    }

    // Static methods untuk dropdown
    public static function getKategoriList()
    {
        return static::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori')
            ->toArray();
    }

    public static function getStatusOptions()
    {
        return [
            'ada' => 'Ada',
            'rusak' => 'Rusak',
            'dijual' => 'Dijual',
            'dihapus' => 'Dihapus'
        ];
    }
}