<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class PenyerahanPerlengkapan extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $table = 'penyerahan_perlengkapans';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'kategori_perlengkapan_id',
        'karyawan_id',
        'created_by',
        'nomor_penyerahan',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope
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

    public function kategori()
    {
        return $this->belongsTo(KategoriPerlengkapan::class, 'kategori_perlengkapan_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PenyerahanPerlengkapanItem::class);
    }

    public function karyawans()
    {
        return $this->hasMany(PenyerahanPerlengkapanKaryawan::class);
    }

    public function karyawanList()
    {
        return $this->belongsToMany(Karyawan::class, 'penyerahan_perlengkapan_karyawans')
                    ->withPivot('status_penyerahan')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByKaryawan($query, $karyawanId)
    {
        return $query->where('karyawan_id', $karyawanId);
    }

    // Accessors
    public function getTotalItemsAttribute()
    {
        return $this->items()->count();
    }

    public function getTotalDiserahkanAttribute()
    {
        return $this->items()->sum('jumlah_diserahkan');
    }

    public function getTotalDikembalikanAttribute()
    {
        return $this->items()->sum('jumlah_dikembalikan');
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

    // Methods
    public function generateNomorPenyerahan()
    {
        $prefix = 'PP';
        $date = now()->format('Ymd');
        $lastNumber = static::whereDate('created_at', now()->toDateString())
                           ->where('nomor_penyerahan', 'like', $prefix . $date . '%')
                           ->count();
        
        return $prefix . $date . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
