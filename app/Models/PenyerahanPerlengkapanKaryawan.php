<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;

class PenyerahanPerlengkapanKaryawan extends Model
{
    use HasFactory, HasHashId;

    protected $table = 'penyerahan_perlengkapan_karyawans';

    protected $fillable = [
        'penyerahan_perlengkapan_id',
        'karyawan_id',
        'status_penyerahan',
    ];

    protected $appends = ['hash_id'];

    // Multi-tenancy global scope - filter through penyerahan relationship
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->whereHas('penyerahan', function($query) {
                    $query->where('perusahaan_id', auth()->user()->perusahaan_id);
                });
            }
        });
    }

    // Relationships
    public function penyerahan()
    {
        return $this->belongsTo(PenyerahanPerlengkapan::class, 'penyerahan_perlengkapan_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }

    // Get items for this karyawan in this penyerahan
    public function items()
    {
        return $this->hasMany(PenyerahanPerlengkapanItem::class, 'penyerahan_perlengkapan_id', 'penyerahan_perlengkapan_id')
                    ->where('karyawan_id', $this->karyawan_id);
    }

    // Get items that have been distributed to this karyawan
    public function itemsDiserahkan()
    {
        return $this->items()->where('is_diserahkan', true);
    }

    // Get items that haven't been distributed to this karyawan
    public function itemsBelumDiserahkan()
    {
        return $this->items()->where('is_diserahkan', false);
    }

    // Update status based on items distribution
    public function updateStatus()
    {
        $totalItems = $this->items()->count();
        $diserahkanItems = $this->itemsDiserahkan()->count();

        if ($diserahkanItems == 0) {
            $this->status_penyerahan = 'belum_diserahkan';
        } elseif ($diserahkanItems == $totalItems) {
            $this->status_penyerahan = 'sudah_diserahkan';
        } else {
            $this->status_penyerahan = 'sebagian_diserahkan';
        }

        $this->save();
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match($this->status_penyerahan) {
            'sudah_diserahkan' => 'green',
            'sebagian_diserahkan' => 'yellow',
            'belum_diserahkan' => 'gray',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status_penyerahan) {
            'sudah_diserahkan' => 'Sudah Diserahkan',
            'sebagian_diserahkan' => 'Sebagian Diserahkan',
            'belum_diserahkan' => 'Belum Diserahkan',
            default => 'Unknown'
        };
    }
}