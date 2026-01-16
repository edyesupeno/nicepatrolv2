# Mobile App - Role Based Features

## Overview

Mobile app (app.nicepatrol.id) memiliki 2 role dengan tampilan dan fitur berbeda:

1. **Security Officer** - Untuk petugas security lapangan
2. **Office Employee** - Untuk karyawan kantor

## Role: Security Officer (security_officer)

### Dashboard Features
- Total patroli hari ini
- Status patroli aktif
- Total patroli bulan ini
- Quick action: Start Patroli, Scan QR

### Main Features

#### 1. Patroli Management
**Route:** `/security/patroli`

- View list patroli (hari ini, minggu ini, bulan ini)
- Start new patroli
- View patroli detail
- Complete patroli

#### 2. QR Code Scanner
**Route:** `/security/scan-qr`

- Scan QR code checkpoint
- Verify checkpoint
- Auto-record checkpoint visit
- Capture photo at checkpoint
- Add notes/catatan

#### 3. Checkpoint Checklist
- View checklist items per checkpoint
- Mark items as checked
- Add photos for evidence
- Add notes for issues

#### 4. Incident Report
- Report insiden saat patroli
- Upload foto insiden
- Pilih kategori insiden
- Add location (GPS)
- Add timestamp

#### 5. Rute Patroli
- View assigned rute
- View checkpoint list in rute
- Navigation to next checkpoint
- Track progress

### Bottom Navigation
```
[Home] [Patroli] [Scan] [Profile]
```

### Permissions
- ✅ Access mobile app
- ✅ View own patroli
- ✅ Create patroli
- ✅ Scan checkpoint
- ✅ Report insiden
- ❌ View other security's patroli
- ❌ Access employee features

---

## Role: Office Employee (office_employee)

### Dashboard Features
- Status kehadiran hari ini (sudah check-in/belum)
- Jam masuk & keluar
- Total kehadiran bulan ini
- Quick action: Check-in, Check-out

### Main Features

#### 1. Kehadiran (Attendance)
**Route:** `/employee/kehadiran`

- Check-in dengan GPS
- Check-out dengan GPS
- View today's attendance
- View attendance history
- View monthly summary

#### 2. Jadwal Shift
- View shift schedule
- View shift details (jam masuk/keluar)
- View shift calendar

#### 3. History Kehadiran
- View attendance history
- Filter by date range
- View statistics (hadir, izin, sakit, alpha)
- Export to PDF

#### 4. Request Izin/Cuti (Future)
- Request izin
- Request cuti
- View request status
- View remaining cuti days

### Bottom Navigation
```
[Home] [Kehadiran] [Jadwal] [Profile]
```

### Permissions
- ✅ Access mobile app
- ✅ Check-in/Check-out
- ✅ View own attendance
- ✅ View own schedule
- ✅ Request izin/cuti
- ❌ View other employee's data
- ❌ Access security features

---

## Shared Features (Both Roles)

### Profile
**Route:** `/profile`

- View profile info
- View perusahaan info
- Change password
- Logout
- App settings

### Notifications (Future)
- Push notifications
- In-app notifications
- Notification history

### Offline Mode (PWA)
- Cache essential data
- Sync when online
- Offline indicator

---

## Role Detection & Redirect

### HomeController Logic

```php
public function index()
{
    $user = auth()->user();
    
    if ($user->isSecurityOfficer()) {
        return $this->securityDashboard();
    } elseif ($user->isOfficeEmployee()) {
        return $this->employeeDashboard();
    }
    
    abort(403, 'Unauthorized role for mobile app');
}
```

### Middleware Protection

```php
// CheckMobileRole Middleware
// Only allow security_officer and office_employee
if (!$user->isSecurityOfficer() && !$user->isOfficeEmployee()) {
    abort(403, 'Anda tidak memiliki akses ke aplikasi mobile');
}
```

### Route Protection

```php
// Security routes
Route::middleware('role:security_officer')->group(function () {
    // Only security_officer can access
});

// Employee routes
Route::middleware('role:office_employee')->group(function () {
    // Only office_employee can access
});
```

---

## View Structure

```
resources/views/mobile/
├── layouts/
│   └── app.blade.php           # Main layout
├── auth/
│   └── login.blade.php         # Login page
├── security/                   # Security Officer views
│   ├── home.blade.php          # Security dashboard
│   ├── scan.blade.php          # QR Scanner
│   └── patroli/
│       ├── index.blade.php     # Patroli list
│       ├── create.blade.php    # Start patroli
│       └── show.blade.php      # Patroli detail
├── employee/                   # Office Employee views
│   ├── home.blade.php          # Employee dashboard
│   ├── kehadiran.blade.php     # Attendance
│   └── jadwal.blade.php        # Schedule
├── profile/
│   └── index.blade.php         # Profile (shared)
└── partials/
    ├── bottom-nav-security.blade.php
    └── bottom-nav-employee.blade.php
```

---

## API Endpoints by Role

### Security Officer API

```
GET  /api/v1/patrolis              # Get own patroli
POST /api/v1/patrolis              # Create patroli
GET  /api/v1/patrolis/{id}         # Get patroli detail
PUT  /api/v1/patrolis/{id}         # Update patroli
POST /api/v1/patrolis/{id}/scan    # Scan checkpoint
GET  /api/v1/checkpoints           # Get checkpoints
GET  /api/v1/lokasis               # Get lokasi
```

### Office Employee API

```
GET  /api/v1/kehadiran             # Get own attendance
POST /api/v1/kehadiran/checkin     # Check-in
POST /api/v1/kehadiran/checkout    # Check-out
GET  /api/v1/schedule              # Get schedule
GET  /api/v1/izin                  # Get izin/cuti
POST /api/v1/izin                  # Request izin/cuti
```

---

## Testing Roles

### Test Security Officer
```bash
# Login as security
POST /api/v1/login
{
  "email": "security@example.com",
  "password": "password"
}

# Access security features
GET /security/patroli
GET /security/scan-qr
```

### Test Office Employee
```bash
# Login as employee
POST /api/v1/login
{
  "email": "employee@example.com",
  "password": "password"
}

# Access employee features
GET /employee/kehadiran
POST /employee/kehadiran/checkin
```

### Test Unauthorized Access
```bash
# Security trying to access employee features
GET /employee/kehadiran
# Should return 403 Forbidden

# Employee trying to access security features
GET /security/patroli
# Should return 403 Forbidden
```

---

## Next Steps

### Security Officer Features
1. ✅ Basic structure
2. ⏳ QR Scanner implementation
3. ⏳ Patroli CRUD
4. ⏳ Checkpoint checklist
5. ⏳ Incident report
6. ⏳ Photo upload
7. ⏳ GPS tracking

### Office Employee Features
1. ✅ Basic structure
2. ⏳ Check-in/Check-out UI
3. ⏳ Attendance history
4. ⏳ Schedule view
5. ⏳ Izin/Cuti request
6. ⏳ GPS validation

### Shared Features
1. ⏳ Profile page
2. ⏳ Change password
3. ⏳ Notifications
4. ⏳ Offline sync
5. ⏳ PWA install prompt
