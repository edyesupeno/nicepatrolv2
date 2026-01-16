<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasHashId;

class Perusahaan extends Model
{
    use HasHashId;

    protected $fillable = [
        'nama',
        'kode',
        'alamat',
        'telepon',
        'email',
        'logo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = ['hash_id'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function kantors(): HasMany
    {
        return $this->hasMany(Kantor::class);
    }
    
    // Alias untuk backward compatibility
    public function lokasis(): HasMany
    {
        return $this->kantors();
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(Checkpoint::class);
    }

    public function patrolis(): HasMany
    {
        return $this->hasMany(Patroli::class);
    }
}
