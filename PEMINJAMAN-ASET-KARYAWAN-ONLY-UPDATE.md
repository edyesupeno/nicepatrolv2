# Peminjaman Aset - Karyawan Only Update

## Perubahan yang Dilakukan

User meminta untuk menghapus opsi User dan hanya menyisakan Karyawan saja sebagai peminjam aset. Berikut adalah perubahan yang telah dilakukan:

### 1. Form Create (resources/views/perusahaan/peminjaman-aset/create.blade.php)

**Dihapus:**
- Radio button untuk memilih tipe peminjam (Karyawan/User)
- Section untuk peminjam User
- JavaScript untuk toggle antara Karyawan dan User
- JavaScript untuk search User
- CSS untuk user search dropdown

**Dipertahankan:**
- Hanya section untuk Karyawan Peminjam
- Search functionality untuk Karyawan
- Validasi dan feedback untuk pencarian Karyawan

### 2. Controller (app/Http/Controllers/Perusahaan/PeminjamanAsetController.php)

**Method store():**
- Dihapus validasi `peminjam_type`
- Dihapus validasi `peminjam_user_id`
- Langsung set `peminjam_user_id = null`
- Hanya validasi `peminjam_karyawan_id` sebagai required

**Method update():**
- Perubahan yang sama dengan store()
- Dihapus logika untuk toggle peminjam type

**Method edit():**
- Dihapus loading data karyawan dan user untuk dropdown
- Hanya pass data yang diperlukan untuk search functionality

**Method index(), show(), jatuhTempo(), exportBuktiPeminjaman():**
- Dihapus loading relasi `peminjamUser`
- Dihapus field `peminjam_user_id` dari select queries

**API Methods:**
- Dihapus method `searchUser()`
- Dipertahankan method `searchKaryawan()`

### 3. Model (app/Models/PeminjamanAset.php)

**Accessors:**
- `getPeminjamNamaAttribute()`: Hanya return nama karyawan
- `getPeminjamTipeAttribute()`: Selalu return 'karyawan'

**Search Scope:**
- Dihapus pencarian berdasarkan peminjamUser
- Hanya search berdasarkan peminjamKaryawan

### 4. Routes (routes/web.php)

**Dihapus:**
- Route untuk `search-user`

**Dipertahankan:**
- Route untuk `search-karyawan`
- Route untuk `search-asets`

## Struktur Form Sekarang

```
1. Project (dropdown) - Required
2. Tipe Aset (dropdown: Aset/Kendaraan) - Required  
3. Aset yang Dipinjam (search input) - Required
4. Karyawan Peminjam (search input) - Required
5. Jumlah Dipinjam (number input) - Required
6. Tanggal Peminjaman (date input) - Required
7. Tanggal Rencana Kembali (date input) - Required
8. Kondisi Saat Dipinjam (dropdown) - Required
9. Keperluan (textarea) - Required
10. Catatan Peminjaman (textarea) - Optional
11. File Bukti Peminjaman (file input) - Optional
```

## Validasi yang Berlaku

```php
[
    'project_id' => 'required|exists:projects,id',
    'aset_type' => 'required|in:data_aset,aset_kendaraan',
    'data_aset_id' => 'required_if:aset_type,data_aset|nullable|exists:data_asets,id',
    'aset_kendaraan_id' => 'required_if:aset_type,aset_kendaraan|nullable|exists:aset_kendaraans,id',
    'peminjam_karyawan_id' => 'required|exists:karyawans,id',
    'tanggal_peminjaman' => 'required|date|after_or_equal:today',
    'tanggal_rencana_kembali' => 'required|date|after:tanggal_peminjaman',
    'jumlah_dipinjam' => 'required|integer|min:1|max:100',
    'keperluan' => 'required|string|max:1000',
    'kondisi_saat_dipinjam' => 'required|in:baik,rusak_ringan,rusak_berat',
    'catatan_peminjaman' => 'nullable|string|max:1000',
    'file_bukti_peminjaman' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
]
```

## Database Impact

Tidak ada perubahan pada database schema. Field `peminjam_user_id` tetap ada di tabel tapi akan selalu berisi `null` untuk data baru. Data lama yang sudah ada dengan `peminjam_user_id` tidak akan terpengaruh.

## Cara Penggunaan

1. **Pilih Project** - Wajib dipilih terlebih dahulu
2. **Pilih Tipe Aset** - Aset atau Kendaraan
3. **Cari Aset** - Ketik minimal 2 karakter untuk mencari aset berdasarkan project yang dipilih
4. **Cari Karyawan** - Ketik minimal 2 karakter untuk mencari karyawan berdasarkan nama atau NIK
5. **Isi form lainnya** - Lengkapi semua field yang required
6. **Submit** - Simpan peminjaman

## Fitur Search yang Tersisa

1. **Asset Search**: 
   - Filter berdasarkan project yang dipilih
   - Support untuk Data Aset dan Aset Kendaraan
   - Real-time search dengan debouncing

2. **Karyawan Search**:
   - Search berdasarkan nama lengkap atau NIK
   - Real-time search dengan debouncing
   - Hanya karyawan aktif yang ditampilkan

## Testing

Untuk menguji perubahan:

1. Buka form create peminjaman aset
2. Pastikan tidak ada opsi untuk memilih User
3. Pastikan search karyawan berfungsi dengan baik
4. Pastikan validasi form berjalan dengan benar
5. Pastikan data tersimpan dengan `peminjam_user_id = null`

Form sekarang lebih sederhana dan fokus hanya pada karyawan sebagai peminjam aset.