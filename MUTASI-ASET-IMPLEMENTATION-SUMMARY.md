# Mutasi Aset Implementation Summary

## Overview
Implementasi fitur Mutasi Aset untuk pemindahan aset antar project dalam satu perusahaan. Fitur ini memungkinkan pemindahan aset (Data Aset dan Aset Kendaraan) dari satu project ke project lain dengan workflow approval.

## Features Implemented

### 1. Database Structure
- **Migration**: `2026_01_29_200000_create_mutasi_asets_table.php`
- **Model**: `MutasiAset.php` dengan traits dan relationships
- **Seeder**: `MutasiAsetSeeder.php` untuk sample data

### 2. Core Functionality
- **CRUD Operations**: Create, Read, Update, Delete mutasi aset
- **Approval Workflow**: Pending → Disetujui/Ditolak → Selesai
- **Asset Transfer**: Automatic project_id update when completed
- **Multi-tenancy**: Global scope untuk isolasi data per perusahaan
- **Hash ID**: URL menggunakan hash ID untuk security

### 3. Controllers & Routes
- **Controller**: `MutasiAsetController.php`
- **Routes**: Resource routes + custom routes untuk approval, complete, print, laporan
- **Middleware**: PerusahaanMiddleware untuk multi-tenancy

### 4. Views & UI
- **Index**: List mutasi dengan filter dan search
- **Create**: Form tambah mutasi dengan validasi
- **Show**: Detail mutasi dengan action buttons
- **Edit**: Form edit untuk mutasi pending
- **Print PDF**: Berita acara mutasi
- **Laporan**: Report dengan statistik dan export PDF

### 5. Key Features

#### Asset Management
- Support untuk Data Aset dan Aset Kendaraan
- Validasi aset tersedia dan berada di project asal
- Auto-update project_id saat mutasi selesai

#### Workflow Management
- Status: pending, disetujui, ditolak, selesai
- Approval dengan catatan
- Complete action untuk finalisasi mutasi

#### Document Management
- Upload dokumen pendukung
- Print berita acara mutasi
- Export laporan PDF

#### Reporting & Analytics
- Filter berdasarkan tanggal, status, tipe aset
- Statistik berdasarkan tipe aset dan status
- Export laporan ke PDF

## File Structure

```
app/
├── Http/Controllers/Perusahaan/
│   └── MutasiAsetController.php
├── Models/
│   └── MutasiAset.php
└── Traits/
    └── HasHashId.php

database/
├── migrations/
│   └── 2026_01_29_200000_create_mutasi_asets_table.php
└── seeders/
    └── MutasiAsetSeeder.php

resources/views/perusahaan/mutasi-aset/
├── index.blade.php
├── create.blade.php
├── show.blade.php
├── edit.blade.php
├── print-pdf.blade.php
├── laporan.blade.php
└── laporan-pdf.blade.php

routes/
└── web.php (added mutasi-aset routes)
```

## Database Schema

### mutasi_asets Table
```sql
- id (bigint, primary key)
- perusahaan_id (foreign key to perusahaans)
- nomor_mutasi (string, unique)
- tanggal_mutasi (date)
- asset_type (enum: data_aset, aset_kendaraan)
- asset_id (bigint)
- karyawan_id (foreign key to karyawans)
- project_asal_id (foreign key to projects)
- project_tujuan_id (foreign key to projects)
- keterangan (text, nullable)
- alasan_mutasi (text)
- status (enum: pending, disetujui, ditolak, selesai)
- disetujui_oleh (foreign key to users, nullable)
- tanggal_persetujuan (timestamp, nullable)
- catatan_persetujuan (text, nullable)
- dokumen_pendukung (string, nullable)
- created_at, updated_at
```

### Indexes
- perusahaan_id, status
- asset_type, asset_id
- tanggal_mutasi
- nomor_mutasi
- project_asal_id, project_tujuan_id

## Routes

```php
// Resource routes
Route::resource('mutasi-aset', MutasiAsetController::class);

// Custom routes
Route::post('mutasi-aset/{mutasiAset}/approve', 'approve');
Route::post('mutasi-aset/{mutasiAset}/complete', 'complete');
Route::get('mutasi-aset/{mutasiAset}/print', 'printMutasi');
Route::get('mutasi-aset-laporan', 'laporan');
```

## Key Methods

### MutasiAsetController
- `index()`: List dengan filter dan pagination
- `create()`: Form create dengan data aset dan project
- `store()`: Validasi dan simpan mutasi baru
- `show()`: Detail mutasi dengan action buttons
- `edit()`: Form edit untuk status pending
- `update()`: Update mutasi pending
- `destroy()`: Hapus mutasi pending
- `approve()`: Approve/reject mutasi
- `complete()`: Finalisasi mutasi dan update asset
- `printMutasi()`: Generate PDF berita acara
- `laporan()`: Report dengan filter dan export

