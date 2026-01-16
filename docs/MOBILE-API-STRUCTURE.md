# Mobile & API Structure Documentation

## Domain Architecture

Project ini menggunakan 3 domain berbeda dalam 1 Laravel project:

```
dash.nicepatrol.id  → Dashboard Admin (existing)
app.nicepatrol.id   → Mobile PWA (new)
api.nicepatrol.id   → REST API (new)
```

## Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/              # Dashboard admin controllers (existing)
│   │   ├── Perusahaan/         # Perusahaan controllers (existing)
│   │   ├── Mobile/             # Mobile PWA controllers (NEW)
│   │   │   ├── AuthController.php
│   │   │   ├── HomeController.php
│   │   │   ├── PatroliController.php
│   │   │   └── ProfileController.php
│   │   └── Api/                # API controllers (NEW)
│   │       ├── AuthController.php
│   │       ├── LokasiController.php
│   │       ├── CheckpointController.php
│   │       └── PatroliController.php
│   └── Resources/              # API Resources (NEW)

resources/
├── views/
│   ├── admin/                  # Dashboard views (existing)
│   ├── perusahaan/             # Perusahaan views (existing)
│   └── mobile/                 # Mobile PWA views (NEW)
│       ├── layouts/
│       ├── auth/
│       ├── security/           # Security Officer views
│       │   ├── home.blade.php
│       │   ├── patroli/
│       │   └── scan.blade.php
│       ├── employee/           # Office Employee views
│       │   ├── home.blade.php
│       │   └── kehadiran.blade.php
│       ├── profile/
│       └── partials/

public/
├── admin/                      # Admin assets (existing)
├── mobile/                     # Mobile PWA assets (NEW)
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   ├── icons/                  # PWA icons
│   ├── manifest.json           # PWA manifest
│   └── service-worker.js       # Service worker

routes/
├── web.php                     # Web & Mobile routes
└── api.php                     # API routes
```

## Routes Configuration

### API Routes (api.nicepatrol.id)

```php
// routes/api.php
Route::domain(env('API_DOMAIN', 'api.nicepatrol.id'))->prefix('v1')->group(function () {
    // Public
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::apiResource('lokasis', LokasiController::class);
        Route::apiResource('checkpoints', CheckpointController::class);
        Route::apiResource('patrolis', PatroliController::class);
    });
});
```

**Base URL:** `https://api.nicepatrol.id/v1`

**Endpoints:**
- `POST /login` - Login & get token
- `POST /logout` - Logout
- `GET /user` - Get current user
- `GET /lokasis` - Get all lokasi
- `GET /checkpoints` - Get all checkpoints
- `GET /checkpoints?lokasi_id=1` - Filter by lokasi
- `GET /patrolis` - Get patroli list (paginated)
- `POST /patrolis` - Create new patroli
- `GET /patrolis/{id}` - Get patroli detail

### Mobile Routes (app.nicepatrol.id)

```php
// routes/web.php
Route::domain(env('MOBILE_DOMAIN', 'app.nicepatrol.id'))->group(function () {
    Route::get('/login', [Mobile\AuthController::class, 'showLogin']);
    
    Route::middleware(['auth', 'mobile'])->group(function () {
        // Home (auto redirect based on role)
        Route::get('/', [Mobile\HomeController::class, 'index']);
        
        // Security Officer Routes
        Route::middleware('role:security_officer')->prefix('security')->group(function () {
            Route::get('/patroli', [Mobile\PatroliController::class, 'index']);
            Route::get('/patroli/create', [Mobile\PatroliController::class, 'create']);
            Route::get('/scan-qr', [Mobile\ScanController::class, 'index']);
        });
        
        // Office Employee Routes
        Route::middleware('role:office_employee')->prefix('employee')->group(function () {
            Route::get('/kehadiran', [Mobile\KehadiranController::class, 'index']);
            Route::post('/kehadiran/checkin', [Mobile\KehadiranController::class, 'checkin']);
            Route::post('/kehadiran/checkout', [Mobile\KehadiranController::class, 'checkout']);
        });
        
        // Shared Routes
        Route::get('/profile', [Mobile\ProfileController::class, 'index']);
    });
});
```

## Mobile App - Role Based Views

Mobile app memiliki 2 tampilan berbeda berdasarkan role:

### 1. Security Officer (security_officer)
**Features:**
- Dashboard patroli
- Scan QR Code checkpoint
- Start/Stop patroli
- Report insiden
- View rute patroli
- Checklist pemeriksaan

