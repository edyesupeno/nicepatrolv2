# Panduan Update Production - Nice Patrol System

## 📋 Checklist Sebelum Update

- [ ] Pastikan tidak ada user yang sedang menggunakan sistem
- [ ] Informasikan maintenance kepada user
- [ ] Pastikan koneksi SSH stabil
- [ ] Pastikan space disk cukup untuk backup

## 🚀 Langkah-Langkah Update Production

### 1. Masuk ke Server & Folder Project

```bash
# SSH ke server
ssh user@your-server-ip

# Masuk ke folder project
cd /www/wwwroot/dash.nicepatrol.id
```

### 2. Backup Database

```bash
# Buat folder backup jika belum ada
mkdir -p /www/wwwroot/BACKUP\ LIVE\ SYSTEM/NICEPATROL/db

# Backup database dengan timestamp
php artisan db:backup --path="/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/db/backup-$(date +%Y%m%d-%H%M%S).sql"

# Atau manual dengan mysqldump
mysqldump -u username -p database_name > "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/db/backup-$(date +%Y%m%d-%H%M%S).sql"
```

**Contoh:**
```bash
mysqldump -u root -p patrol_db > "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/db/backup-$(date +%Y%m%d-%H%M%S).sql"
```

### 3. Backup Files (Storage & Public)

```bash
# Buat folder backup files jika belum ada
mkdir -p /www/wwwroot/BACKUP\ LIVE\ SYSTEM/NICEPATROL/files

# Backup storage folder
tar -czf "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/files/storage-$(date +%Y%m%d-%H%M%S).tar.gz" storage/

# Backup public uploads (jika ada)
tar -czf "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/files/public-$(date +%Y%m%d-%H%M%S).tar.gz" public/storage/
```

### 4. Backup .env File

```bash
# Buat folder backup env jika belum ada
mkdir -p /www/wwwroot/BACKUP\ LIVE\ SYSTEM/NICEPATROL/env

# Backup .env file
cp .env "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/.env-$(date +%Y%m%d-%H%M%S)"
```

### 5. Set Maintenance Mode

```bash
# Aktifkan maintenance mode
php artisan down --message="System sedang dalam maintenance. Mohon tunggu beberapa menit." --retry=60
```

### 6. Handle Local Changes & Pull Latest Code

```bash
# Cek status perubahan lokal
git status

# Jika ada perubahan lokal, backup dulu
mkdir -p "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/local-changes-$(date +%Y%m%d-%H%M%S)"

# Copy files yang berubah (sesuaikan dengan output git status)
# Contoh:
# cp app/Models/User.php "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/local-changes-$(date +%Y%m%d-%H%M%S)/"

# Stash perubahan lokal
git stash

# Pull latest code
git pull origin master

# Jika perlu apply kembali perubahan lokal
# git stash pop
```

### 7. Update Dependencies

```bash
# Update Composer dependencies
composer install --no-dev --optimize-autoloader

# Update NPM dependencies (jika ada perubahan frontend)
npm install
npm run build
```

### 8. Set Permissions (Chown ke www)

```bash
# Set ownership ke user www
chown -R www:www /www/wwwroot/dash.nicepatrol.id

# Set permissions untuk storage dan cache
chmod -R 775 storage bootstrap/cache
```

**Atau jika menggunakan user berbeda:**
```bash
# Ganti 'www' dengan user web server Anda (nginx/apache)
chown -R www-data:www-data /www/wwwroot/dash.nicepatrol.id
```

### 9. Clear All Cache

```bash
# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# Optimize untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Run Database Migration

```bash
# PENTING: Gunakan migrate (BUKAN migrate:fresh atau migrate:refresh)
# Ini akan menjalankan migration baru tanpa menghapus data

php artisan migrate --force

# Jika ada seeder yang perlu dijalankan (optional)
# php artisan db:seed --class=SpecificSeederClass
```

**⚠️ PERINGATAN:**
- **JANGAN** gunakan `php artisan migrate:fresh` (akan hapus semua data!)
- **JANGAN** gunakan `php artisan migrate:refresh` (akan hapus semua data!)
- **GUNAKAN** `php artisan migrate` saja (aman, hanya tambah tabel/kolom baru)

### 11. Verify Migration

```bash
# Cek status migration
php artisan migrate:status

# Cek apakah ada error
tail -f storage/logs/laravel.log
```

### 12. Disable Maintenance Mode

```bash
# Nonaktifkan maintenance mode
php artisan up
```

### 13. Test Aplikasi

```bash
# Test apakah aplikasi berjalan normal
curl -I https://your-domain.com

# Atau buka di browser dan test:
# - Login
# - Create data
# - View data
# - Update data
```

## 🔄 Script Otomatis (Optional)

Buat file `update-production.sh` untuk otomasi:

```bash
#!/bin/bash

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

PROJECT_PATH="/www/wwwroot/dash.nicepatrol.id"
BACKUP_PATH="/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL"

echo -e "${GREEN}=== Nice Patrol Production Update ===${NC}"
echo -e "${GREEN}Project: $PROJECT_PATH${NC}"
echo ""

# Masuk ke folder project
cd $PROJECT_PATH

# 1. Backup Database
echo -e "${YELLOW}[1/12] Backing up database...${NC}"
mkdir -p "$BACKUP_PATH/db"
php artisan db:backup --path="$BACKUP_PATH/db/backup-$(date +%Y%m%d-%H%M%S).sql"

# 2. Backup Files
echo -e "${YELLOW}[2/12] Backing up files...${NC}"
mkdir -p "$BACKUP_PATH/files"
tar -czf "$BACKUP_PATH/files/storage-$(date +%Y%m%d-%H%M%S).tar.gz" storage/

