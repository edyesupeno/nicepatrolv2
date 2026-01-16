# Role Management System

## Daftar Roles

Project ini memiliki 9 roles yang berbeda dengan akses dan fungsi masing-masing:

### 1. **Superadmin**
- **Constant**: `User::ROLE_SUPERADMIN` = `'superadmin'`
- **Display Name**: Super Admin
- **Akses**: Full access ke seluruh sistem
- **Dashboard**: `/admin/dashboard`
- **Fungsi**:
  - Manage semua perusahaan
  - Manage system settings
  - Full control atas semua data

### 2. **Admin**
- **Constant**: `User::ROLE_ADMIN` = `'admin'`
- **Display Name**: Admin
- **Akses**: Admin perusahaan (current implementation)
- **Dashboard**: `/perusahaan/dashboard`
- **Fungsi**:
  - Manage data perusahaan
  - Manage karyawan, payroll, attendance
  - Manage patrol system

### 3. **Security Officer** (Default untuk user baru)
- **Constant**: `User::ROLE_SECURITY_OFFICER` = `'security_officer'`
- **Display Name**: Security Officer
- **Akses**: Terbatas pada patrol operations
- **Dashboard**: TBD (To Be Developed)
- **Fungsi** (Planned):
  - Scan QR checkpoint
  - Mulai/selesai patroli
  - Lapor insiden
  - View riwayat patroli pribadi

### 4. **Office Employee**
- **Constant**: `User::ROLE_OFFICE_EMPLOYEE` = `'office_employee'`
- **Display Name**: Office Employee
- **Akses**: Terbatas pada data pribadi
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - View attendance pribadi
  - Submit leave request
  - View payslip
  - Update personal data

### 5. **Manager Project**
- **Constant**: `User::ROLE_MANAGER_PROJECT` = `'manager_project'`
- **Display Name**: Manager Project
- **Akses**: Manage satu project
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - View project overview & analytics
  - Manage team members
  - Approve leave requests
  - View project reports

### 6. **Admin Project**
- **Constant**: `User::ROLE_ADMIN_PROJECT` = `'admin_project'`
- **Display Name**: Admin Project
- **Akses**: Admin untuk satu project
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - Manage employee schedules
  - Track attendance
  - Manage project settings
  - Generate reports

### 7. **Admin Branch**
- **Constant**: `User::ROLE_ADMIN_BRANCH` = `'admin_branch'`
- **Display Name**: Admin Branch
- **Akses**: Manage multiple projects dalam satu branch
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - Branch overview
  - Multi-project management
  - Resource allocation
  - Branch-level reports

### 8. **Finance Branch**
- **Constant**: `User::ROLE_FINANCE_BRANCH` = `'finance_branch'`
- **Display Name**: Finance Branch
- **Akses**: Finance operations untuk branch
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - Payroll management
  - Financial reports
  - Budget tracking
  - Payment approvals

### 9. **Admin HSSE**
- **Constant**: `User::ROLE_ADMIN_HSSE` = `'admin_hsse'`
- **Display Name**: Admin HSSE
- **Akses**: Health, Safety, Security, Environment
- **Dashboard**: TBD
- **Fungsi** (Planned):
  - Safety incident management
  - Health checkup tracking
  - Safety training records
  - Compliance reports

## Penggunaan

### Check Role di Controller
```php
// Single role check
if (auth()->user()->isSecurityOfficer()) {
    // Do something
}

// Multiple roles check
if (auth()->user()->hasRole(['manager_project', 'admin_project'])) {
    // Do something
}

// Using constants
if (auth()->user()->role === User::ROLE_SECURITY_OFFICER) {
    // Do something
}
```

### Check Role di Blade
```blade
@if(auth()->user()->isSecurityOfficer())
    <!-- Security Officer content -->
@endif

@if(auth()->user()->hasRole(['manager_project', 'admin_project']))
    <!-- Manager/Admin Project content -->
@endif
```

### Middleware
```php
// Single role
Route::middleware('role:security_officer')->group(function () {
    // Routes
});

// Multiple roles
Route::middleware('role:manager_project,admin_project')->group(function () {
    // Routes
});
```

### Get Role Display Name
```php
$displayName = auth()->user()->getRoleDisplayName();
// Returns: "Security Officer", "Manager Project", etc.
```

### Get All Available Roles
```php
$roles = User::getAllRoles();
// Returns array: ['security_officer' => 'Security Officer', ...]
```

## Migration

Untuk apply roles baru, jalankan:
```bash
php artisan migrate
```

Migration akan update enum `role` di tabel `users` dengan roles baru.

## Default Role

**Default role untuk user baru adalah `security_officer`**

Ini berlaku untuk:
- User yang di-import
- User yang dibuat manual
- User yang register (jika ada fitur register)

## Backward Compatibility

Role `petugas` masih tersedia untuk backward compatibility dengan data lama.

## TODO - Development Plan

1. **Phase 1**: Setup roles & permissions ✅
2. **Phase 2**: Buat dashboard untuk Security Officer
3. **Phase 3**: Buat dashboard untuk Office Employee
4. **Phase 4**: Buat dashboard untuk Manager Project
5. **Phase 5**: Buat dashboard untuk Admin Project
6. **Phase 6**: Buat dashboard untuk Admin Branch
7. **Phase 7**: Buat dashboard untuk Finance Branch
8. **Phase 8**: Buat dashboard untuk Admin HSSE

## Notes

- Superadmin selalu bisa akses semua halaman (bypass role check di middleware)
- Setiap role akan punya dashboard dan menu yang berbeda
- Role-based access control akan diterapkan di level route dan controller
