<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashId;

class PengalamanKerja extends Model
{
    use HasHashId;

    protected $fillable = [
        'karyawan_id',
        'nama_perusahaan',
        'jabatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'masih_bekerja',
        'deskripsi_pekerjaan',
        'pencapaian',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'masih_bekerja' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
