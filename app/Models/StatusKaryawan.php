<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusKaryawan extends Model
{
    protected $table = 'status_karyawans';
    
    protected $fillable = [
        'nama',
    ];
    
    public $timestamps = false;
}
