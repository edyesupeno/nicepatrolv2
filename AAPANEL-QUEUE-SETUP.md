# aaPanel Queue Setup - Import Karyawan Background

## 🎯 **Setup Queue Worker di aaPanel**

### **Step 1: Login ke aaPanel**
1. Buka aaPanel dashboard
2. Login dengan akun admin

### **Step 2: Setup Cron Job**
1. **Masuk ke menu "Cron"**
   - Klik **"Cron"** di sidebar kiri
   - Atau buka **"Task Manager" → "Cron"**

2. **Tambah Cron Job Baru**
   - Klik **"Add Cron"** atau **"Tambah Tugas"**
   - Pilih **"Shell Script"** atau **"Custom"**

3. **Konfigurasi Cron Job:**
   ```
   Name/Nama: Laravel Queue Worker - Nice Patrol
   Type: Shell Script
   Script Path: /www/wwwroot/nicepatrol/queue-worker.sh
   Execution Cycle: Every minute (*/1 * * * *)
   ```

### **Step 3: Buat Script Queue Worker**

**Buat file:** `/www/wwwroot/nicepatrol/queue-worker.sh`

```bash
#!/bin/bash

# Laravel Queue Worker untuk aaPanel
# File: /www/wwwroot/nicepatrol/queue-worker.sh

PROJECT_PATH="/www/wwwroot/nicepatrol"
LOCK_FILE="$PROJECT_PATH/storage/queue-worker.lock"
LOG_FILE="$PROJECT_PATH/storage/logs/queue-worker.log"
PHP_PATH="/www/server/php/82/bin/php"  # Sesuaikan dengan versi PHP

# Function to log with timestamp
log_message() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG_FILE"
}

# Check if queue worker is running
if [ -f "$LOCK_FILE" ]; then
    PID=$(cat "$LOCK_FILE")
    if ps -p $PID > /dev/null 2>&1; then
        # Worker is running, check if it's been running too long (max 1 hour)
        START_TIME=$(stat -c %Y "$LOCK_FILE" 2>/dev/null || stat -f %m "$LOCK_FILE" 2>/dev/null)
        CURRENT_TIME=$(date +%s)
        RUNNING_TIME=$((CURRENT_TIME - START_TIME))
        
        if [ $RUNNING_TIME -gt 3600 ]; then
            log_message "Queue worker running too long (${RUNNING_TIME}s), restarting..."
            kill $PID 2>/dev/null
            rm -f "$LOCK_FILE"
        else
            log_message "Queue worker is running (PID: $PID, ${RUNNING_TIME}s)"
            exit 0
        fi
    else
        log_message "Queue worker PID file exists but process is dead, removing lock file"
        rm -f "$LOCK_FILE"
    fi
fi

# Change to project directory
cd "$PROJECT_PATH" || {
    log_message "ERROR: Cannot change to project directory: $PROJECT_PATH"
    exit 1
}

# Check if there are jobs to process
JOB_COUNT=$($PHP_PATH artisan tinker --execute="echo DB::table('jobs')->count();" 2>/dev/null | tail -1)

if [ "$JOB_COUNT" = "0" ]; then
    log_message "No jobs in queue, skipping worker start"
    exit 0
fi

# Start queue worker
log_message "Starting queue worker... (Jobs in queue: $JOB_COUNT)"

# Start queue worker in background and save PID
nohup $PHP_PATH artisan queue:work --sleep=3 --tries=3 --max-time=3600 --timeout=300 --stop-when-empty > "$PROJECT_PATH/storage/logs/queue-output.log" 2>&1 &
WORKER_PID=$!

# Save PID to lock file
echo $WORKER_PID > "$LOCK_FILE"
log_message "Queue worker started with PID: $WORKER_PID"

# Wait a moment to check if worker started successfully
sleep 2
if ps -p $WORKER_PID > /dev/null 2>&1; then
    log_message "Queue worker confirmed running (PID: $WORKER_PID)"
else
    log_message "ERROR: Queue worker failed to start"
    rm -f "$LOCK_FILE"
    exit 1
fi
```

### **Step 4: Set Permissions**

**Via aaPanel File Manager:**
1. Buka **"Files"** di aaPanel
2. Navigate ke `/www/wwwroot/nicepatrol/`
3. Klik kanan pada `queue-worker.sh`
4. Pilih **"Permissions"** atau **"Chmod"**
5. Set permission ke **755** atau **rwxr-xr-x**

**Via Terminal (jika ada akses SSH):**
```bash
chmod +x /www/wwwroot/nicepatrol/queue-worker.sh
chown www:www /www/wwwroot/nicepatrol/queue-worker.sh
```

### **Step 5: Setup Log Directory**

**Pastikan folder log ada dan writable:**
```bash
mkdir -p /www/wwwroot/nicepatrol/storage/logs
chmod 755 /www/wwwroot/nicepatrol/storage/logs
chown -R www:www /www/wwwroot/nicepatrol/storage
```

### **Step 6: Test Cron Job**

1. **Manual Test:**
   - Di aaPanel, buka **"Cron"**
   - Cari cron job yang sudah dibuat
   - Klik **"Run Now"** atau **"Execute"**
   - Check log: `/www/wwwroot/nicepatrol/storage/logs/queue-worker.log`

