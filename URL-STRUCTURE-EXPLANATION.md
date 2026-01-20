# URL Structure Explanation - Nice Patrol API

## ❓ Pertanyaan User
> "http://localhost:8000/api/v1 kalau di local kan ada /api/v1 apakah di server prod nanti tetap ada /api nya? kalau sekarang di di dokumentasi kan ga ada /api/v1 langsung aja https://devapi.nicepatrol.id/v1 apakah itu benar?"

## ✅ Jawaban: URL Structure yang Benar

### Laravel API Routes Behavior
Di Laravel 11, ketika kita menggunakan `api: __DIR__.'/../routes/api.php'` di `bootstrap/app.php`, Laravel **secara otomatis menambahkan prefix `/api`** ke semua routes di file `routes/api.php`.

### URL Structure yang Benar:

#### 🏠 Local Development
```
http://localhost:8000/api/v1/login
http://localhost:8000/api/v1/penerimaan-barang
http://localhost:8000/api/v1/checkpoints
```

#### 🌐 Production/Staging Servers
```
https://stagapi.nicepatrol.id/api/v1/login
https://stagapi.nicepatrol.id/api/v1/penerimaan-barang
https://stagapi.nicepatrol.id/api/v1/checkpoints
```

#### 🔴 Production Live
```
https://apiv1.nicepatrol.id/api/v1/login
https://apiv1.nicepatrol.id/api/v1/penerimaan-barang
https://apiv1.nicepatrol.id/api/v1/checkpoints
```

## ❌ URL yang Salah (Sebelumnya)
```
https://devapi.nicepatrol.id/v1/login          ❌ SALAH - missing /api
https://stagapi.nicepatrol.id/v1/login         ❌ SALAH - missing /api
https://apiv1.nicepatrol.id/v1/login           ❌ SALAH - missing /api
```

## ✅ URL yang Benar (Sekarang)
```
https://devapi.nicepatrol.id/api/v1/login      ✅ BENAR
https://stagapi.nicepatrol.id/api/v1/login     ✅ BENAR
https://apiv1.nicepatrol.id/api/v1/login       ✅ BENAR
```

## 🔧 Technical Explanation

### File: `routes/api.php`
```php
// Semua routes di file ini otomatis mendapat prefix /api
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    // Hasil: /api/v1/login
});
```

### File: `bootstrap/app.php`
```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // ← Ini otomatis add /api prefix
    // ...
)
```

### Hasil Final URL Structure:
- **Laravel prefix**: `/api` (otomatis dari bootstrap/app.php)
- **Custom prefix**: `/v1` (dari routes/api.php)
- **Endpoint**: `/login` (dari route definition)
- **Final URL**: `/api/v1/login`

## 📋 Files yang Sudah Diperbaiki

### 1. Swagger Documentation (`docs/api/swagger.yaml`)
```yaml
servers:
  - url: https://stagapi.nicepatrol.id/api/v1  # ✅ Fixed
  - url: https://apiv1.nicepatrol.id/api/v1    # ✅ Fixed
```

### 2. Swagger UI (`public/api-docs.html`)
```javascript
const environments = {
    staging: 'https://stagapi.nicepatrol.id/api/v1',  // ✅ Fixed
    prod: 'https://apiv1.nicepatrol.id/api/v1'        // ✅ Fixed
};
```

### 3. Postman Environments
- `environments/Staging-Server.postman_environment.json` ✅ Fixed
- `environments/Production-Live.postman_environment.json` ✅ Fixed
- `environments/Development-Laptop.postman_environment.json` ✅ Fixed

### 4. Documentation Files
- `API-ENVIRONMENTS-SETUP.md` ✅ Fixed
- `PENERIMAAN-BARANG-API-SUMMARY.md` ✅ Fixed

## 🧪 Testing URLs

### Test Login Endpoint:
```bash
# Local
curl -X POST http://localhost:8000/api/v1/login

# Staging (Recommended)
curl -X POST https://stagapi.nicepatrol.id/api/v1/login

# Production
curl -X POST https://apiv1.nicepatrol.id/api/v1/login
```

### Test Penerimaan Barang:
```bash
# Get list
curl -H "Authorization: Bearer {token}" https://stagapi.nicepatrol.id/api/v1/penerimaan-barang

# Create new
curl -X POST -H "Authorization: Bearer {token}" https://stagapi.nicepatrol.id/api/v1/penerimaan-barang
```

## ✅ Kesimpulan

**Ya, di semua environment (local, staging, production) tetap ada `/api` prefix!**

- ✅ **Local**: `http://localhost:8000/api/v1/...`
- ✅ **Staging**: `https://stagapi.nicepatrol.id/api/v1/...`
- ✅ **Production**: `https://apiv1.nicepatrol.id/api/v1/...`

Dokumentasi sebelumnya yang menggunakan `/v1` tanpa `/api` adalah **SALAH** dan sudah diperbaiki.

## 🎯 Action Items Completed

- [x] Fixed Swagger YAML servers URLs
- [x] Fixed Swagger UI JavaScript environments
- [x] Fixed all Postman environment files
- [x] Fixed documentation files
- [x] Added this explanation document

**Status: ✅ SEMUA URL SUDAH DIPERBAIKI**