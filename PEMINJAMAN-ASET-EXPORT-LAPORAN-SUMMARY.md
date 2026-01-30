# Peminjaman Aset - Export Laporan Feature

## Fitur yang Ditambahkan

Telah berhasil menambahkan fitur export laporan PDF untuk peminjaman aset dengan filter yang sesuai dengan halaman index.

### 1. Controller Method (PeminjamanAsetController.php)

**Method exportLaporan():**
- Menggunakan filter yang sama dengan index
- Support filter: project, status, aset_type, tanggal_dari, tanggal_sampai, search, terlambat
- Export ke PDF format landscape A4
- Include informasi filter dan statistik ringkasan

### 2. Route Baru

```php
Route::get('peminjaman-aset-export-laporan', [PeminjamanAsetController::class, 'exportLaporan'])
    ->name('peminjaman-aset.export-laporan');
```

### 3. View PDF Template (laporan-pdf.blade.php)

**Fitur PDF:**
- Header dengan informasi perusahaan
- Informasi filter yang diterapkan
- Ringkasan statistik (total, pending, dipinjam, dikembalikan, terlambat)
- Tabel detail peminjaman dengan kolom lengkap
- Status badge dengan warna sesuai status
- Footer dengan informasi sistem

### 4. UI Improvements (index.blade.php)

**Filter Section:**
- Layout grid yang lebih rapi dengan label
- Tambahan filter tanggal (dari/sampai)
- Tombol Filter dan Reset
- Counter total data

**Export Button:**
- Tombol "Export Laporan" di header
- Modal export dengan preview filter
- Copy filter dari halaman utama ke modal

### 5. Modal Export

**Features:**
- Form dengan semua filter yang tersedia
- Auto-populate dengan filter saat ini
- Informasi tentang export
- Loading state saat generate PDF

## Cara Penggunaan

1. **Akses Halaman**: Buka `/perusahaan/peminjaman-aset`
2. **Set Filter**: Pilih filter sesuai kebutuhan (project, status, tanggal, dll)
3. **Export**: Klik tombol "Export Laporan"
4. **Review Filter**: Modal akan muncul dengan filter yang sudah terisi
5. **Generate**: Klik "Export PDF" untuk download laporan

## Filter yang Tersedia

1. **Project**: Filter berdasarkan project tertentu
2. **Status**: Filter berdasarkan status peminjaman
3. **Tipe Aset**: Filter berdasarkan tipe aset (Aset/Kendaraan)
4. **Tanggal Dari/Sampai**: Filter berdasarkan tanggal peminjaman
5. **Pencarian**: Search berdasarkan kode, aset, atau peminjam
6. **Terlambat**: Filter hanya peminjaman yang terlambat

## Konten Laporan PDF

### Header
- Nama perusahaan
- Alamat, telepon, email perusahaan
- Judul laporan
- Tanggal cetak

### Filter Information
- Menampilkan semua filter yang diterapkan
- Format yang mudah dibaca

### Ringkasan Statistik
- Total peminjaman
- Jumlah menunggu persetujuan
- Jumlah sedang dipinjam
- Jumlah sudah dikembalikan
- Jumlah terlambat

### Tabel Detail
- No urut
- Kode peminjaman
- Project
- Informasi aset (kode, nama, kategori)
- Peminjam (nama, NIK)
- Tanggal pinjam dan rencana kembali
- Jumlah dipinjam
- Status dengan badge warna
- Kondisi aset
- Keperluan

## Technical Details

- **PDF Library**: DomPDF
- **Paper Size**: A4 Landscape
- **Styling**: Inline CSS untuk kompatibilitas PDF
- **Performance**: Efficient query dengan select specific columns
- **Memory**: Optimized untuk dataset besar

Fitur export laporan ini memberikan kemudahan untuk membuat laporan peminjaman aset yang komprehensif dengan filter yang fleksibel sesuai kebutuhan bisnis.