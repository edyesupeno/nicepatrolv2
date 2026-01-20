# Login Route Fix - 405 Method Not Allowed

## MASALAH TERIDENTIFIKASI ✅

**Error**: `405 Method Not Allowed` pada endpoint login

**Penyebab**: URL yang digunakan di Postman **SALAH**

## ANALISIS MASALAH

### ❌ URL YANG SALAH (di Postman)
```
{{base_url}}/login
```

### ✅ URL YANG BENAR
```
{{base_url}}/api/v1/login
```

## VERIFIKASI ROUTE ✅

Route sudah terdaftar dengan benar:
```bash
php artisan route:list --path=api
```

**Output menunjukkan**:
```
POST api/v1/login ......................... Api\AuthController@login
```

## SOLUSI

### 1. Update Postman Collection Variable

**Set `base_url` variable di Postman**:
- **Development**: `http://localhost:8000`
- **Production**: `https://devapi.nicepatrol.id`

### 2. Update Login Endpoint URL

**Di Postman, ubah URL login menjadi**:
```
{{base_url}}/api/v1/login
```

**BUKAN**:
```
{{base_url}}/login
```

## STRUKTUR URL YANG BENAR

### Development (Local)
```
Base URL: http://localhost:8000
Login: http://localhost:8000/api/v1/login
```

### Production
```
Base URL: https://devapi.nicepatrol.id
Login: https://devapi.nicepatrol.id/v1/login
```

## TESTING CREDENTIALS

### Regular User
```json
{
  "email": "edy@gmail.com",
  "password": "12345678"
}
```

### Admin User
```json
{
  "email": "abb@nicepatrol.id",
  "password": "12345678"
}
```

## POSTMAN CONFIGURATION

### Collection Variables
```
base_url: http://localhost:8000
token: (auto-set after login)
user_id: (auto-set after login)
perusahaan_id: (auto-set after login)
project_id: (auto-set after login)
```

### Login Request
```
Method: POST
URL: {{base_url}}/api/v1/login
Headers:
  Content-Type: application/json
  Accept: application/json
Body (raw JSON):
{
  "email": "edy@gmail.com",
  "password": "12345678"
}
```

## ENVIRONMENT CONFIGURATION

### Current Environment (.env)
```
APP_ENV=local
APP_URL=https://devapp.nicepatrol.id
API_DOMAIN=devapi.nicepatrol.id
```

### Route Registration (routes/api.php)
```php
// Local/Development: accessible from any domain with /api/v1 prefix
Route::prefix('v1')->group($apiRoutes);
```

## QUICK FIX STEPS

1. **Open Postman Collection**
2. **Go to Variables tab**
3. **Set `base_url` to**: `http://localhost:8000`
4. **Update Login request URL to**: `{{base_url}}/api/v1/login`
5. **Test login with credentials**: `edy@gmail.com` / `12345678`

## VERIFICATION

### Test Login Command
```bash
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "edy@gmail.com",
    "password": "12345678"
  }'
```

### Expected Response
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 2,
      "name": "Edy Security",
      "email": "edy@gmail.com",
      "role": "security_officer",
      "perusahaan_id": 1,
      "project_id": 1,
      "project": {
        "id": 1,
        "nama": "Kantor Jakarta"
      }
    },
    "token": "1|abc123..."
  }
}
```

## SUMMARY

**MASALAH**: URL salah di Postman
**SOLUSI**: Gunakan `/api/v1/login` bukan `/login`
**STATUS**: Route sudah benar, hanya perlu update URL di Postman

**LANGKAH CEPAT**:
1. ✅ Update `base_url` variable
2. ✅ Update login URL ke `{{base_url}}/api/v1/login`
3. ✅ Test dengan credentials yang benar
4. ✅ Verifikasi response dan token