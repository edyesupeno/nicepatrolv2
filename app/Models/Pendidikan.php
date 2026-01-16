<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasHashId;

class Pendidikan extends Model
{
    use HasHashId;

    protected $fillable = [
        'karyawan_id',
        'jenjang_pendidikan',
        'nama_institusi',
        'jurusan',
        'ipk',
        'tahun_mulai',
        'tahun_selesai',
    ];

    protected $appends = ['hash_id'];

    // Relationships
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