2. **Check Log:**
   ```bash
   tail -f /www/wwwroot/nicepatrol/storage/logs/queue-worker.log
   ```

## 🔧 **Alternative: Simple Cron (Jika Script Tidak Bisa)**

Jika script di atas tidak bisa dijalankan, gunakan cron sederhana:

**Cron Job Configuration:**
```
Name: Laravel Queue Simple
Type: Shell Script
Command: cd /www/wwwroot/nicepatrol && /www/server/php/82/bin/php artisan queue:work --stop-when-empty
Execution Cycle: Every minute (*/1 * * * *)
```

## 📊 **Monitoring & Troubleshooting**

### **1. Check Queue Status**
```bash
# Via SSH atau Terminal aaPanel
cd /www/wwwroot/nicepatrol
php artisan queue:monitor
php artisan queue:failed
```

### **2. View Logs**
```bash
# Queue worker log
tail -f storage/logs/queue-worker.log

# Laravel application log
tail -f storage/logs/laravel.log

# Queue output log
tail -f storage/logs/queue-output.log
```

### **3. Manual Queue Processing**
```bash
# Process all pending jobs once
cd /www/wwwroot/nicepatrol
php artisan queue:work --stop-when-empty

# Process single job (for debugging)
php artisan queue:work --once
```

### **4. Clear Failed Jobs**
```bash
# Retry all failed jobs
php artisan queue:retry all

# Clear all failed jobs
php artisan queue:flush
```

## 🚨 **Common Issues & Solutions**

### **Issue 1: Permission Denied**
```bash
# Fix permissions
chmod +x /www/wwwroot/nicepatrol/queue-worker.sh
chown www:www /www/wwwroot/nicepatrol/queue-worker.sh
chmod -R 755 /www/wwwroot/nicepatrol/storage
```

### **Issue 2: PHP Path Not Found**
```bash
# Find PHP path
which php
# atau
find /www/server -name "php" -type f 2>/dev/null

# Update script dengan path yang benar
# Contoh: /www/server/php/82/bin/php
```

### **Issue 3: Database Connection Error**
```bash
# Check .env file
cat /www/wwwroot/nicepatrol/.env | grep DB_

# Test database connection
cd /www/wwwroot/nicepatrol
php artisan tinker --execute="DB::connection()->getPdo();"
```

### **Issue 4: Memory Limit**
```bash
# Check PHP memory limit
php -i | grep memory_limit

# Increase in php.ini or .env
# PHP_MEMORY_LIMIT=512M
```

## 📋 **Verification Checklist**

Setelah setup, pastikan:

- [ ] Cron job terdaftar dan aktif di aaPanel
- [ ] Script `queue-worker.sh` executable (permission 755)
- [ ] Log file `storage/logs/queue-worker.log` terbuat
- [ ] Test import karyawan berfungsi dengan progress bar
- [ ] Tidak ada error di log Laravel
- [ ] Queue worker restart otomatis jika crash

## 🎯 **Testing Import**

1. **Login ke sistem Nice Patrol**
2. **Menu Karyawan → Import Karyawan**
3. **Upload file Excel dengan header "No Badge"**
4. **Lihat progress bar real-time**
5. **Check log untuk memastikan job diproses:**
   ```bash
   tail -f /www/wwwroot/nicepatrol/storage/logs/queue-worker.log
   ```

## 📱 **Monitoring Dashboard**

**Buat file monitoring sederhana:** `/www/wwwroot/nicepatrol/public/queue-status.php`

```php
<?php
// Simple queue monitoring
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$jobCount = DB::table('jobs')->count();
$failedCount = DB::table('failed_jobs')->count();
$isWorkerRunning = file_exists(storage_path('queue-worker.lock'));

echo "<h2>Queue Status</h2>";
echo "<p>Jobs in queue: <strong>$jobCount</strong></p>";
echo "<p>Failed jobs: <strong>$failedCount</strong></p>";
echo "<p>Worker status: <strong>" . ($isWorkerRunning ? 'Running' : 'Stopped') . "</strong></p>";
echo "<p>Last check: " . date('Y-m-d H:i:s') . "</p>";

if ($jobCount > 0) {
    echo "<h3>Recent Jobs:</h3>";
    $jobs = DB::table('jobs')->orderBy('created_at', 'desc')->limit(5)->get();
    foreach ($jobs as $job) {
        $payload = json_decode($job->payload, true);
        echo "<p>- " . $payload['displayName'] . " (attempts: {$job->attempts})</p>";
    }
}
?>
```

**Akses via:** `https://yourdomain.com/queue-status.php`

## 🎉 **Success Indicators**

Jika setup berhasil, Anda akan melihat:

1. ✅ **Cron job berjalan setiap menit**
2. ✅ **Log queue-worker.log terupdate**
3. ✅ **Import karyawan dengan progress bar**
4. ✅ **Area otomatis ter-assign ke karyawan**
5. ✅ **Tidak ada failed jobs**

**Setup selesai! Import karyawan background processing siap digunakan di production.** 🚀