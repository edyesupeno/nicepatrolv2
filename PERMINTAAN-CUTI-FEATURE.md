# Fitur Permintaan Cuti Karyawan

## Overview
Fitur ini menambahkan sistem manajemen permintaan cuti karyawan yang terintegrasi dengan batas cuti tahunan per project. Karyawan dapat mengajukan berbagai jenis cuti dan admin dapat menyetujui atau menolak permintaan tersebut.

## Fitur yang Ditambahkan

### 1. Database Migration
- **File**: `database/migrations/2026_01_23_095833_create_cutis_table.php`
- **Tabel**: `cutis`
- **Kolom Utama**:
  - `perusahaan_id`, `project_id`, `karyawan_id` (foreign keys)
  - `tanggal_mulai`, `tanggal_selesai`, `total_hari`
  - `jenis_cuti` (enum: tahunan, sakit, melahirkan, menikah, khitan, baptis, keluarga_meninggal, lainnya)
  - `alasan`, `catatan_approval`
  - `status` (enum: pending, approved, rejected)
  - `approved_by`, `approved_at`

### 2. Model Cuti
- **File**: `app/Models/Cuti.php`
- **Features**:
  - Hash ID untuk URL routing
  - Multi-tenancy dengan global scope
  - Relationships ke Perusahaan, Project, Karyawan, User
  - Accessors untuk status badge dan jenis cuti badge
  - Methods: `canEdit()`, `canDelete()`, `canApprove()`, `calculateTotalHari()`
  - Scopes: `pending()`, `approved()`, `rejected()`, `byProject()`, `byKaryawan()`, `byDateRange()`

### 3. Controller Cuti
- **File**: `app/Http/Controllers/Perusahaan/CutiController.php`
- **Methods**:
  - `index()` - Daftar cuti dengan filter dan statistik
  - `create()`, `store()` - Tambah permintaan cuti
  - `show()` - Detail permintaan cuti
  - `edit()`, `update()` - Edit permintaan cuti (hanya status pending)
  - `destroy()` - Hapus permintaan cuti (hanya status pending)
  - `approve()`, `reject()` - Setujui/tolak permintaan (AJAX)
  - `getKaryawansByProject()` - API untuk dropdown karyawan

### 4. Routes
- **File**: `routes/web.php`
- **Routes**:
  - Resource routes: `/perusahaan/cuti`
  - Approval routes: `/perusahaan/cuti/{cuti}/approve`, `/perusahaan/cuti/{cuti}/reject`
  - API route: `/perusahaan/cuti-karyawans/{project}`

### 5. Menu Sidebar
- **File**: `resources/views/perusahaan/layouts/app.blade.php`
- **Menu**: "Permintaan Cuti" di bawah "Permintaan Lembur" dalam grup "Presensi Karyawan"
- **Icon**: `fas fa-calendar-times`

### 6. Views
- **File**: `resources/views/perusahaan/cuti/index.blade.php`
- **Features**:
  - Statistics cards (Total, Pending, Disetujui, Ditolak)
  - Advanced filters (Project, Status, Jenis Cuti, Search Karyawan)
  - Data table dengan informasi lengkap
  - Modal approve/reject dengan AJAX
  - Inline actions (View, Edit, Delete, Approve, Reject)

## Jenis Cuti yang Didukung

1. **Cuti Tahunan** - Terbatas sesuai batas cuti tahunan project
2. **Sakit** - Tidak terbatas
3. **Melahirkan** - Tidak terbatas
4. **Menikah** - Tidak terbatas
5. **Khitan** - Tidak terbatas
6. **Baptis** - Tidak terbatas
7. **Keluarga Meninggal** - Tidak terbatas
8. **Lainnya** - Tidak terbatas

## Validasi Bisnis

### 1. Validasi Batas Cuti Tahunan
- Hanya berlaku untuk jenis cuti "tahunan"
- Mengecek total cuti tahunan yang sudah diambil dalam tahun berjalan
- Membandingkan dengan `batas_cuti_tahunan` dari project
- Menampilkan sisa cuti yang tersedia

