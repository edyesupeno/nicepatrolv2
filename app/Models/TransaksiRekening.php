<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasHashId;
use Carbon\Carbon;

class TransaksiRekening extends Model
{
    use HasFactory, HasHashId;

    protected $fillable = [
        'perusahaan_id',
        'rekening_id',
        'nomor_transaksi',
        'tanggal_transaksi',
        'jenis_transaksi',
        'jumlah',
        'saldo_sebelum',
        'saldo_sesudah',
        'kategori_transaksi',
        'keterangan',
        'referensi',
        'user_id',
        'metadata',
        'is_verified',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
        'saldo_sebelum' => 'decimal:2',
        'saldo_sesudah' => 'decimal:2',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime'
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

        // Auto generate nomor transaksi
        static::creating(function ($transaksi) {
            if (empty($transaksi->nomor_transaksi)) {
                $transaksi->nomor_transaksi = self::generateNomorTransaksi();
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
     * Relationship dengan Rekening
     */
    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }

    /**
     * Relationship dengan User (yang melakukan transaksi)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship dengan User (yang memverifikasi)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope untuk transaksi debit
     */
    public function scopeDebit($query)
    {
        return $query->where('jenis_transaksi', 'debit');
    }

    /**
     * Scope untuk transaksi kredit
     */
    public function scopeKredit($query)
    {
        return $query->where('jenis_transaksi', 'kredit');
    }

    /**
     * Scope untuk transaksi terverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope untuk transaksi belum terverifikasi
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope berdasarkan rekening
     */
    public function scopeByRekening($query, $rekeningId)
    {
        return $query->where('rekening_id', $rekeningId);
    }

    /**
     * Scope berdasarkan periode
     */
    public function scopeByPeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_transaksi', $kategori);
    }

    /**
     * Get formatted jumlah
     */
    public function getFormattedJumlahAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    /**
     * Get formatted saldo sebelum
     */
    public function getFormattedSaldoSebelumAttribute()
    {
        return 'Rp ' . number_format($this->saldo_sebelum, 0, ',', '.');
    }

    /**
     * Get formatted saldo sesudah
     */
    public function getFormattedSaldoSesudahAttribute()
    {
        return 'Rp ' . number_format($this->saldo_sesudah, 0, ',', '.');
    }

    /**
     * Get kategori transaksi label
     */
    public function getKategoriTransaksiLabelAttribute()
    {
        $labels = [
            'transfer_masuk' => 'Transfer Masuk',
            'transfer_keluar' => 'Transfer Keluar',
            'pembayaran_vendor' => 'Pembayaran Vendor',
            'pembayaran_gaji' => 'Pembayaran Gaji',
            'reimbursement' => 'Reimbursement Karyawan',
            'penerimaan_client' => 'Penerimaan dari Client',
            'biaya_operasional' => 'Biaya Operasional',
            'investasi' => 'Investasi',
            'pinjaman' => 'Pinjaman',
            'bunga_bank' => 'Bunga Bank',
            'biaya_admin' => 'Biaya Administrasi',
            'lainnya' => 'Lainnya'
        ];

        return $labels[$this->kategori_transaksi] ?? ucwords(str_replace('_', ' ', $this->kategori_transaksi));
    }

    /**
     * Get available kategori transaksi
     */
    public static function getAvailableKategori()
    {
        return [
            'transfer_masuk' => 'Transfer Masuk',
            'transfer_keluar' => 'Transfer Keluar',
            'pembayaran_vendor' => 'Pembayaran Vendor',
            'pembayaran_gaji' => 'Pembayaran Gaji',
            'reimbursement' => 'Reimbursement Karyawan',
            'penerimaan_client' => 'Penerimaan dari Client',
            'biaya_operasional' => 'Biaya Operasional',
            'investasi' => 'Investasi',
            'pinjaman' => 'Pinjaman',
            'bunga_bank' => 'Bunga Bank',
            'biaya_admin' => 'Biaya Administrasi',
            'lainnya' => 'Lainnya'
        ];
    }

    /**
     * Generate nomor transaksi otomatis
     */
    public static function generateNomorTransaksi()
    {
        $prefix = 'TRX';
        $date = Carbon::now()->format('Ymd');
        $lastTransaction = self::where('nomor_transaksi', 'like', $prefix . $date . '%')
            ->orderBy('nomor_transaksi', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = intval(substr($lastTransaction->nomor_transaksi, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Verify transaksi
     */
    public function verify($userId = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $userId ?? auth()->id()
        ]);
    }

    /**
     * Unverify transaksi
     */
    public function unverify()
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null
        ]);
    }

    /**
     * Create transaksi dan update saldo rekening
     */
    public static function createTransaksi($data)
    {
        try {
            $rekening = Rekening::findOrFail($data['rekening_id']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('Rekening tidak ditemukan atau tidak aktif.');
        }

        $saldoSebelum = $rekening->saldo_saat_ini;
        
        // Hitung saldo sesudah
        if ($data['jenis_transaksi'] === 'debit') {
            $saldoSesudah = $saldoSebelum + $data['jumlah'];
        } else {
            $saldoSesudah = $saldoSebelum - $data['jumlah'];
            
            // Validasi saldo tidak boleh negatif (opsional, tergantung business rule)
            if ($saldoSesudah < 0 && !config('app.allow_negative_balance', false)) {
                throw new \Exception('Saldo tidak mencukupi untuk transaksi ini.');
            }
        }

        // Create transaksi
        $transaksi = self::create([
            'perusahaan_id' => auth()->user()->perusahaan_id,
            'rekening_id' => $data['rekening_id'],
            'tanggal_transaksi' => $data['tanggal_transaksi'],
            'jenis_transaksi' => $data['jenis_transaksi'],
            'jumlah' => $data['jumlah'],
            'saldo_sebelum' => $saldoSebelum,
            'saldo_sesudah' => $saldoSesudah,
            'kategori_transaksi' => $data['kategori_transaksi'],
            'keterangan' => $data['keterangan'],
            'referensi' => $data['referensi'] ?? null,
            'user_id' => auth()->id(),
            'metadata' => $data['metadata'] ?? null,
            'is_verified' => $data['is_verified'] ?? false
        ]);

        // Update saldo rekening
        $rekening->update(['saldo_saat_ini' => $saldoSesudah]);

        return $transaksi;
    }
}