# Fix Tim Patroli - Error Resolution

## Masalah yang Ditemukan

### 1. Error "Gagal memuat data" di halaman Tambah Tim Patroli
**Penyebab**: 
- Ambiguous column `perusahaan_id` dalam query JOIN
- Global scope menambahkan filter `perusahaan_id` tanpa prefix tabel

**Solusi**:
- Menambahkan prefix tabel yang spesifik: `rute_patrols.perusahaan_id`
- Menggunakan `withoutGlobalScope('perusahaan')` untuk query JOIN yang kompleks
- Menambahkan manual filter `perusahaan_id` dengan prefix tabel yang benar

### 2. Data Sample Tim Patroli Tidak Lengkap
**Penyebab**:
- Area Patrol hanya ada di Project ID 1, tapi seeder mencoba project lain
- Relasi antara Project dan AreaPatrol tidak konsisten

**Solusi**:
- Membuat seeder yang mencari project dengan area patrol yang ada
- Menambahkan relasi `areaPatrols()` di model Project
- Membuat tim patroli dengan data yang lengkap (areas, rutes, checkpoints)

## Perubahan yang Dilakukan

### 1. Controller: `app/Http/Controllers/Perusahaan/TimPatroliController.php`

#### Method `getDataByProject()`:
```php
// Perbaikan query dengan prefix tabel dan disable global scope
$rutes = RutePatrol::withoutGlobalScope('perusahaan')
    ->select('rute_patrols.id', 'rute_patrols.nama')
    ->join('area_patrols', 'rute_patrols.area_patrol_id', '=', 'area_patrols.id')
    ->where('area_patrols.project_id', $projectId)
    ->where('rute_patrols.is_active', true)
    ->where('rute_patrols.perusahaan_id', $perusahaanId)
    ->orderBy('rute_patrols.nama')
    ->get();

$checkpoints = Checkpoint::withoutGlobalScope('perusahaan')
    ->select('checkpoints.id', 'checkpoints.nama', 'checkpoints.rute_patrol_id', 'rute_patrols.nama as rute_nama')
    ->join('rute_patrols', 'checkpoints.rute_patrol_id', '=', 'rute_patrols.id')
    ->join('area_patrols', 'rute_patrols.area_patrol_id', '=', 'area_patrols.id')
    ->where('area_patrols.project_id', $projectId)
    ->where('checkpoints.is_active', true)
    ->where('checkpoints.perusahaan_id', $perusahaanId)
    ->orderBy('rute_patrols.nama')
    ->orderBy('checkpoints.urutan')
    ->get();
```

#### Perbaikan Error Handling:
- Menambahkan validasi `auth()->user()` tidak null
- Menambahkan response yang lebih informatif untuk error
- Menggunakan `first()` instead of `firstOrFail()` untuk handling yang lebih baik

### 2. Model: `app/Models/Project.php`

#### Menambahkan Relasi AreaPatrol:
```php
public function areaPatrols(): HasMany
{
    return $this->hasMany(AreaPatrol::class);
}
```

### 3. Data Sample
- Membuat tim patroli sample dengan data lengkap:
  - Tim: "Tim Patroli Alpha"
  - Project: "Kantor Jakarta" (ID: 1)
  - Areas: 2 (Lindai, Siak)
  - Rutes: 1 (Rute pengecekan gedung)
  - Checkpoints: 4 checkpoint

## Testing

### 1. API Endpoint Test
```bash
# Test dengan user yang sudah login
GET /perusahaan/tim-patroli/get-data-by-project/1

Response:
{
    "success": true,
    "areas": [...], // 2 items
    "rutes": [...], // 1 item  
    "checkpoints": [...], // 4 items
    "inventaris": [...], // 1 item
    "kuesioners": [...], // 1 item
    "pemeriksaans": [...] // 1 item
}
```

### 2. Database Verification
```sql
-- Verify tim patroli data
SELECT tp.*, p.nama as project_nama 
FROM tim_patrolis tp 
JOIN projects p ON tp.project_id = p.id;

-- Verify relationships
SELECT COUNT(*) FROM area_tim_patroli WHERE tim_patroli_id = 1; -- Should be 2
SELECT COUNT(*) FROM rute_tim_patroli WHERE tim_patroli_id = 1; -- Should be 1  
SELECT COUNT(*) FROM checkpoint_tim_patroli WHERE tim_patroli_id = 1; -- Should be 4
```

## Status

✅ **FIXED**: Error "Gagal memuat data" sudah teratasi
✅ **FIXED**: Query ambiguous column sudah diperbaiki
✅ **FIXED**: Data sample tim patroli sudah lengkap
✅ **TESTED**: Endpoint `getDataByProject` berfungsi normal
✅ **READY**: Halaman Tambah Tim Patroli siap untuk testing

## Next Steps

1. Test halaman Tambah Tim Patroli di browser
2. Verify form submission berfungsi dengan baik
3. Test create, edit, dan delete tim patroli
4. Verify multi-tenancy isolation berfungsi
5. Test dengan user dari perusahaan yang berbeda

## Notes

- Global scope `perusahaan` sangat berguna untuk isolasi data, tapi perlu hati-hati saat menggunakan JOIN query
- Selalu gunakan prefix tabel yang spesifik saat ada kemungkinan ambiguous column
- Untuk query JOIN yang kompleks, pertimbangkan untuk disable global scope dan tambahkan filter manual
- Pastikan data sample konsisten dengan relasi yang ada di database