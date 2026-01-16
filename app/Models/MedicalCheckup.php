<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashId;

class MedicalCheckup extends Model
{
    use HasHashId;

    protected $fillable = [
        'karyawan_id',
        // Informasi Dasar
        'jenis_checkup',
        'tanggal_checkup',
        'status_kesehatan',
        // Pengukuran Fisik
        'tinggi_badan',
        'berat_badan',
        'golongan_darah',
        'tekanan_darah',
        // Hasil Lab
        'gula_darah',
        'kolesterol',
        // Informasi Medis
        'rumah_sakit',
        'nama_dokter',
        'diagnosis',
        'catatan_tambahan',
    ];

    protected $casts = [
        'tanggal_checkup' => 'date',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
        'gula_darah' => 'decimal:2',
        'kolesterol' => 'decimal:2',
    ];

    protected $appends = ['hash_id'];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
