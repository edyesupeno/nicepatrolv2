# Mobile & API Quick Start

## 🎯 Struktur Domain

```
dash.nicepatrol.id  → Dashboard Admin (existing - JANGAN GANGGU)
app.nicepatrol.id   → Mobile PWA (NEW)
api.nicepatrol.id   → REST API (NEW)
```

## 📁 Folder Baru

```
app/Http/Controllers/Mobile/    → Mobile controllers
app/Http/Controllers/Api/       → API controllers
resources/views/mobile/         → Mobile views
public/mobile/                  → Mobile assets & PWA
```

## 🔧 Setup Environment

Tambahkan di `.env`:

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

## 🚀 API Endpoints

**Base URL:** `https://api.nicepatrol.id/v1`

### Authentication
```bash
POST /login
{
  "email": "user@example.com",
  "password": "password"
}
```

### Protected Endpoints (require Bearer token)
```bash
GET  /user
GET  /lokasis
GET  /checkpoints
GET  /checkpoints?lokasi_id=1
GET  /patrolis
POST /patrolis
GET  /patrolis/{id}
```

## 📱 Mobile Routes

**Base URL:** `https://app.nicepatrol.id`

### Security Officer (security_officer)
```
GET  /               → Home (auto redirect to security dashboard)
GET  /security/patroli        → Patroli list
GET  /security/patroli/create → Start patroli
GET  /security/scan-qr        → QR Scanner
GET  /profile                 → Profile
```

### Office Employee (office_employee)
```
GET  /                        → Home (auto redirect to employee dashboard)
GET  /employee/kehadiran      → Attendance dashboard
POST /employee/kehadiran/checkin  → Check-in
POST /employee/kehadiran/checkout → Check-out
GET  /profile                 → Profile
```

## 🎭 Mobile Roles

Mobile app memiliki 2 role berbeda:

### 1. Security Officer
- Dashboard patroli
- Scan QR checkpoint
- Start/Stop patroli
- Report insiden
- View rute patroli

### 2. Office Employee
- Dashboard kehadiran
- Check-in/Check-out
- View jadwal shift
- History kehadiran
- Request izin/cuti

**Middleware:** `mobile` - Hanya allow security_officer & office_employee

## 🔐 Authentication

### API (Token-based)
1. Login via `POST /login`
2. Get token from response
3. Use token in header: `Authorization: Bearer {token}`

### Mobile (Session-based)
- Same as dashboard admin
- Session shared across subdomains

## 📦 PWA Files

```
public/mobile/manifest.json      → PWA manifest
public/mobile/service-worker.js  → Service worker
public/mobile/css/app.css        → Mobile styles
public/mobile/js/app.js          → Mobile scripts
```

## 🎨 Next: Build Mobile Views

Sekarang tinggal buat views di `resources/views/mobile/`:

### Security Officer Views
- `security/home.blade.php` - Dashboard security
- `security/scan.blade.php` - QR Scanner
- `security/patroli/index.blade.php` - Patroli list
- `security/patroli/create.blade.php` - Start patroli

### Office Employee Views
- `employee/home.blade.php` - Dashboard employee
- `employee/kehadiran.blade.php` - Attendance
- `employee/jadwal.blade.php` - Schedule

### Shared Views
- `layouts/app.blade.php` - Main layout
- `auth/login.blade.php` - Login page
- `profile/index.blade.php` - Profile page
- `partials/bottom-nav-security.blade.php` - Security nav
- `partials/bottom-nav-employee.blade.php` - Employee nav

## 📚 Full Documentation

Lihat `docs/MOBILE-API-STRUCTURE.md` untuk dokumentasi lengkap.
