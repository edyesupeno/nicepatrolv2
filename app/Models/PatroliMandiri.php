<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class PatroliMandiri extends Model
{
    use HasHashId;

    protected $table = 'patroli_mandiri';

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'area_patrol_id',
        'petugas_id',
        'nama_lokasi',
        'latitude',
        'longitude',
        'maps_url',
        'waktu_laporan',
        'status_lokasi',
        'jenis_kendala',
        'deskripsi_kendala',
        'catatan_petugas',
        'tindakan_yang_diambil',
        'foto_lokasi',
        'foto_kendala',
        'status_laporan',
        'reviewed_by',
        'reviewed_at',
        'review_catatan',
        'prioritas',
    ];

    protected $casts = [
        'waktu_laporan' => 'datetime',
        'reviewed_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $appends = ['hash_id'];

    // Global Scope - Multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    // Relationships
    public function perusahaan(): BelongsTo
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function areaPatrol(): BelongsTo
    {
        return $this->belongsTo(AreaPatrol::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Accessors
    public function getFotoLokasiUrlAttribute()
    {
        return $this->foto_lokasi ? asset('storage/' . $this->foto_lokasi) : null;
    }

    public function getFotoKendalaUrlAttribute()
    {
        return $this->foto_kendala ? asset('storage/' . $this->foto_kendala) : null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aman' => 'bg-green-100 text-green-800',
            'tidak_aman' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->status_lokasi] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPrioritasBadgeAttribute()
    {
        $badges = [
            'rendah' => 'bg-blue-100 text-blue-800',
            'sedang' => 'bg-yellow-100 text-yellow-800',
            'tinggi' => 'bg-orange-100 text-orange-800',
            'kritis' => 'bg-red-100 text-red-800',
        ];

        return $badges[$this->prioritas] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusLaporanBadgeAttribute()
    {
        $badges = [
            'draft' => 'bg-gray-100 text-gray-800',
            'submitted' => 'bg-blue-100 text-blue-800',
            'reviewed' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
        ];

        return $badges[$this->status_laporan] ?? 'bg-gray-100 text-gray-800';
    }

    // Scopes
    public function scopeAman($query)
    {
        return $query->where('status_lokasi', 'aman');
    }

    public function scopeTidakAman($query)
    {
        return $query->where('status_lokasi', 'tidak_aman');
    }

    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_laporan', $status);
    }

    // Methods
    public function setPrioritas()
    {
        if ($this->status_lokasi === 'aman') {
            $this->prioritas = 'rendah';
            return;
        }

        // Set prioritas berdasarkan jenis kendala
        $prioritasMap = [
            'kebakaran' => 'kritis',
            'sabotase' => 'kritis',
            'pencurian' => 'tinggi',
            'orang_mencurigakan' => 'tinggi',
            'aset_hilang' => 'sedang',
            'aset_rusak' => 'sedang',
            'kabel_terbuka' => 'sedang',
            'demo' => 'tinggi',
        ];

        $this->prioritas = $prioritasMap[$this->jenis_kendala] ?? 'rendah';
    }

    public function generateMapsUrl()
    {
        if ($this->latitude && $this->longitude) {
            $this->maps_url = "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }
    }
}