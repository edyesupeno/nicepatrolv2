# Server Queue Setup Guide

## 🎯 **Pilihan Setup Berdasarkan Server Type**

### 1. **VPS/Dedicated Server dengan Supervisor (RECOMMENDED)**

#### Install Supervisor
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install supervisor

# CentOS/RHEL
sudo yum install supervisor
# atau
sudo dnf install supervisor
```

#### Setup Configuration
```bash
# Copy config file
sudo cp supervisor-queue.conf /etc/supervisor/conf.d/nicepatrol-queue.conf

# Edit path sesuai project Anda
sudo nano /etc/supervisor/conf.d/nicepatrol-queue.conf
# Ganti: /path/to/your/project dengan path sebenarnya
# Contoh: /var/www/nicepatrol

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start nicepatrol-queue:*
```

#### Monitor & Control
```bash
# Check status
sudo supervisorctl status

# Start/Stop/Restart
sudo supervisorctl start nicepatrol-queue:*
sudo supervisorctl stop nicepatrol-queue:*
sudo supervisorctl restart nicepatrol-queue:*

# View logs
sudo tail -f /var/www/nicepatrol/storage/logs/queue-worker.log
```

### 2. **Server dengan Systemd (Ubuntu 16+, CentOS 7+)**

#### Setup Service
```bash
# Copy service file
sudo cp nicepatrol-queue.service /etc/systemd/system/

# Edit path sesuai project
sudo nano /etc/systemd/system/nicepatrol-queue.service
# Ganti: /path/to/your/project dengan path sebenarnya

# Enable dan start service
sudo systemctl daemon-reload
sudo systemctl enable nicepatrol-queue
sudo systemctl start nicepatrol-queue
```

#### Monitor & Control
```bash
# Check status
sudo systemctl status nicepatrol-queue

# Start/Stop/Restart
sudo systemctl start nicepatrol-queue
sudo systemctl stop nicepatrol-queue
sudo systemctl restart nicepatrol-queue

# View logs
sudo journalctl -u nicepatrol-queue -f
```

### 3. **Shared Hosting dengan Cron Job**

#### Setup Cron
```bash
# Edit crontab
crontab -e

# Tambahkan line ini (jalankan setiap menit)
* * * * * /path/to/your/project/queue-cron.sh

# Atau jika tidak bisa script, langsung command
* * * * * cd /path/to/your/project && php artisan queue:work --stop-when-empty
```

#### Make Script Executable
```bash
chmod +x queue-cron.sh
chmod +x /path/to/your/project/queue-cron.sh
```

### 4. **Docker Environment**

#### Run Queue Container
```bash
# Single container
docker run -d --name nicepatrol-queue \
  -v $(pwd):/var/www \
  -w /var/www \
  php:8.2-cli \
  php artisan queue:work --sleep=3 --tries=3

# Atau dengan docker-compose
docker-compose -f docker-compose.queue.yml up -d
```

## 🔧 **Configuration Steps**

### 1. **Update .env untuk Queue**
```env
# Queue Configuration
QUEUE_CONNECTION=database
# atau jika pakai Redis:
# QUEUE_CONNECTION=redis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

### 2. **Create Queue Tables (jika pakai database)**
```bash
php artisan queue:table
php artisan migrate
```

### 3. **Set Proper Permissions**
```bash
# Set ownership
sudo chown -R www-data:www-data /path/to/your/project/storage
sudo chown -R www-data:www-data /path/to/your/project/bootstrap/cache

# Set permissions
sudo chmod -R 775 /path/to/your/project/storage
sudo chmod -R 775 /path/to/your/project/bootstrap/cache
```

## 🚨 **Troubleshooting**

### Check Queue Status
```bash
# Check if jobs are in queue
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

### Common Issues

#### 1. **Permission Denied**
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

#### 2. **Memory Limit**
```bash
# Edit php.ini
memory_limit = 512M
max_execution_time = 300
```

#### 3. **Database Connection**
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

## 📊 **Monitoring & Maintenance**

### 1. **Log Rotation**
```bash
# Add to logrotate
sudo nano /etc/logrotate.d/nicepatrol-queue

/var/www/nicepatrol/storage/logs/queue-worker.log {
    daily
    missingok
    rotate 7
    compress
    notifempty
    create 644 www-data www-data
    postrotate
        supervisorctl restart nicepatrol-queue:* > /dev/null 2>&1 || true
    endscript
}
```

### 2. **Health Check Script**
```bash
#!/bin/bash
# health-check.sh

QUEUE_STATUS=$(php artisan queue:monitor --once 2>/dev/null | grep -c "No jobs")

if [ "$QUEUE_STATUS" -eq 0 ]; then
    echo "Queue is processing jobs"
else
    echo "Queue is idle"
fi

# Check if worker is running
if pgrep -f "queue:work" > /dev/null; then
    echo "Queue worker is running"
else
    echo "Queue worker is NOT running"
    # Restart worker
    supervisorctl restart nicepatrol-queue:*
fi
```

### 3. **Performance Monitoring**
```bash
# Monitor queue performance
watch -n 5 'php artisan queue:monitor'

# Check system resources
htop
iostat -x 1
```

## 🎯 **Recommended Setup by Server Type**

| Server Type | Recommended Method | Pros | Cons |
|-------------|-------------------|------|------|
| **VPS/Dedicated** | Supervisor | Auto-restart, logging, multiple workers | Requires root access |
| **Cloud (AWS/GCP)** | Systemd | Native service management | Linux only |
| **Shared Hosting** | Cron Job | Works everywhere | Less reliable |
| **Docker** | Docker Compose | Containerized, scalable | Requires Docker knowledge |

## 🚀 **Quick Start Commands**

### For VPS with Supervisor:
```bash
sudo apt install supervisor
sudo cp supervisor-queue.conf /etc/supervisor/conf.d/nicepatrol-queue.conf
sudo nano /etc/supervisor/conf.d/nicepatrol-queue.conf  # Edit paths
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl start nicepatrol-queue:*
```

### For Shared Hosting:
```bash
chmod +x queue-cron.sh
crontab -e
# Add: * * * * * /path/to/project/queue-cron.sh
```

### For Docker:
```bash
docker-compose -f docker-compose.queue.yml up -d
```

## ✅ **Verification**

After setup, verify queue is working:

1. **Check worker status:**
   ```bash
   sudo supervisorctl status  # for Supervisor
   sudo systemctl status nicepatrol-queue  # for Systemd
   ```

2. **Test import:**
   - Upload small Excel file
   - Check progress bar works
   - Verify data imported correctly

3. **Monitor logs:**
   ```bash
   tail -f storage/logs/queue-worker.log
   ```

Choose the method that best fits your server environment!