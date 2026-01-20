# Local Testing Guide - Import Karyawan Background

## 🧪 **Testing di Local (Development)**

### **Method 1: Script Otomatis (Recommended)**
```bash
# Jalankan queue worker untuk testing
./test-queue-local.sh
```

### **Method 2: Manual Command**
```bash
# Terminal 1: Jalankan Laravel server
php artisan serve

# Terminal 2: Jalankan queue worker
php artisan queue:work --sleep=1 --tries=3 --timeout=60
```

### **Method 3: One-time Processing (untuk debug)**
```bash
# Process hanya 1 job lalu stop (bagus untuk debugging)
php artisan queue:work --once

# Atau process semua job yang ada lalu stop
php artisan queue:work --stop-when-empty
```

## 📋 **Step-by-Step Testing**

### **1. Setup Database Queue (Sekali saja)**
```bash
# Buat tabel queue jika belum ada
php artisan queue:table
php artisan migrate
```

### **2. Pastikan .env Correct**
```env
# Di file .env
QUEUE_CONNECTION=database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nicepatrol
DB_USERNAME=root
DB_PASSWORD=
```

### **3. Start Testing Environment**

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
# Buka: http://localhost:8000
```

**Terminal 2 - Queue Worker:**
```bash
./test-queue-local.sh
# Atau manual: php artisan queue:work
```

### **4. Test Import Process**

1. **Login ke sistem**
2. **Buka menu Karyawan**
3. **Klik "Import Karyawan"**
4. **Pilih Project & Role**
5. **Download template (header "No Badge")**
6. **Isi data test (5-10 karyawan)**
7. **Upload file**
8. **Lihat progress bar real-time**

### **5. Monitor & Debug**

**Check Queue Status:**
```bash
# Lihat job yang pending
php artisan queue:monitor

# Lihat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

**Check Logs:**
```bash
# Laravel log
tail -f storage/logs/laravel.log

# Queue worker output (jika pakai script)
# Akan muncul di terminal yang jalankan queue worker
```

## 🎯 **Sample Test Data**

Buat file Excel dengan data ini untuk testing:

| No Badge | Nama Lengkap | Email | No. Telepon | Project | Jabatan | Status Karyawan | Jenis Kelamin | Status Perkawinan | Jumlah Tanggungan | Tanggal Lahir | Tempat Lahir | Tanggal Masuk | Habis Kontrak | Status |
|----------|--------------|-------|-------------|---------|---------|-----------------|---------------|-------------------|-------------------|---------------|--------------|---------------|---------------|--------|
| TEST001 | John Doe | john@test.com | 081234567890 | Office Pekanbaru | Security Officer | Kontrak | Laki-laki | TK | 0 | 1990-01-15 | Jakarta | 2024-01-01 | 2024-12-31 | Aktif |
| TEST002 | Jane Smith | jane@test.com | 081234567891 | Office Pekanbaru | Security Officer | Tetap | Perempuan | K | 2 | 1985-05-20 | Bandung | 2024-01-01 |  | Aktif |

## 🔍 **Debugging Tips**

### **1. Check Job Queue Table**
```sql
-- Lihat job yang pending
SELECT * FROM jobs ORDER BY created_at DESC LIMIT 10;

-- Lihat failed jobs
SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 10;
```

### **2. Test Progress Polling**
```bash
# Test endpoint progress manual
curl "http://localhost:8000/perusahaan/karyawans/import-progress?job_id=test123"
```

### **3. Force Process Job**
```bash
# Process job tertentu
php artisan queue:work --once --queue=default

# Clear semua job (jika stuck)
php artisan queue:flush
```

### **4. Check File Upload**
```bash
# Pastikan folder temp ada
ls -la storage/app/temp/

# Check permissions
chmod 755 storage/app/temp/
```

## ⚠️ **Common Issues & Solutions**

### **Issue 1: Queue Worker Tidak Jalan**
```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check queue table exists
php artisan queue:table
php artisan migrate
```

### **Issue 2: Progress Tidak Update**
```bash
# Check CSRF token di browser console
# Check network tab untuk AJAX errors
# Pastikan route accessible: php artisan route:list | grep import-progress
```

### **Issue 3: File Upload Error**
```bash
# Check upload limits
php -i | grep upload_max_filesize
php -i | grep post_max_size

# Create temp directory
mkdir -p storage/app/temp
chmod 755 storage/app/temp
```

### **Issue 4: Memory/Timeout Error**
```bash
# Increase limits untuk testing
php -d memory_limit=512M -d max_execution_time=300 artisan queue:work
```

## 🎉 **Expected Results**

Setelah testing berhasil, Anda akan lihat:

1. ✅ **Progress bar** bergerak dari 0% ke 100%
2. ✅ **Stats update** real-time (berhasil/di-skip)
3. ✅ **Karyawan baru** muncul di list
4. ✅ **Area otomatis** ter-assign ke karyawan
5. ✅ **No error** di console/logs

## 🚀 **Ready for Production**

Jika testing local berhasil, siap deploy ke server dengan cronjob:

```bash
# Cronjob untuk aaPanel (setiap menit)
* * * * * cd /www/wwwroot/nicepatrol && php artisan queue:work --stop-when-empty
```

## 📱 **Quick Test Commands**

```bash
# Start everything
./test-queue-local.sh &
php artisan serve

# Stop queue worker
pkill -f "queue:work"

# Reset testing
php artisan queue:flush
php artisan cache:clear
```

Happy testing! 🎯