# 3. Backup .env
echo -e "${YELLOW}[3/12] Backing up .env...${NC}"
mkdir -p "$BACKUP_PATH/env"
cp .env "$BACKUP_PATH/env/.env-$(date +%Y%m%d-%H%M%S)"

# 4. Backup Local Changes
echo -e "${YELLOW}[4/12] Checking local changes...${NC}"
if [[ -n $(git status -s) ]]; then
    echo -e "${YELLOW}Local changes detected, backing up...${NC}"
    mkdir -p "$BACKUP_PATH/local-changes-$(date +%Y%m%d-%H%M%S)"
    git diff > "$BACKUP_PATH/local-changes-$(date +%Y%m%d-%H%M%S)/changes.diff"
fi

# 5. Maintenance Mode
echo -e "${YELLOW}[5/12] Enabling maintenance mode...${NC}"
php artisan down --message="System update in progress" --retry=60

# 6. Pull Code
echo -e "${YELLOW}[6/12] Pulling latest code...${NC}"
git stash
git pull origin master

# 7. Update Dependencies
echo -e "${YELLOW}[7/12] Updating dependencies...${NC}"
sudo composer install --no-dev --optimize-autoloader

# 8. Set Permissions
echo -e "${YELLOW}[8/12] Setting permissions...${NC}"
chown -R www:www $PROJECT_PATH
chmod -R 775 storage bootstrap/cache

# 9. Clear Cache
echo -e "${YELLOW}[9/12] Clearing cache...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled

# 10. Run Migration
echo -e "${YELLOW}[10/12] Running migrations...${NC}"
php artisan migrate --force

# 11. Optimize
echo -e "${YELLOW}[11/12] Optimizing...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 12. Disable Maintenance
echo -e "${YELLOW}[12/12] Disabling maintenance mode...${NC}"
php artisan up

echo ""
echo -e "${GREEN}=== Update Complete! ===${NC}"
echo -e "${GREEN}Please test the application at: https://dash.nicepatrol.id${NC}"
```

**Cara menggunakan script:**
```bash
# Buat file di folder project
cd /www/wwwroot/dash.nicepatrol.id
nano update-production.sh

# Paste script di atas, lalu save (Ctrl+X, Y, Enter)

# Buat file executable
chmod +x update-production.sh

# Jalankan script
./update-production.sh
```

## 🔙 Rollback (Jika Ada Masalah)

### Rollback Database

```bash
# Restore database dari backup terakhir
mysql -u username -p database_name < "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/db/backup-YYYYMMDD-HHMMSS.sql"
```

### Rollback Code

```bash
# Masuk ke folder project
cd /www/wwwroot/dash.nicepatrol.id

# Kembali ke commit sebelumnya
git log --oneline  # Lihat history commit
git reset --hard COMMIT_HASH  # Ganti COMMIT_HASH dengan hash commit sebelumnya

# Atau rollback 1 commit
git reset --hard HEAD~1

# Clear cache setelah rollback
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Rollback Files

```bash
# Restore storage dari backup
tar -xzf "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/files/storage-YYYYMMDD-HHMMSS.tar.gz"
```

## 📝 Checklist Setelah Update

- [ ] Test login dengan berbagai role
- [ ] Test create/read/update/delete data
- [ ] Test upload file
- [ ] Test import Excel
- [ ] Cek error log: `tail -f storage/logs/laravel.log`
- [ ] Cek web server log: `tail -f /var/log/nginx/error.log`
- [ ] Monitor performa aplikasi
- [ ] Informasikan user bahwa maintenance selesai

## ⚠️ Troubleshooting

### Error: Permission Denied

```bash
# Fix permissions
chown -R www:www /www/wwwroot/dash.nicepatrol.id
chmod -R 775 storage bootstrap/cache
```

### Error: Class Not Found

```bash
# Clear dan rebuild autoload
composer dump-autoload
php artisan clear-compiled
php artisan cache:clear
```

### Error: Migration Failed

```bash
# Rollback 1 migration
php artisan migrate:rollback --step=1

# Cek status
php artisan migrate:status

# Coba lagi
php artisan migrate --force
```

### Error: 500 Internal Server Error

```bash
# Masuk ke folder project
cd /www/wwwroot/dash.nicepatrol.id

# Cek error log
tail -f storage/logs/laravel.log

# Cek web server log
tail -f /var/log/nginx/error.log

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Fix permissions
chown -R www:www /www/wwwroot/dash.nicepatrol.id
chmod -R 775 storage bootstrap/cache
```

## 📞 Kontak Support

Jika ada masalah saat update, hubungi:
- Developer: Edy Macem (edy@phylot.co.id)
- Project Path: `/www/wwwroot/dash.nicepatrol.id`
- Backup Path: `/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/`

## 📚 Referensi

- [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
- [Git Documentation](https://git-scm.com/doc)
- [MySQL Backup & Restore](https://dev.mysql.com/doc/refman/8.0/en/backup-and-recovery.html)

## 🚨 Quick Fix untuk Git Pull Error

Jika muncul error "Your local changes would be overwritten by merge":

```bash
# Masuk ke folder project
cd /www/wwwroot/dash.nicepatrol.id

# Backup perubahan lokal
mkdir -p "/www/wwwroot/BACKUP LIVE SYSTEM/NICEPATROL/local-changes-$(date +%Y%m%d-%H%M%S)"

# Lihat file apa saja yang berubah
git status

# Stash perubahan lokal
git stash

# Pull code terbaru
git pull origin master

# Lanjutkan dengan clear cache dan migrate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan migrate --force

# Set permissions
chown -R www:www /www/wwwroot/dash.nicepatrol.id
chmod -R 775 storage bootstrap/cache

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

**Last Updated:** 2026-01-17
**Version:** 1.1
**Project Path:** `/www/wwwroot/dash.nicepatrol.id`
