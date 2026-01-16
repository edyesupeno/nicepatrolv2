# Testing Mobile App dari HP

## Setup untuk Akses dari HP (Same Network)

### 1. IP Address Mac
```
192.168.1.18
```

### 2. Akses dari HP

Buka browser di HP dan akses:
```
http://192.168.1.18:8000/login
```

**Note:** Tidak perlu pakai domain `.test` karena HP tidak punya hosts file yang sama.

### 3. API Endpoint

API akan otomatis detect dan pakai:
```
http://192.168.1.18:8000/api/v1
```

JavaScript di `app.js` sudah auto-detect environment, jadi akan work otomatis.

### 4. Test Login

1. Buka `http://192.168.1.18:8000/login` di HP
2. Login dengan user security_officer
3. Seharusnya redirect ke dashboard

## Troubleshooting

### HP tidak bisa akses?

1. **Cek firewall Mac:**
```bash
# Disable firewall sementara untuk testing
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate off

# Enable lagi setelah testing
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate on
```

2. **Pastikan server running:**
```bash
# Cek process
ps aux | grep "php artisan serve"
```

3. **Pastikan server listen di 0.0.0.0:**
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### CORS Error?

Sudah di-handle di `config/cors.php` dengan `allowed_origins => ['*']`

### API 404?

Pastikan akses dengan IP yang sama:
- ✅ `http://192.168.1.18:8000/api/v1/login`
- ❌ `http://app.nicepatrol.test:8000/api/v1/login` (tidak work di HP)

## Install PWA di HP

Setelah buka di browser HP:

### Android (Chrome)
1. Buka `http://192.168.1.18:8000/login`
2. Tap menu (3 dots)
3. Tap "Add to Home screen"
4. App akan muncul di home screen

### iOS (Safari)
1. Buka `http://192.168.1.18:8000/login`
2. Tap Share button
3. Tap "Add to Home Screen"
4. App akan muncul di home screen

## Production Setup

Di production, ganti IP dengan domain:
```
https://app.nicepatrol.id/login
```

API akan otomatis pakai:
```
https://api.nicepatrol.id/v1
```

## Current Server Info

- **Mac IP:** 192.168.1.18
- **Port:** 8000
- **Mobile URL:** http://192.168.1.18:8000/login
- **API URL:** http://192.168.1.18:8000/api/v1
- **Dashboard URL:** http://192.168.1.18:8000/login (untuk admin)

## Tips

1. **Bookmark di HP** untuk akses cepat
2. **Install as PWA** untuk experience seperti native app
3. **Test offline mode** setelah install PWA
4. **Test geolocation** untuk fitur check-in/patroli
5. **Test camera** untuk foto checkpoint
