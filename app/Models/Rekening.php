<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Rekening extends Model
{
    use HasFactory, HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'project_id',
        'nama_rekening',
        'nomor_rekening',
        'nama_bank',
        'nama_pemilik',
        'jenis_rekening',
        'saldo_awal',
        'saldo_saat_ini',
        'mata_uang',
        'keterangan',
        'is_active',
        'is_primary',
        'warna_card'
    ];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_saat_ini' => 'decimal:2',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    /**
     * Global scope untuk multi-tenancy
     */
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }

    /**
     * Relationship dengan Perusahaan
     */
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class);
    }

    /**
     * Relationship dengan Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship dengan Transaksi Rekening
     */
    public function transaksiRekenings()
    {
        return $this->hasMany(TransaksiRekening::class);
    }

    /**
     * Get transaksi debit
     */
    public function transaksiDebit()
    {
        return $this->hasMany(TransaksiRekening::class)->where('jenis_transaksi', 'debit');
    }

    /**
     * Get transaksi kredit
     */
    public function transaksiKredit()
    {
        return $this->hasMany(TransaksiRekening::class)->where('jenis_transaksi', 'kredit');
    }

    /**
     * Scope untuk rekening aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk rekening primary
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope berdasarkan project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope berdasarkan jenis rekening
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_rekening', $jenis);
    }

    /**
     * Get formatted saldo awal
     */
    public function getFormattedSaldoAwalAttribute()
    {
        return 'Rp ' . number_format($this->saldo_awal, 0, ',', '.');
    }

    /**
     * Get formatted saldo saat ini
     */
    public function getFormattedSaldoSaatIniAttribute()
    {
        return 'Rp ' . number_format($this->saldo_saat_ini, 0, ',', '.');
    }

    /**
     * Get formatted nomor rekening (dengan mask)
     */
    public function getFormattedNomorRekeningAttribute()
    {
        $nomor = $this->nomor_rekening;
        if (strlen($nomor) > 8) {
            return substr($nomor, 0, 4) . str_repeat('*', strlen($nomor) - 8) . substr($nomor, -4);
        }
        return $nomor;
    }

    /**
     * Get jenis rekening label
     */
    public function getJenisRekeningLabelAttribute()
    {
        $labels = [
            'operasional' => 'Operasional',
            'payroll' => 'Payroll',
            'investasi' => 'Investasi',
            'emergency' => 'Emergency Fund',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$this->jenis_rekening] ?? 'Tidak Diketahui';
    }

    /**
     * Get available colors for cards
     */
    public static function getAvailableColors()
    {
        return [
            '#3B82C8' => 'Biru',
            '#10B981' => 'Hijau',
            '#F59E0B' => 'Kuning',
            '#EF4444' => 'Merah',
            '#8B5CF6' => 'Ungu',
            '#F97316' => 'Orange',
            '#06B6D4' => 'Cyan',
            '#84CC16' => 'Lime',
            '#EC4899' => 'Pink',
            '#6B7280' => 'Abu-abu'
        ];
    }

    /**
     * Get color name
     */
    public function getWarnaCardNameAttribute()
    {
        $colors = self::getAvailableColors();
        return $colors[$this->warna_card] ?? 'Biru';
    }

    /**
     * Set rekening sebagai primary (hanya satu per project)
     */
    public function setPrimary()
    {
        // Reset semua rekening di project ini menjadi non-primary
        self::where('project_id', $this->project_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set rekening ini sebagai primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Update saldo saat ini
     */
    public function updateSaldo($amount, $type = 'add')
    {
        if ($type === 'add') {
            $this->saldo_saat_ini += $amount;
        } else {
            $this->saldo_saat_ini -= $amount;
        }
        
        $this->save();
    }
}