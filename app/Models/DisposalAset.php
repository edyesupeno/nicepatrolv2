<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DisposalAset extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_disposal',
        'perusahaan_id',
        'project_id',
        'asset_type',
        'asset_id',
        'asset_code',
        'asset_name',
        'tanggal_disposal',
        'jenis_disposal',
        'alasan_disposal',
        'nilai_buku',
        'nilai_disposal',
        'pembeli',
        'catatan',
        'status',
        'diajukan_oleh',
        'disetujui_oleh',
        'tanggal_disetujui',
        'catatan_approval',
        'dokumen_pendukung',
        'foto_kondisi',
    ];

    protected $casts = [
        'tanggal_disposal' => 'date',
        'tanggal_disetujui' => 'datetime',
        'nilai_buku' => 'decimal:2',
        'nilai_disposal' => 'decimal:2',
        'dokumen_pendukung' => 'array',
    ];

    protected $appends = ['hash_id'];

    // Global scope for multi-tenancy
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id && !auth()->user()->isSuperAdmin()) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

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

    // Relationships
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    // Get the actual asset being disposed
    public function getAssetAttribute()
    {
        if ($this->asset_type === 'data_aset') {
            return DataAset::withoutGlobalScope('perusahaan')->find($this->asset_id);
        } elseif ($this->asset_type === 'aset_kendaraan') {
            return AsetKendaraan::withoutGlobalScope('perusahaan')->find($this->asset_id);
        }
        return null;
    }

    // Formatted attributes
    public function getFormattedNilaiBukuAttribute()
    {
        return $this->nilai_buku ? 'Rp ' . number_format($this->nilai_buku, 0, ',', '.') : '-';
    }

    public function getFormattedNilaiDisposalAttribute()
    {
        return $this->nilai_disposal ? 'Rp ' . number_format($this->nilai_disposal, 0, ',', '.') : '-';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-blue-100 text-blue-800',
            'rejected' => 'bg-red-100 text-red-800',
            'completed' => 'bg-green-100 text-green-800',
        ];

        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getJenisDisposalBadgeAttribute()
    {
        $badges = [
            'dijual' => 'bg-green-100 text-green-800',
            'rusak' => 'bg-red-100 text-red-800',
            'hilang' => 'bg-purple-100 text-purple-800',
            'tidak_layak' => 'bg-orange-100 text-orange-800',
            'expired' => 'bg-gray-100 text-gray-800',
        ];

        return $badges[$this->jenis_disposal] ?? 'bg-gray-100 text-gray-800';
    }

    // Generate disposal number
    public static function generateNomorDisposal($perusahaanId)
    {
        $today = now()->format('Ymd');
        $count = self::where('perusahaan_id', $perusahaanId)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        return 'DSP/' . $today . '/' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByAssetType($query, $assetType)
    {
        return $query->where('asset_type', $assetType);
    }

    public function scopeByJenisDisposal($query, $jenisDisposal)
    {
        return $query->where('jenis_disposal', $jenisDisposal);
    }
}