### MutasiAset Model
- `generateNomorMutasi()`: Auto-generate nomor mutasi
- `getAssetAttribute()`: Polymorphic relationship ke aset
- `getAssetNameAttribute()`: Nama aset berdasarkan tipe
- `getStatusBadgeAttribute()`: HTML badge untuk status
- Relationships: perusahaan, karyawan, projectAsal, projectTujuan, disetujuiOleh

## Validation Rules

### Create/Update
- tanggal_mutasi: required, date
- asset_type: required, in:data_aset,aset_kendaraan
- asset_id: required, integer
- karyawan_id: required, exists:karyawans,id
- project_asal_id: required, exists:projects,id
- project_tujuan_id: required, exists:projects,id, different:project_asal_id
- alasan_mutasi: required, string
- keterangan: nullable, string
- dokumen_pendukung: nullable, file, mimes:pdf,jpg,jpeg,png, max:2048

### Approval
- action: required, in:approve,reject
- catatan_persetujuan: nullable, string

## Security Features

### Multi-tenancy
- Global scope untuk filter berdasarkan perusahaan_id
- Auto-assign perusahaan_id saat create
- Validasi ownership di semua operations

### Hash ID
- URL menggunakan hash ID untuk obfuscation
- Route model binding dengan hash_id
- Security melalui HasHashId trait

### Authorization
- Middleware PerusahaanMiddleware
- Role-based access control
- File upload validation

## Business Logic

### Workflow States
1. **Pending**: Mutasi baru dibuat, menunggu persetujuan
2. **Disetujui**: Mutasi disetujui, siap untuk dipindahkan
3. **Ditolak**: Mutasi ditolak dengan alasan
4. **Selesai**: Mutasi selesai, aset sudah dipindahkan

### Asset Transfer Process
1. User membuat mutasi aset
2. Admin/Manager approve mutasi
3. Admin complete mutasi
4. System update project_id pada aset
5. Mutasi status menjadi selesai

### Validation Logic
- Aset harus tersedia/aktif
- Aset harus berada di project asal
- Project tujuan harus berbeda dari project asal
- Hanya mutasi pending yang bisa diedit/dihapus
- Hanya mutasi disetujui yang bisa diselesaikan

## UI/UX Features

### Index Page
- Filter berdasarkan search, status, asset_type, date range
- Pagination dengan query string preservation
- Action buttons berdasarkan status
- Responsive table dengan badge status

### Create/Edit Form
- Dynamic asset loading berdasarkan tipe
- Auto-populate project asal dari aset
- Validation project tujuan berbeda dari asal
- File upload untuk dokumen pendukung

### Detail Page
- Comprehensive information display
- Asset details berdasarkan tipe
- Approval actions dengan SweetAlert
- Complete action untuk finalisasi

### Reporting
- Summary cards dengan statistik
- Filter dan export PDF
- Detailed statistics by asset type and status
- Professional PDF layout

## Integration Points

### Existing Models
- **DataAset**: Polymorphic relationship
- **AsetKendaraan**: Polymorphic relationship
- **Karyawan**: Foreign key relationship
- **Project**: Foreign key relationships (asal & tujuan)
- **User**: Foreign key untuk approval
- **Perusahaan**: Multi-tenancy relationship

### Navigation
- Added to "Aset Operasional" submenu
- Icon: fas fa-exchange-alt
- Route highlighting untuk active state

## Testing Data
- Seeder dengan 5+ sample mutasi
- Different statuses dan asset types
- Realistic business scenarios
- Proper relationships dan foreign keys

## Performance Considerations

### Database Optimization
- Proper indexing untuk query performance
- Eager loading untuk relationships
- Pagination untuk large datasets
- Efficient filtering dengan query builder

### File Management
- Organized storage dalam mutasi-aset/dokumen
- File cleanup saat delete/update
- Proper file validation dan security

## Future Enhancements

### Potential Features
- Bulk mutasi untuk multiple assets
- Email notifications untuk approval
- Asset tracking history
- Integration dengan barcode scanning
- Mobile app support
- Advanced reporting dengan charts

### Technical Improvements
- API endpoints untuk mobile
- Real-time notifications
- Audit trail untuk semua changes
- Advanced search dengan Elasticsearch
- Automated approval rules

## Conclusion

Fitur Mutasi Aset telah berhasil diimplementasikan dengan:
- ✅ Complete CRUD operations
- ✅ Approval workflow
- ✅ Multi-tenancy support
- ✅ Security dengan Hash ID
- ✅ Professional UI/UX
- ✅ Comprehensive reporting
- ✅ PDF generation
- ✅ File upload support
- ✅ Proper validation
- ✅ Database optimization

Fitur ini siap untuk production use dan dapat di-extend sesuai kebutuhan bisnis yang berkembang.