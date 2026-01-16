# Patrol Management Menu Structure

## Overview
Menu Patrol Management telah dibuat dengan struktur 3 menu utama yang collapsible, masing-masing dengan submenu. Semua halaman saat ini menampilkan "Under Construction" page.

## Menu Structure

### 1. Patrol (Menu Utama)
**Route Prefix:** `perusahaan.patrol.*`
**Controller:** `App\Http\Controllers\Perusahaan\PatrolController`

**Submenu:**
- **Kategori Insiden** 
  - Route: `perusahaan.patrol.kategori-insiden`
  - View: `resources/views/perusahaan/patrol/kategori-insiden.blade.php`
  - Progress: 15%
  
- **Area**
  - Route: `perusahaan.patrol.area`
  - View: `resources/views/perusahaan/patrol/area.blade.php`
  - Progress: 20%
  
- **Rute Patrol**
  - Route: `perusahaan.patrol.rute-patrol`
  - View: `resources/views/perusahaan/patrol/rute-patrol.blade.php`
  - Progress: 10%
  
- **Checkpoint**
  - Route: `perusahaan.patrol.checkpoint`
  - View: `resources/views/perusahaan/patrol/checkpoint.blade.php`
  - Progress: 100% ✅
  - **Status: COMPLETED**
  - **Features:**
    - CRUD checkpoint dengan modal
    - Relasi dengan rute patrol
    - QR Code auto-generate
    - Urutan checkpoint (sequence)
    - Koordinat GPS (latitude/longitude)
    - Alamat/lokasi checkpoint
    - Filter by rute & status
    - Search by nama/alamat/QR code
    - Multi-tenancy dengan global scope
    - Hash ID untuk URL
  
- **Aset Kawasan**
  - Route: `perusahaan.patrol.aset-kawasan`
  - View: `resources/views/perusahaan/patrol/aset-kawasan.blade.php`
  - Progress: 100% ✅
  - **Status: COMPLETED**
  - **Features:**
    - CRUD aset kawasan dengan modal
    - Upload foto aset (JPG, PNG, max 2MB)
    - Auto-generate kode aset
    - Kategori, merk, model, serial number
    - Filter by kategori & status
    - Search by nama/kode/kategori/merk/model
    - Preview foto saat upload
    - Multi-tenancy dengan global scope
    - Hash ID untuk URL
    - Relasi many-to-many dengan checkpoint

### 2. Tim Patroli (Menu Utama)
**Route Prefix:** `perusahaan.tim-patroli.*`
**Controller:** `App\Http\Controllers\Perusahaan\TimPatroliController`

**Submenu:**
- **Master Tim Patroli**
  - Route: `perusahaan.tim-patroli.master`
  - View: `resources/views/perusahaan/tim-patroli/master.blade.php`
  - Progress: 25%
  
- **Inventaris Patroli**
  - Route: `perusahaan.tim-patroli.inventaris`
  - View: `resources/views/perusahaan/tim-patroli/inventaris.blade.php`
  - Progress: 15%

### 3. Laporan Patroli (Menu Utama)
**Route Prefix:** `perusahaan.laporan-patroli.*`
**Controller:** `App\Http\Controllers\Perusahaan\LaporanPatroliController`

**Submenu:**
- **Laporan Insiden**
  - Route: `perusahaan.laporan-patroli.insiden`
  - View: `resources/views/perusahaan/laporan-patroli/insiden.blade.php`
  - Progress: 20%
  
- **Patroli Kawasan**
  - Route: `perusahaan.laporan-patroli.kawasan`
  - View: `resources/views/perusahaan/laporan-patroli/kawasan.blade.php`
  - Progress: 30%
  
- **Inventaris Patroli**
  - Route: `perusahaan.laporan-patroli.inventaris`
  - View: `resources/views/perusahaan/laporan-patroli/inventaris.blade.php`
  - Progress: 10%
  
- **Kru Change**
  - Route: `perusahaan.laporan-patroli.kru-change`
  - View: `resources/views/perusahaan/laporan-patroli/kru-change.blade.php`
  - Progress: 5%

## Under Construction Template

Semua view menggunakan template reusable: `resources/views/perusahaan/under-construction.blade.php`

**Template Parameters:**
- `title` - Browser title
- `pageTitle` - Page header title
- `pageSubtitle` - Page header subtitle
- `featureName` - Nama fitur yang ditampilkan
- `progress` - Progress bar percentage (0-100)
- `features` - Array of features yang akan datang
- `backUrl` - (Optional) Custom back URL

**Example Usage:**
```blade
@extends('perusahaan.under-construction', [
    'title' => 'Kategori Insiden',
    'pageTitle' => 'Kategori Insiden',
    'pageSubtitle' => 'Kelola kategori insiden untuk laporan patroli',
    'featureName' => 'Kategori Insiden',
    'progress' => 15,
    'features' => [
        'Tambah, edit, dan hapus kategori insiden',
        'Klasifikasi tingkat keparahan insiden',
        'Template laporan per kategori',
        'Statistik insiden per kategori'
    ]
])
```

## Files Created

### Controllers
- `app/Http/Controllers/Perusahaan/PatrolController.php`
- `app/Http/Controllers/Perusahaan/TimPatroliController.php`
- `app/Http/Controllers/Perusahaan/LaporanPatroliController.php`

### Views
- `resources/views/perusahaan/under-construction.blade.php` (Template)
- `resources/views/perusahaan/patrol/kategori-insiden.blade.php`
- `resources/views/perusahaan/patrol/area.blade.php`
- `resources/views/perusahaan/patrol/rute-patrol.blade.php`
- `resources/views/perusahaan/patrol/checkpoint.blade.php`
- `resources/views/perusahaan/patrol/aset-kawasan.blade.php`
- `resources/views/perusahaan/tim-patroli/master.blade.php`
- `resources/views/perusahaan/tim-patroli/inventaris.blade.php`
- `resources/views/perusahaan/laporan-patroli/insiden.blade.php`
- `resources/views/perusahaan/laporan-patroli/kawasan.blade.php`
- `resources/views/perusahaan/laporan-patroli/inventaris.blade.php`
- `resources/views/perusahaan/laporan-patroli/kru-change.blade.php`

### Routes
Routes ditambahkan di `routes/web.php` dalam group `perusahaan` middleware.

### Layout
Menu ditambahkan di `resources/views/perusahaan/layouts/app.blade.php` dengan collapsible functionality.

## Next Steps

Untuk mengimplementasikan fitur-fitur ini:

1. **Database Migration** - Buat migration untuk tabel yang diperlukan
2. **Models** - Buat model dengan relationship yang sesuai
3. **Controller Logic** - Implementasi CRUD di controller
4. **Views** - Replace under construction dengan UI yang sebenarnya
5. **Validation** - Tambahkan form validation
6. **Authorization** - Implementasi policy untuk access control
7. **Testing** - Buat test untuk setiap fitur

## Standards to Follow

Pastikan mengikuti project standards:
- ✅ Gunakan Hash ID untuk URL (bukan integer ID)
- ✅ Implementasi multi-tenancy dengan global scope
- ✅ Gunakan SweetAlert2 untuk notifikasi
- ✅ Optimasi query dengan select spesifik dan eager loading
- ✅ Implementasi race condition protection untuk data kritis
