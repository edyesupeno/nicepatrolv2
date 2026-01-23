# Fitur Batas Cuti Tahunan Project

## Overview
Fitur ini menambahkan pengaturan batas maksimal cuti tahunan per project. Setiap project dapat memiliki kebijakan cuti yang berbeda sesuai kebutuhan.

## Fitur yang Ditambahkan

### 1. Database Migration
- **File**: `database/migrations/2026_01_23_094541_add_batas_cuti_tahunan_to_projects_table.php`
- **Kolom**: `batas_cuti_tahunan` (integer, default: 12)
- **Deskripsi**: Batas maksimal cuti karyawan per tahun (dalam hari)

### 2. Model Update
- **File**: `app/Models/Project.php`
- **Update**: Menambahkan `batas_cuti_tahunan` ke `$fillable`

### 3. Controller Update
- **File**: `app/Http/Controllers/Perusahaan/ProjectController.php`
- **Update**: 
  - Validasi field `batas_cuti_tahunan` di method `store()` dan `update()`
  - **NEW**: Method `updateBatasCuti()` untuk inline edit
  - Range: 1-365 hari
  - Required field

### 4. Routes Update
- **File**: `routes/web.php`
- **NEW**: Route `PUT /perusahaan/projects/{project}/batas-cuti` untuk inline edit

### 5. View Update
- **File**: `resources/views/perusahaan/projects/index.blade.php`
- **Update**:
  - Menampilkan batas cuti di card project
  - Form input di modal create dan edit
  - **NEW**: Inline edit dengan tombol pensil
  - **NEW**: JavaScript untuk handle inline edit dengan keyboard support

## Penggunaan

### 1. Menambah Project Baru
- Buka menu **Perusahaan → Projects**
- Klik **Tambah Project**
- Isi field **Batas Cuti Tahunan** (default: 12 hari)
- Range: 1-365 hari per tahun

### 2. Edit Project Existing (Modal)
- Klik icon edit (pensil) di card project
- Update field **Batas Cuti Tahunan**
- Simpan perubahan

### 3. **NEW: Inline Edit Batas Cuti**
- **Hover** pada baris "Batas Cuti" di card project
- Klik **icon pensil** yang muncul
- Edit nilai langsung di input field
- **Enter** untuk simpan, **Escape** untuk batal
- Atau klik icon **✓** untuk simpan, **✗** untuk batal

### 4. Melihat Batas Cuti
- Informasi batas cuti ditampilkan di card project
- Format: "X hari/tahun"

## Fitur Inline Edit

### UI/UX Features:
- **Hover Effect**: Tombol pensil muncul saat hover pada baris batas cuti
- **Instant Edit**: Klik pensil langsung mengubah tampilan ke mode edit
- **Keyboard Support**: 
  - **Enter** = Simpan perubahan
  - **Escape** = Batal edit
- **Visual Feedback**: 
  - Loading spinner saat menyimpan
  - SweetAlert untuk success/error message
- **Auto Focus**: Input langsung ter-focus dan ter-select saat edit

### Technical Features:
- **AJAX Request**: Update tanpa reload halaman
- **Real-time Validation**: Validasi 1-365 hari
- **Error Handling**: Rollback jika gagal simpan
- **CSRF Protection**: Token CSRF untuk keamanan

## Validasi
- **Required**: Field wajib diisi
- **Min**: 1 hari per tahun
- **Max**: 365 hari per tahun
- **Type**: Integer

## Default Value
- Semua project baru: **12 hari/tahun**
- Project existing: Otomatis diset **12 hari/tahun**

## Implementasi Future
Fitur ini dapat diintegrasikan dengan:
1. **Sistem Cuti Karyawan** - Validasi pengajuan cuti berdasarkan batas project
2. **Dashboard Analytics** - Monitoring penggunaan cuti per project
3. **Laporan HR** - Analisis trend cuti karyawan per project
4. **Notifikasi** - Alert jika karyawan mendekati batas cuti

## Technical Notes
- Menggunakan Tailwind CSS untuk styling
- SweetAlert2 untuk notifikasi
- Hash ID untuk URL routing
- Multi-tenancy compliant (perusahaan_id scope)
- Fetch API untuk AJAX requests
- Event delegation untuk keyboard handling

## Testing
✅ Migration berhasil dijalankan
✅ Default value 12 hari terset untuk semua project
✅ Form validation berfungsi
✅ UI responsive dan konsisten
✅ **NEW**: Inline edit berfungsi dengan AJAX
✅ **NEW**: Keyboard shortcuts (Enter/Escape) berfungsi
✅ **NEW**: Route dan controller method teruji