# Local Development Setup - macOS

## Setup Cepat (5 Menit)

### 1. Edit Hosts File

```bash
sudo nano /etc/hosts
```

Tambahkan baris ini di paling bawah:
```
127.0.0.1 dash.nicepatrol.test
127.0.0.1 app.nicepatrol.test
127.0.0.1 api.nicepatrol.test
```

Save: `Ctrl+O` → `Enter` → `Ctrl+X`

### 2. Flush DNS Cache

```bash
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder
```

### 3. Update .env

```bash
cp .env.example .env
nano .env
```

Update bagian domain:
```env
APP_URL=http://dash.nicepatrol.test:8000

# Domain Configuration (LOCAL)
DASHBOARD_DOMAIN=dash.nicepatrol.test
MOBILE_DOMAIN=app.nicepatrol.test
API_DOMAIN=api.nicepatrol.test

# Session
SESSION_DOMAIN=.nicepatrol.test

# Sanctum
SANCTUM_STATEFUL_DOMAINS=dash.nicepatrol.test,app.nicepatrol.test
```

### 4. Run Laravel

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### 5. Akses Mobile App

Buka browser:
```
http://app.nicepatrol.test:8000/login
```

## Done! 🎉

Sekarang kamu bisa:
- Dashboard: `http://dash.nicepatrol.test:8000`
- Mobile: `http://app.nicepatrol.test:8000/login`
- API: `http://api.nicepatrol.test:8000/v1/login`

## Troubleshooting

### Domain tidak bisa diakses?

```bash
# Cek hosts file
cat /etc/hosts | grep nicepatrol

# Flush DNS lagi
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder

# Restart browser
```

### Mau pakai Valet? (Optional - Lebih Smooth)

```bash
# Install Valet (jika belum)
composer global require laravel/valet
valet install

# Link project
cd /path/to/nicepatrolv2
valet link nicepatrol

# Akses tanpa port!
# http://dash.nicepatrol.test
# http://app.nicepatrol.test
# http://api.nicepatrol.test
```

Dengan Valet, tidak perlu `php artisan serve` dan tidak perlu port `:8000`
