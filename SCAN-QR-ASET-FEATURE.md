# Fitur Scan QR Code untuk Pengecekan Aset

## Overview
Fitur ini memungkinkan security officer untuk melakukan scan QR code checkpoint dan memeriksa kondisi aset-aset yang ada di checkpoint tersebut melalui mobile app.

## Flow Aplikasi

### 1. Scan QR Code Checkpoint
- Security officer membuka mobile app dan tap tombol "Scan" di bottom navigation
- Kamera terbuka untuk scan QR code checkpoint
- Setelah scan berhasil, sistem akan:
  - Validasi QR code checkpoint
  - Mencatat waktu scan dan lokasi GPS
  - Redirect ke halaman detail checkpoint dengan daftar aset

### 2. Pengecekan Aset
- Tampil daftar semua aset yang ada di checkpoint
- Security officer harus memeriksa setiap aset dan menentukan status:
  - **Aman**: Aset dalam kondisi baik
  - **Rusak**: Aset mengalami kerusakan
  - **Hilang**: Aset tidak ditemukan
- Untuk status "Rusak" atau "Hilang", wajib mengisi catatan
- Wajib mengambil minimal 1 foto dokumentasi

### 3. Submit Laporan
- Setelah semua aset diperiksa dan foto diambil, submit laporan
- Data tersimpan ke database untuk audit trail

## Komponen yang Dibuat

### 1. API Controllers

#### `app/Http/Controllers/Api/PatroliController.php`
- **Method `scanCheckpoint()`**: Untuk scan QR code checkpoint
- Validasi QR code, cek duplikasi scan, simpan patrol detail

#### `app/Http/Controllers/Api/AsetCheckpointController.php`
- **Method `checkpointAsets()`**: Get daftar aset di checkpoint
- **Method `updateAsetStatus()`**: Update status pengecekan aset

### 2. Models

#### `app/Models/AsetCheck.php`
- Model untuk menyimpan hasil pengecekan aset
- Relasi ke PatroliDetail dan AsetKawasan

### 3. Database Migration

#### `database/migrations/2026_01_18_093222_create_aset_checks_table.php`
- Tabel untuk menyimpan hasil pengecekan aset
- Fields: patroli_detail_id, aset_kawasan_id, status, catatan, foto

### 4. Mobile Views

#### `resources/views/mobile/security/scan.blade.php`
- Interface untuk scan QR code
- Menggunakan library html5-qrcode
- Support manual input QR code sebagai fallback
- Integrasi dengan GPS untuk lokasi

#### `resources/views/mobile/security/checkpoint.blade.php`
- Interface untuk pengecekan aset
- Daftar aset dengan toggle status (Aman/Rusak/Hilang)
- Camera integration untuk foto dokumentasi
- Form validation sebelum submit

### 5. Routes

#### API Routes (`routes/api.php`)
```php
Route::post('patrolis/{patroli}/scan', [PatroliController::class, 'scanCheckpoint']);
Route::get('checkpoints/{checkpoint}/asets', [AsetCheckpointController::class, 'checkpointAsets']);
Route::post('checkpoints/{checkpoint}/aset-status', [AsetCheckpointController::class, 'updateAsetStatus']);
```

#### Web Routes (`routes/web.php`)
```php
Route::get('/security/scan', function() {
    return view('mobile.security.scan');
});
Route::get('/security/checkpoint/{checkpoint}', function($checkpoint) {
    return view('mobile.security.checkpoint');
});
```

## Fitur Keamanan

### 1. Multi-Tenancy
- Semua data ter-filter berdasarkan `perusahaan_id`
- Global scope pada model Checkpoint dan AsetKawasan
- Auto-assign `perusahaan_id` saat create data

### 2. Hash ID
- Semua URL menggunakan hash_id, bukan integer ID
- Implementasi di model dengan trait `HasHashId`

### 3. Authentication
- Semua API endpoint protected dengan `auth:sanctum`
- Token-based authentication untuk mobile app

### 4. Validation
- Validasi QR code checkpoint
- Cek duplikasi scan dalam satu patroli
- Validasi ownership aset terhadap checkpoint
- Validasi kelengkapan data sebelum submit

## Teknologi yang Digunakan

### Frontend (Mobile)
- **HTML5 QR Code Scanner**: Library untuk scan QR code
- **Camera API**: Untuk ambil foto dokumentasi
- **Geolocation API**: Untuk GPS coordinates
- **SweetAlert2**: Untuk notifikasi user-friendly
- **Tailwind CSS**: Untuk styling responsive

### Backend (API)
- **Laravel Sanctum**: Authentication
- **Eloquent ORM**: Database operations
- **Database Transactions**: Untuk data consistency
- **File Storage**: Untuk simpan foto dokumentasi

## Testing

### 1. Manual Testing
1. Login sebagai security officer
2. Buka `/security/scan`
3. Scan QR code checkpoint yang valid
4. Periksa semua aset dan set status
5. Ambil foto dokumentasi
6. Submit laporan
7. Verifikasi data tersimpan di database

