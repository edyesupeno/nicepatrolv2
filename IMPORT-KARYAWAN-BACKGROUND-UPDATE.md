# Import Karyawan Background Processing Update

## 🚀 Fitur Baru

### 1. **No Badge** (Ganti dari NIK Karyawan)
- ✅ Header Excel berubah dari "NIK Karyawan" menjadi "No Badge"
- ✅ Backward compatibility: Masih support file lama dengan "NIK Karyawan"
- ✅ Template Excel sudah diupdate dengan header baru
- ✅ Validasi error message sudah diupdate

### 2. **Auto-assign Area Karyawan**
- ✅ Semua area di project otomatis di-assign ke karyawan baru
- ✅ Area pertama dijadikan primary area
- ✅ Insert ke tabel `karyawan_areas` dengan `is_primary` flag
- ✅ Konsisten dengan sistem area access yang ada

### 3. **Background Processing dengan Progress Bar**
- ✅ Import berjalan di background menggunakan Laravel Queue
- ✅ Real-time progress bar dengan polling setiap 2 detik
- ✅ Menampilkan statistik: berhasil, di-skip, total
- ✅ Menampilkan error terbaru (5 error terakhir)
- ✅ File size limit dinaikkan dari 2MB ke 10MB

## 📁 File yang Diubah

### Backend
1. **`app/Jobs/ImportKaryawanJob.php`** - Job baru untuk background processing
2. **`app/Http/Controllers/Perusahaan/KaryawanController.php`** - Update method import
3. **`app/Imports/KaryawanImport.php`** - Update untuk auto-assign area
4. **`app/Exports/KaryawanTemplateExport.php`** - Update header "No Badge"
5. **`routes/web.php`** - Tambah route untuk progress polling

### Frontend
6. **`resources/views/perusahaan/karyawans/index.blade.php`** - Progress bar UI
7. **`resources/views/layouts/app.blade.php`** - Tambah CSRF token meta

### Scripts
8. **`start-queue.sh`** - Script untuk menjalankan queue worker

## 🔧 Setup Queue Worker

Untuk menjalankan background processing, queue worker harus aktif:

```bash
# Jalankan queue worker
./start-queue.sh

# Atau manual
php artisan queue:work --queue=default --sleep=3 --tries=3
```

## 📊 Flow Import Baru

### 1. **Step 1: Pilih Project & Role**
- User pilih project dan role untuk semua karyawan
- Validasi project dan role wajib

### 2. **Step 2: Download Template & Upload**
- Download template Excel dengan header "No Badge"
- Upload file Excel (max 10MB)
- Validasi file format

### 3. **Step 3: Progress Monitoring**
- Form submit via AJAX ke background job
- Progress bar real-time dengan polling
- Statistik import: berhasil, di-skip, error
- Tombol "Selesai" atau "Import Lagi"

## 🎯 Fitur Progress Bar

### Real-time Updates
```javascript
// Polling setiap 2 detik
setInterval(checkProgress, 2000);

// Update progress bar, stats, dan errors
updateProgress(progressData);
```

### Progress Data Structure
```json
{
    "percentage": 75,
    "message": "Memproses baris 150...",
    "success_count": 120,
    "skipped_count": 5,
    "errors": ["Baris 10: Email sudah digunakan", "..."],
    "completed": false,
    "timestamp": "2024-01-20T10:30:00Z"
}
```

## 🔄 Auto-assign Area Logic

```php
// Get all areas in project
$areas = Area::where('project_id', $project->id)->get();

// Assign to karyawan
foreach ($areas as $index => $area) {
    DB::table('karyawan_areas')->insertOrIgnore([
        'karyawan_id' => $karyawan->id,
        'area_id' => $area->id,
        'is_primary' => $index === 0, // First area = primary
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
```

## 🔒 Security & Validation

### Multi-tenancy Protection
- ✅ Semua query ter-filter berdasarkan `perusahaan_id`
- ✅ Auto-assign `perusahaan_id` saat create karyawan
- ✅ Validasi project ownership
- ✅ Area assignment hanya untuk area di perusahaan yang sama

### Data Validation
- ✅ No Badge harus unique per perusahaan
- ✅ Email harus unique global
- ✅ Project dan Jabatan harus exist di perusahaan
- ✅ Format tanggal dan data type validation

## 📈 Performance Improvements

### Background Processing
- ✅ Import tidak block UI
- ✅ Bisa handle file besar (10MB)
- ✅ Progress tracking untuk user experience
- ✅ Error handling yang robust

### Database Optimization
- ✅ Batch insert untuk area assignment
- ✅ Transaction untuk data consistency
- ✅ `insertOrIgnore` untuk avoid duplicate errors

## 🧪 Testing

### Manual Testing
1. **Template Download**: Pastikan header "No Badge"
2. **Small File**: Test dengan 10-50 karyawan
3. **Large File**: Test dengan 500+ karyawan
4. **Error Handling**: Test dengan data invalid
5. **Progress Bar**: Pastikan update real-time
6. **Area Assignment**: Cek karyawan_areas table

### Queue Worker Testing
```bash
# Test queue worker
php artisan queue:work --once

# Monitor queue jobs
php artisan queue:monitor
```

## 🚨 Troubleshooting

### Queue Worker Tidak Jalan
```bash
# Cek queue connection
php artisan queue:table
php artisan migrate

# Restart queue worker
php artisan queue:restart
```

### Progress Tidak Update
- Pastikan CSRF token ada di meta tag
- Cek network tab untuk AJAX errors
- Pastikan route progress accessible

### Import Gagal
- Cek log Laravel: `storage/logs/laravel.log`
- Cek queue failed jobs: `php artisan queue:failed`
- Retry failed jobs: `php artisan queue:retry all`

## 📋 Checklist Deployment

- [ ] Queue worker running di production
- [ ] CSRF token meta tag ada
- [ ] File upload limit 10MB di server
- [ ] Database migration untuk karyawan_areas
- [ ] Template Excel dengan header "No Badge"
- [ ] Test import dengan file besar
- [ ] Monitor queue performance

## 🎉 Benefits

1. **User Experience**: Progress bar, tidak block UI
2. **Scalability**: Bisa handle file besar
3. **Automation**: Auto-assign area berdasarkan project
4. **Consistency**: Header "No Badge" lebih jelas
5. **Reliability**: Background processing dengan retry mechanism
6. **Monitoring**: Real-time progress dan error tracking

## 🔮 Future Enhancements

1. **Email Notification**: Kirim email saat import selesai
2. **Import History**: Log semua import activity
3. **Bulk Area Assignment**: Pilih area spesifik saat import
4. **Excel Validation**: Pre-validate file sebelum import
5. **Import Templates**: Multiple template untuk role berbeda