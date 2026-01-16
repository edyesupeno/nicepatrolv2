<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashId;

class Sertifikasi extends Model
{
    use HasHashId;

    protected $fillable = [
        'karyawan_id',
        'nama_sertifikasi',
        'penerbit',
        'tanggal_terbit',
        'tanggal_expired',
        'nomor_sertifikat',
        'url_sertifikat',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_expired' => 'date',
    ];

    protected $appends = ['hash_id'];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
