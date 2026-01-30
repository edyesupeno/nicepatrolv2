# Peminjaman Aset - Display Issues Fix

## Masalah yang Ditemukan

User melaporkan adanya tampilan "Unknown" dan koma-koma di halaman peminjaman aset. Setelah investigasi, ditemukan beberapa masalah:

### 1. Accessor Model Issues

**Masalah:**
- Accessor `getAsetNamaAttribute()` menggunakan `nama_kendaraan` yang merupakan accessor, bukan field database
- Accessor `getPeminjamNamaAttribute()` menampilkan 'Unknown' jika tidak ada karyawan
- Accessor tidak memberikan pesan error yang jelas

**Perbaikan:**
- Fixed accessor untuk menggunakan field database langsung
- Improved error messages untuk debugging
- Added better null checking

### 2. Data Lama Tanpa aset_type

**Masalah:**
- Data peminjaman lama yang dibuat sebelum fitur dual asset type tidak memiliki `aset_type`
- Menyebabkan accessor tidak bisa menentukan tipe aset

**Perbaikan:**
- Created migration untuk update data lama
- Set `aset_type = 'data_aset'` untuk data yang memiliki `data_aset_id`
- Set default `aset_type = 'data_aset'` untuk data lainnya

### 3. Global Scope Issues

**Masalah:**
- Penggunaan `withoutGlobalScope('perusahaan')` mungkin menyebabkan masalah loading relasi
- Relasi tidak ter-load dengan benar

**Perbaikan:**
- Removed `withoutGlobalScope('perusahaan')` dari loading relasi
- Added `perusahaan_id` ke select fields untuk memastikan global scope bekerja
- Improved relation loading dengan explicit field selection

## Perbaikan yang Dilakukan

### 1. Model PeminjamanAset.php

**Accessor getAsetNamaAttribute():**
```php
public function getAsetNamaAttribute()
{
    if ($this->aset_type === 'aset_kendaraan') {
        if ($this->asetKendaraan) {
            return "{$this->asetKendaraan->merk} {$this->asetKendaraan->model} ({$this->asetKendaraan->tahun_pembuatan})";
        }
        return 'Kendaraan tidak ditemukan';
    } elseif ($this->aset_type === 'data_aset') {
        if ($this->dataAset) {
            return $this->dataAset->nama_aset;
        }
        return 'Aset tidak ditemukan';
    }
    return 'Tipe aset tidak valid';
}
```

**Accessor getPeminjamNamaAttribute():**
```php
public function getPeminjamNamaAttribute()
{
    if ($this->peminjamKaryawan) {
        return $this->peminjamKaryawan->nama_lengkap;
    }
    return 'Karyawan tidak ditemukan';
}
```

**Improved Accessors:**
- Better null checking
- More descriptive error messages
- Cleaner logic flow

### 2. Migration untuk Data Lama

**File:** `2026_01_29_181348_update_existing_peminjaman_aset_data.php`

```php
// Update existing peminjaman aset data that doesn't have aset_type
DB::table('peminjaman_asets')
    ->whereNull('aset_type')
    ->whereNotNull('data_aset_id')
    ->update(['aset_type' => 'data_aset']);
    
// Set default aset_type for any remaining null values
DB::table('peminjaman_asets')
    ->whereNull('aset_type')
    ->update(['aset_type' => 'data_aset']);
```

### 3. Controller Improvements

**Improved Relation Loading:**
```php
$query = PeminjamanAset::with([
    'project' => function($query) {
        $query->withoutGlobalScope('project_access')->select('id', 'nama');
    },
    'dataAset' => function($query) {
        $query->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
    },
    'asetKendaraan' => function($query) {
        $query->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
    },
    'peminjamKaryawan' => function($query) {
        $query->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
    },
    'createdBy:id,name'
]);
```

## Testing

Untuk memastikan perbaikan berhasil:

1. **Check Data Display**: Pastikan tidak ada lagi "Unknown" atau koma-koma
2. **Check Asset Names**: Pastikan nama aset dan kendaraan tampil dengan benar
3. **Check Employee Names**: Pastikan nama karyawan tampil dengan benar
4. **Check Old Data**: Pastikan data lama yang sudah di-update tampil dengan benar

## Expected Results

Setelah perbaikan:
- ✅ Tidak ada lagi tampilan "Unknown"
- ✅ Tidak ada lagi koma-koma yang tidak perlu
- ✅ Nama aset tampil dengan format yang benar
- ✅ Nama karyawan tampil dengan benar
- ✅ Data lama sudah ter-update dengan aset_type yang benar
- ✅ Relasi ter-load dengan benar tanpa masalah global scope

## Preventive Measures

1. **Data Validation**: Pastikan semua data baru memiliki aset_type yang valid
2. **Relation Checking**: Pastikan relasi selalu ter-load dengan benar
3. **Error Handling**: Accessor memberikan pesan error yang jelas untuk debugging
4. **Migration Testing**: Test migration pada data production sebelum deploy

Perbaikan ini mengatasi masalah display dan memastikan data tampil dengan benar di halaman peminjaman aset.