### 2. Validasi Tanggal Overlapping
- Mengecek apakah ada cuti lain yang bertabrakan tanggal
- Berlaku untuk semua status kecuali "rejected"
- Mencegah double booking cuti

### 3. Validasi Tanggal
- Tanggal mulai tidak boleh kurang dari hari ini
- Tanggal selesai harus setelah atau sama dengan tanggal mulai
- Auto-calculate total hari berdasarkan range tanggal

## Workflow Approval

### 1. Status Pending
- Baru dibuat, menunggu persetujuan
- Dapat diedit dan dihapus oleh pembuat
- Dapat disetujui/ditolak oleh admin

### 2. Status Approved
- Sudah disetujui admin
- Tidak dapat diedit atau dihapus
- Masuk dalam perhitungan sisa cuti tahunan

### 3. Status Rejected
- Ditolak admin dengan alasan
- Tidak dapat diedit atau dihapus
- Tidak masuk dalam perhitungan sisa cuti

## Permissions & Access Control

### User Roles:
- **Karyawan**: Dapat membuat, edit, hapus permintaan cuti sendiri (status pending)
- **Admin**: Dapat melihat semua permintaan, approve/reject
- **Superadmin**: Full access ke semua fitur

### Multi-tenancy:
- Data ter-isolasi per perusahaan
- Global scope otomatis filter berdasarkan `perusahaan_id`
- Project scope sesuai akses user

## UI/UX Features

### 1. Statistics Dashboard
- Cards dengan icon dan warna berbeda per status
- Real-time count dari database

### 2. Advanced Filters
- Filter by Project, Status, Jenis Cuti
- Search by nama/NIK karyawan
- Persistent filter state di URL

### 3. Interactive Table
- Hover effects dan responsive design
- Color-coded badges untuk status dan jenis cuti
- Inline actions dengan tooltips

### 4. Modal Approval
- Separate modals untuk approve dan reject
- Form validation dengan SweetAlert
- AJAX submission tanpa reload halaman

### 5. Responsive Design
- Mobile-friendly layout
- Tailwind CSS styling
- Consistent dengan design system existing

## Integration dengan Fitur Lain

### 1. Project Management
- Menggunakan `batas_cuti_tahunan` dari project
- Filter berdasarkan project yang accessible user

### 2. Karyawan Management
- Dropdown karyawan berdasarkan project
- Display nama lengkap dan NIK karyawan

### 3. User Management
- Track created_by dan approved_by
- Permission-based access control

## Technical Notes

- **Hash ID**: Semua URL menggunakan hash ID untuk keamanan
- **CSRF Protection**: Semua form dan AJAX request ter-protect
- **SweetAlert2**: Untuk notifikasi dan konfirmasi
- **Tailwind CSS**: Untuk styling yang konsisten
- **Multi-tenancy**: Global scope dan validation
- **Database Indexes**: Untuk performa query yang optimal

## Testing Checklist

✅ Migration berhasil dijalankan
✅ Model relationships berfungsi
✅ Routes terdaftar dengan benar
✅ Menu sidebar muncul dan aktif
✅ Controller methods teruji
✅ Validasi bisnis berfungsi
✅ UI responsive dan konsisten
✅ **NEW**: View Index (Daftar Cuti) - Lengkap dengan filter dan approval
✅ **NEW**: View Create (Form Tambah) - Lengkap dengan validasi dan search karyawan
✅ **NEW**: View Edit (Form Edit) - Lengkap dengan data existing
✅ **NEW**: View Show (Detail) - Lengkap dengan timeline dan approval actions

## Next Steps (Future Development)

1. ~~**Create Form** - Form tambah permintaan cuti~~ ✅ **SELESAI**
2. ~~**Edit Form** - Form edit permintaan cuti~~ ✅ **SELESAI**
3. ~~**Detail View** - Halaman detail permintaan cuti~~ ✅ **SELESAI**
4. **Email Notifications** - Notifikasi approval via email
5. **Calendar Integration** - Tampilan kalender cuti
6. **Reporting** - Laporan cuti karyawan
7. **Mobile API** - API untuk mobile app
8. **Bulk Operations** - Approve/reject multiple cuti
9. **Advanced Batas Cuti Check** - Real-time API check sisa cuti tahunan