### 2. API Testing
```bash
# Test scan checkpoint
POST /api/v1/patrolis/{patroli_id}/scan
{
    "qr_code": "CP-AREA-A-001",
    "latitude": -6.2088,
    "longitude": 106.8456
}

# Test get checkpoint assets
GET /api/v1/checkpoints/{checkpoint_id}/asets

# Test update asset status
POST /api/v1/checkpoints/{checkpoint_id}/aset-status
{
    "patroli_detail_id": 1,
    "aset_checks": [
        {
            "aset_id": 1,
            "status": "aman",
            "catatan": null,
            "foto": "base64_image_string"
        }
    ]
}
```

## Database Schema

### Tabel `aset_checks`
```sql
CREATE TABLE aset_checks (
    id BIGINT PRIMARY KEY,
    patroli_detail_id BIGINT FOREIGN KEY,
    aset_kawasan_id BIGINT FOREIGN KEY,
    status ENUM('aman', 'rusak', 'hilang'),
    catatan TEXT NULL,
    foto VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Relasi Database
- `aset_checks` → `patroli_details` (many-to-one)
- `aset_checks` → `aset_kawasans` (many-to-one)
- `checkpoints` → `aset_kawasans` (many-to-many via `aset_checkpoint`)

## UI/UX Features

### 1. Scan QR Code Page
- Camera preview dengan overlay frame
- Manual input sebagai fallback
- Loading indicator saat processing
- Error handling dengan pesan yang jelas

### 2. Checkpoint Detail Page
- Info checkpoint dengan foto (jika ada)
- Daftar aset dengan foto thumbnail
- Toggle button untuk status (Aman/Rusak/Hilang)
- Conditional catatan field untuk status bermasalah
- Camera integration untuk dokumentasi
- Progress indicator untuk kelengkapan data

### 3. Bottom Navigation
- Tombol "Scan" di tengah dengan design prominent
- Icon QR code yang jelas
- Consistent dengan design system

## Error Handling

### 1. QR Code Errors
- QR code tidak valid
- Checkpoint tidak aktif
- Checkpoint sudah di-scan sebelumnya
- Tidak ada patroli aktif

### 2. Camera Errors
- Permission denied
- Camera tidak tersedia
- Foto gagal diambil

### 3. Network Errors
- Connection timeout
- Server error
- Invalid response

## Performance Optimization

### 1. Database
- Index pada kolom yang sering di-query
- Eager loading untuk relasi
- Select specific columns saja

### 2. Frontend
- Lazy loading untuk foto
- Compress foto sebelum upload
- Cache data yang tidak sering berubah

### 3. API
- Pagination untuk list data
- Response compression
- Proper HTTP status codes

## Security Considerations

### 1. Data Privacy
- Foto tersimpan dengan nama file random
- Path foto tidak predictable
- Access control untuk file download

### 2. Input Validation
- Sanitize semua input user
- Validate file type dan size untuk foto
- XSS protection

### 3. Rate Limiting
- Limit API calls per user
- Prevent spam scanning
- Throttle foto upload

## Deployment Checklist

- [ ] Migration `aset_checks` table sudah dijalankan
- [ ] Storage folder untuk foto sudah ada dan writable
- [ ] API routes sudah terdaftar
- [ ] Web routes untuk mobile sudah terdaftar
- [ ] HTTPS enabled untuk camera access
- [ ] Mobile domain configuration sudah benar
- [ ] Database indexes sudah optimal
- [ ] Error logging sudah aktif
- [ ] Backup strategy untuk foto dokumentasi

## Future Enhancements

### 1. Offline Support
- Cache data checkpoint dan aset
- Queue foto upload saat offline
- Sync data saat kembali online

### 2. Advanced Features
- Barcode scanning selain QR code
- Voice notes untuk catatan
- Real-time notification ke supervisor
- Analytics dashboard untuk patrol performance

### 3. Integration
- Export laporan ke PDF
- Integration dengan sistem inventory
- Push notification untuk reminder patrol

## Troubleshooting

### 1. QR Scanner Tidak Berfungsi
- Pastikan HTTPS enabled
- Check camera permission
- Verify browser compatibility

### 2. Foto Tidak Tersimpan
- Check storage permission
- Verify disk space
- Check file size limit

### 3. Data Tidak Tersinkron
- Check network connection
- Verify API authentication
- Check server logs untuk error

---

**Catatan**: Fitur ini mengikuti standar project yang sudah ditetapkan:
- ✅ Menggunakan Hash ID untuk URL
- ✅ Multi-tenancy dengan global scope
- ✅ SweetAlert2 untuk notifikasi
- ✅ API-first approach untuk mobile
- ✅ Token-based authentication
- ✅ Database optimization dengan index
- ✅ Race condition protection dengan transactions