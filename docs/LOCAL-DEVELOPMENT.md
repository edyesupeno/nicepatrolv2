# Local Development Setup - Nice Patrol

## Setup Domain Lokal

Untuk development lokal, kita perlu setup domain agar bisa akses:
- `dash.nicepatrol.test` → Dashboard Admin
- `app.nicepatrol.test` → Mobile App
- `api.nicepatrol.test` → API

### Opsi 1: Edit Hosts File (Recommended)

#### macOS / Linux

1. Edit hosts file:
```bash
sudo nano /etc/hosts
```

2. Tambahkan baris ini:
```
127.0.0.1 dash.nicepatrol.test
127.0.0.1 app.nicepatrol.test
127.0.0.1 api.nicepatrol.test
```

3. Save (Ctrl+O, Enter, Ctrl+X)

4. Flush DNS cache:
```bash
# macOS
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder

# Linux
sudo systemd-resolve --flush-caches
```

#### Windows

1. Buka Notepad as Administrator

2. Open file: `C:\Windows\System32\drivers\etc\hosts`

3. Tambahkan baris ini:
```
127.0.0.1 dash.nicepatrol.test
127.0.0.1 app.nicepatrol.test
127.0.0.1 api.nicepatrol.test
```

4. Save

5. Flush DNS:
```cmd
ipconfig /flushdns
```

### Opsi 2: Laravel Valet (macOS Only)

Jika pakai Valet:

```bash
# Link project
cd /path/to/nicepatrolv2
valet link nicepatrol

# Secure dengan HTTPS
valet secure nicepatrol

# Akses via:
# https://nicepatrol.test
# https://dash.nicepatrol.test
# https://app.nicepatrol.test
# https://api.nicepatrol.test
```

### Opsi 3: Laravel Herd (macOS/Windows)

Jika pakai Herd:
1. Add site di Herd
2. Otomatis bisa akses `*.test` domain

## Environment Configuration

### .env untuk Local Development

```env
APP_NAME="Nice Patrol"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://dash.nicepatrol.test

# Domain Configuration (LOCAL)
DASHBOARD_DOMAIN=dash.nicepatrol.test
MOBILE_DOMAIN=app.nicepatrol.test
API_DOMAIN=api.nicepatrol.test

# Session (shared across subdomains)
SESSION_DOMAIN=.nicepatrol.test

# Sanctum
SANCTUM_STATEFUL_DOMAINS=dash.nicepatrol.test,app.nicepatrol.test

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=patrol_db
DB_USERNAME=patrol_user
DB_PASSWORD=patrol_password
```

## Running Laravel

### Opsi 1: PHP Built-in Server (Simple)

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Akses via:
- http://dash.nicepatrol.test:8000
- http://app.nicepatrol.test:8000
- http://api.nicepatrol.test:8000

**Note:** Semua domain akan point ke port 8000

### Opsi 2: Laravel Valet/Herd (Recommended)

Otomatis handle multiple domains tanpa port.

Akses via:
- http://dash.nicepatrol.test
- http://app.nicepatrol.test
- http://api.nicepatrol.test

### Opsi 3: Docker (Advanced)

Jika pakai Docker, setup nginx untuk handle multiple domains.

## Testing Mobile App di Local

### 1. Akses Login Page

```
http://app.nicepatrol.test/login
```

atau dengan port:

```
http://app.nicepatrol.test:8000/login
```

### 2. API Endpoint

API akan otomatis point ke:

```
http://api.nicepatrol.test/v1/login
```

atau dengan port:

```
http://api.nicepatrol.test:8000/v1/login
```

### 3. Update API URL di JavaScript

Untuk local development, update `public/mobile/js/app.js`:

```javascript
// Development
const API_BASE_URL = window.location.hostname.includes('localhost') || window.location.hostname.includes('.test')
    ? 'http://api.nicepatrol.test:8000/v1'  // Local
    : 'https://api.nicepatrol.id/v1';        // Production
```

Atau buat environment detection:

```javascript
const API_BASE_URL = (() => {
    const hostname = window.location.hostname;
    
    // Local development
    if (hostname.includes('.test') || hostname === 'localhost') {
        const port = window.location.port || '8000';
        return `http://api.nicepatrol.test:${port}/v1`;
    }
    
    // Production
    return 'https://api.nicepatrol.id/v1';
})();
```

## Testing di Mobile Device (Same Network)

Jika mau test di HP yang sama network:

### 1. Cari IP Address Komputer

```bash
# macOS/Linux
ifconfig | grep "inet "

# Windows
ipconfig
```

Contoh: `192.168.1.100`

### 2. Edit Hosts di HP (Requires Root/Jailbreak)

Atau gunakan IP langsung:

```
http://192.168.1.100:8000/login
```

### 3. Update API URL untuk IP

```javascript
const API_BASE_URL = 'http://192.168.1.100:8000/v1';
```

## CORS Configuration untuk Local

Update `config/cors.php`:

```php
return [
    'paths' => ['api/*'],
    'allowed_origins' => [
        'http://app.nicepatrol.test',
        'http://app.nicepatrol.test:8000',
        'http://localhost:8000',
        'http://127.0.0.1:8000',
    ],
    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

## Quick Start Commands

```bash
# 1. Setup hosts file (pilih salah satu cara di atas)

# 2. Copy .env
cp .env.example .env

# 3. Update .env dengan domain .test

# 4. Generate key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Run server
php artisan serve --host=0.0.0.0 --port=8000

# 7. Akses mobile app
# http://app.nicepatrol.test:8000/login
```

## Troubleshooting

### Domain tidak bisa diakses

1. Cek hosts file sudah benar
2. Flush DNS cache
3. Restart browser
4. Cek Laravel server running

### API CORS Error

1. Cek `config/cors.php` sudah include domain local
2. Clear config cache: `php artisan config:clear`
3. Cek browser console untuk detail error

### Token tidak tersimpan

1. Cek browser localStorage (F12 → Application → Local Storage)
2. Cek API response di Network tab
3. Pastikan HTTPS di production, HTTP di local

## Production vs Local

| Feature | Local | Production |
|---------|-------|------------|
| Domain | `.test` | `.id` |
| Protocol | HTTP | HTTPS |
| Port | `:8000` | `:443` (default) |
| SSL | No | Yes (Let's Encrypt) |
| Session Domain | `.nicepatrol.test` | `.nicepatrol.id` |

## Recommended Setup

Untuk development yang smooth:

1. ✅ **Gunakan Laravel Valet/Herd** (macOS/Windows)
   - Auto domain setup
   - No port needed
   - HTTPS support

2. ✅ **Edit hosts file** (All OS)
   - Simple
   - Works everywhere
   - Need port `:8000`

3. ❌ **Jangan pakai localhost** untuk multi-domain
   - Session sharing tidak work
   - CORS issues
   - Cookie issues

## Next Steps

Setelah setup local:

1. Test login di `http://app.nicepatrol.test:8000/login`
2. Check API di `http://api.nicepatrol.test:8000/v1/user`
3. Develop mobile features
4. Deploy ke production dengan domain `.id`
