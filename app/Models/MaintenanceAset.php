<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class MaintenanceAset extends Model
{
    use HasFactory, SoftDeletes, HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'created_by',
        'nomor_maintenance',
        'asset_type',
        'asset_id',
        'jenis_maintenance',
        'tanggal_maintenance',
        'waktu_mulai',
        'waktu_selesai',
        'estimasi_durasi',
        'deskripsi_pekerjaan',
        'catatan_sebelum',
        'catatan_sesudah',
        'teknisi_internal',
        'vendor_eksternal',
        'kontak_vendor',
        'biaya_sparepart',
        'biaya_jasa',
        'biaya_lainnya',
        'total_biaya',
        'status',
        'prioritas',
        'hasil_maintenance',
        'masalah_ditemukan',
        'tindakan_dilakukan',
        'rekomendasi',
        'reminder_aktif',
        'reminder_hari',
        'reminder_terakhir',
        'foto_sebelum',
        'foto_sesudah',
        'dokumen_pendukung',
        'invoice_pembayaran',
        'tanggal_maintenance_berikutnya',
        'interval_maintenance',
    ];

    protected $casts = [
        'tanggal_maintenance' => 'date',
        'tanggal_maintenance_berikutnya' => 'date',
        'reminder_terakhir' => 'datetime',
        'biaya_sparepart' => 'decimal:2',
        'biaya_jasa' => 'decimal:2',
        'biaya_lainnya' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'reminder_aktif' => 'boolean',
        'estimasi_durasi' => 'integer',
        'reminder_hari' => 'integer',
        'interval_maintenance' => 'integer',
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

        // Auto-generate nomor maintenance saat creating
        static::creating(function ($model) {
            if (empty($model->nomor_maintenance)) {
                $model->nomor_maintenance = $model->generateNomorMaintenance();
            }
            
            // Auto-calculate total biaya
            $model->total_biaya = $model->biaya_sparepart + $model->biaya_jasa + $model->biaya_lainnya;
        });

        static::updating(function ($model) {
            // Auto-calculate total biaya
            $model->total_biaya = $model->biaya_sparepart + $model->biaya_jasa + $model->biaya_lainnya;
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

    public function dataAset()
    {
        return $this->belongsTo(DataAset::class, 'asset_id');
    }

    public function asetKendaraan()
    {
        return $this->belongsTo(AsetKendaraan::class, 'asset_id');
    }

    // Accessors
    public function getAssetAttribute()
    {
        if ($this->asset_type === 'data_aset') {
            return $this->dataAset ?? DataAset::find($this->asset_id);
        } elseif ($this->asset_type === 'aset_kendaraan') {
            return $this->asetKendaraan ?? AsetKendaraan::find($this->asset_id);
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
            'scheduled' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Terjadwal</span>',
            'in_progress' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Sedang Dikerjakan</span>',
            'completed' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Selesai</span>',
            'cancelled' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Dibatalkan</span>',
        ];

        return $badges[$this->status] ?? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
    }

    public function getPrioritasBadgeAttribute()
    {
        $badges = [
            'low' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Rendah</span>',
            'medium' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Sedang</span>',
            'high' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Tinggi</span>',
            'urgent' => '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Mendesak</span>',
        ];

        return $badges[$this->prioritas] ?? '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
    }

    public function getFormattedTotalBiayaAttribute()
    {
        return 'Rp ' . number_format($this->total_biaya, 0, ',', '.');
    }

    public function getIsOverdueAttribute()
    {
        return $this->tanggal_maintenance->isPast() && in_array($this->status, ['scheduled']);
    }

    public function getIsReminderDueAttribute()
    {
        if (!$this->reminder_aktif || $this->status !== 'scheduled') {
            return false;
        }

        $reminderDate = $this->tanggal_maintenance->subDays($this->reminder_hari);
        return now()->gte($reminderDate);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('tanggal_maintenance', '<', now()->toDateString());
    }

    public function scopeUpcoming($query, $days = 7)
    {
        return $query->where('status', 'scheduled')
                    ->whereBetween('tanggal_maintenance', [
                        now()->toDateString(),
                        now()->addDays($days)->toDateString()
                    ]);
    }

    public function scopeByAssetType($query, $type)
    {
        return $query->where('asset_type', $type);
    }

    public function scopeByJenisMaintenance($query, $jenis)
    {
        return $query->where('jenis_maintenance', $jenis);
    }

    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('nomor_maintenance', 'like', "%{$search}%")
              ->orWhere('deskripsi_pekerjaan', 'like', "%{$search}%")
              ->orWhere('teknisi_internal', 'like', "%{$search}%")
              ->orWhere('vendor_eksternal', 'like', "%{$search}%");
        });
    }

    // Methods
    public function generateNomorMaintenance()
    {
        $perusahaanId = auth()->user()->perusahaan_id ?? $this->perusahaan_id;
        $projectId = $this->project_id;
        
        // Format: MNT-{PROJECT_ID}-{YEAR}-{SEQUENCE}
        $year = now()->year;
        $prefix = "MNT-{$projectId}-{$year}-";
        
        // Get last sequence number
        $lastMaintenance = static::withoutGlobalScope('perusahaan')
            ->where('perusahaan_id', $perusahaanId)
            ->where('project_id', $projectId)
            ->where('nomor_maintenance', 'like', $prefix . '%')
            ->orderBy('nomor_maintenance', 'desc')
            ->first();
        
        if ($lastMaintenance) {
            $lastSequence = (int) substr($lastMaintenance->nomor_maintenance, strlen($prefix));
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }
        
        return $prefix . $sequence;
    }

    public function markAsCompleted($hasil = 'berhasil', $catatan = null)
    {
        $this->update([
            'status' => 'completed',
            'hasil_maintenance' => $hasil,
            'catatan_sesudah' => $catatan,
            'waktu_selesai' => now()->format('H:i'),
        ]);

        // Generate next maintenance if interval is set
        if ($this->interval_maintenance) {
            $this->generateNextMaintenance();
        }
    }

    public function generateNextMaintenance()
    {
        if (!$this->interval_maintenance) {
            return null;
        }

        $nextDate = $this->tanggal_maintenance->addDays($this->interval_maintenance);
        
        $nextMaintenance = static::create([
            'perusahaan_id' => $this->perusahaan_id,
            'project_id' => $this->project_id,
            'created_by' => $this->created_by,
            'asset_type' => $this->asset_type,
            'asset_id' => $this->asset_id,
            'jenis_maintenance' => $this->jenis_maintenance,
            'tanggal_maintenance' => $nextDate,
            'deskripsi_pekerjaan' => $this->deskripsi_pekerjaan,
            'prioritas' => $this->prioritas,
            'interval_maintenance' => $this->interval_maintenance,
            'reminder_aktif' => $this->reminder_aktif,
            'reminder_hari' => $this->reminder_hari,
        ]);

        // Update current maintenance with next maintenance date
        $this->update([
            'tanggal_maintenance_berikutnya' => $nextDate
        ]);

        return $nextMaintenance;
    }

    // Static methods untuk dropdown
    public static function getJenisMaintenanceOptions()
    {
        return [
            'preventive' => 'Preventive (Pencegahan)',
            'corrective' => 'Corrective (Perbaikan)',
            'predictive' => 'Predictive (Prediktif)',
        ];
    }

    public static function getStatusOptions()
    {
        return [
            'scheduled' => 'Terjadwal',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
    }

    public static function getPrioritasOptions()
    {
        return [
            'low' => 'Rendah',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
        ];
    }

    public static function getHasilMaintenanceOptions()
    {
        return [
            'berhasil' => 'Berhasil',
            'sebagian' => 'Sebagian Berhasil',
            'gagal' => 'Gagal',
        ];
    }
}