**Routes:**
- `GET /` → Auto redirect ke security dashboard
- `GET /security/patroli` → List patroli
- `GET /security/patroli/create` → Start patroli
- `GET /security/scan-qr` → QR Scanner
- `GET /profile` → Profile

### 2. Office Employee (office_employee)
**Features:**
- Dashboard kehadiran
- Check-in/Check-out
- View jadwal shift
- View history kehadiran
- Request izin/cuti

**Routes:**
- `GET /` → Auto redirect ke employee dashboard
- `GET /employee/kehadiran` → Kehadiran dashboard
- `POST /employee/kehadiran/checkin` → Check-in
- `POST /employee/kehadiran/checkout` → Check-out
- `GET /profile` → Profile

## Authentication

### API Authentication (Sanctum Token)

**Login:**
```bash
POST https://api.nicepatrol.id/v1/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "user@example.com",
      "role": "security",
      "perusahaan_id": 1
    },
    "token": "1|abc123def456..."
  }
}
```

**Using Token:**
```bash
GET https://api.nicepatrol.id/v1/patrolis
Authorization: Bearer 1|abc123def456...
```

### Mobile Authentication (Session)

Mobile PWA menggunakan Laravel session (sama seperti dashboard admin).
Session di-share antar subdomain dengan config:

```env
SESSION_DOMAIN=.nicepatrol.id
```

## Environment Configuration

**`.env`:**
```env
# Domain Configuration
DASHBOARD_DOMAIN=dash.nicepatrol.id
MOBILE_DOMAIN=app.nicepatrol.id
API_DOMAIN=api.nicepatrol.id

# Session (shared across subdomains)
SESSION_DOMAIN=.nicepatrol.id

# Sanctum
SANCTUM_STATEFUL_DOMAINS=dash.nicepatrol.id,app.nicepatrol.id
```

## PWA Configuration

### Manifest (`public/mobile/manifest.json`)

Defines PWA properties:
- App name & icons
- Display mode (standalone)
- Theme colors
- Start URL

### Service Worker (`public/mobile/service-worker.js`)

Handles:
- Offline caching
- Asset caching
- Background sync (future)

### Installation

Users can install PWA:
1. Open `app.nicepatrol.id` in mobile browser
2. Browser will show "Add to Home Screen" prompt
3. App will run in standalone mode (no browser UI)

## API Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": { ... }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": { ... }
}
```

### Pagination Response

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [ ... ],
    "first_page_url": "...",
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

## Multi-Tenancy

**CRITICAL:** Semua API & Mobile tetap mengikuti aturan multi-tenancy:

1. Global scope `perusahaan_id` tetap aktif
2. Auto-assign `perusahaan_id` saat create
3. User hanya bisa akses data perusahaannya
4. Superadmin bisa akses semua data

## Development

### Local Development

**Option 1: Edit hosts file**
```
127.0.0.1 dash.nicepatrol.id
127.0.0.1 app.nicepatrol.id
127.0.0.1 api.nicepatrol.id
```

**Option 2: Use .test domain**
```env
DASHBOARD_DOMAIN=dash.nicepatrol.test
MOBILE_DOMAIN=app.nicepatrol.test
API_DOMAIN=api.nicepatrol.test
```

### Testing API

**Using cURL:**
```bash
# Login
curl -X POST https://api.nicepatrol.id/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get data with token
curl https://api.nicepatrol.id/v1/patrolis \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Using Postman:**
1. Create new request
2. Set Authorization → Bearer Token
3. Add token from login response

## Deployment

### DNS Configuration

Add A records:
```
app.nicepatrol.id  → Server IP
api.nicepatrol.id  → Server IP
```

### SSL Certificate

```bash
sudo certbot certonly --nginx \
  -d nicepatrol.id \
  -d dash.nicepatrol.id \
  -d app.nicepatrol.id \
  -d api.nicepatrol.id
```

### Nginx Configuration

See `docs/deployment/NGINX-CONFIG.md` for complete nginx setup.

## Next Steps

1. ✅ Struktur folder API & Mobile
2. ✅ Routes configuration
3. ✅ Basic controllers
4. ✅ PWA setup
5. ⏳ Mobile views (Blade templates)
6. ⏳ API endpoints lengkap
7. ⏳ QR Code scanner
8. ⏳ Geolocation tracking
9. ⏳ Photo upload
10. ⏳ Offline sync

## Notes

- Dashboard admin (existing) **TIDAK DIUBAH**
- Semua development baru di folder `Mobile/` dan `Api/`
- Multi-tenancy rules tetap berlaku
- PWA bisa diinstall seperti native app
- API siap untuk native app di masa depan
