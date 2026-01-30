<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class Karyawan extends Model
{
    use HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'user_id',
        'project_id',
        // Data Dasar
        'nik_karyawan',
        'status_karyawan',
        'jabatan_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'is_active',
        'gaji_pokok',
        // Data Pribadi
        'nama_lengkap',
        'nik_ktp',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'status_perkawinan',
        'jumlah_tanggungan',
        'telepon',
        'alamat',
        'kota',
        'provinsi',
        'foto',
        // Rekening Bank
        'nama_bank',
        'nomor_rekening',
        'nama_pemilik_rekening',
        'cabang_bank',
        // BPJS Ketenagakerjaan
        'bpjs_jkm_nomor', 'bpjs_jkm_npp', 'bpjs_jkm_tanggal_terdaftar', 'bpjs_jkm_status', 'bpjs_jkm_catatan',
        'bpjs_jkk_nomor', 'bpjs_jkk_npp', 'bpjs_jkk_tanggal_terdaftar', 'bpjs_jkk_status', 'bpjs_jkk_catatan',
        'bpjs_jp_nomor', 'bpjs_jp_npp', 'bpjs_jp_tanggal_terdaftar', 'bpjs_jp_status', 'bpjs_jp_catatan',
        'bpjs_jht_nomor', 'bpjs_jht_npp', 'bpjs_jht_tanggal_terdaftar', 'bpjs_jht_status', 'bpjs_jht_catatan',
        // BPJS Kesehatan
        'bpjs_kesehatan_nomor', 'bpjs_kesehatan_tanggal_terdaftar', 'bpjs_kesehatan_status', 'bpjs_kesehatan_catatan',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
        'tanggal_lahir' => 'date',
        'is_active' => 'boolean',
        'bpjs_jkm_tanggal_terdaftar' => 'date',
        'bpjs_jkk_tanggal_terdaftar' => 'date',
        'bpjs_jp_tanggal_terdaftar' => 'date',
        'bpjs_jht_tanggal_terdaftar' => 'date',
        'bpjs_kesehatan_tanggal_terdaftar' => 'date',
    ];

    protected $appends = ['hash_id'];

    // Global scope untuk multi-tenancy
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }

    // Area kerja karyawan (many-to-many)
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'karyawan_areas')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Area utama karyawan
    public function primaryArea()
    {
        return $this->belongsToMany(Area::class, 'karyawan_areas')
                    ->wherePivot('is_primary', true)
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Get area utama (single)
    public function getPrimaryAreaAttribute()
    {
        return $this->primaryArea()->first();
    }

    public function pengalamanKerjas()
    {
        return $this->hasMany(PengalamanKerja::class);
    }

    public function pendidikans()
    {
        return $this->hasMany(Pendidikan::class);
    }

    public function sertifikasis()
    {
        return $this->hasMany(Sertifikasi::class);
    }

    public function medicalCheckups()
    {
        return $this->hasMany(MedicalCheckup::class);
    }

    public function items()
    {
        return $this->hasMany(PenyerahanPerlengkapanItem::class);
    }

    public function penyerahanKaryawans()
    {
        return $this->hasMany(PenyerahanPerlengkapanKaryawan::class);
    }

    public function jadwalShifts()
    {
        return $this->hasMany(JadwalShift::class);
    }

    /**
     * Get status PTKP lengkap (TK/0, K/1, dll)
     */
    public function getPtkpStatusAttribute(): string
    {
        $status = $this->status_perkawinan ?? 'TK';
        $tanggungan = min($this->jumlah_tanggungan ?? 0, 3); // Max 3 tanggungan
        return "{$status}/{$tanggungan}";
    }

    /**
     * Get PTKP value from PayrollSetting
     */
    public function getPtkpValueAttribute(): int
    {
        $payrollSetting = \App\Models\PayrollSetting::first();
        if (!$payrollSetting) {
            return 0;
        }

        $status = $this->status_perkawinan ?? 'TK';
        $tanggungan = min($this->jumlah_tanggungan ?? 0, 3);
        
        $ptkpKey = 'ptkp_' . strtolower($status) . $tanggungan;
        return $payrollSetting->$ptkpKey ?? 0;
    }
}
