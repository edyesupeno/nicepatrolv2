# Multi-Tenancy API Fix - Nice Patrol System

## Masalah yang Diperbaiki

Sebelumnya, API tidak membatasi data berdasarkan `project_id` user yang login. User bisa melihat semua project dan data dari perusahaan yang sama, padahal seharusnya hanya bisa melihat data dari project mereka sendiri.

## Perubahan yang Dibuat

### 1. AuthController - Mengembalikan project_id User

**File:** `app/Http/Controllers/Api/AuthController.php`

- ✅ **Login endpoint** sekarang mengembalikan `project_id` dan `project` info
- ✅ **User/Me endpoint** juga mengembalikan `project_id` dan `project` info
- ✅ Project info diambil dari `user->karyawan->project_id`

**Response baru:**
```json
{
  "data": {
    "user": {
      "id": 1,
      "project_id": 5,
      "project": {
        "id": 5,
        "nama": "Kantor Jakarta"
      }
    }
  }
}
```

### 2. Global Scope untuk Multi-Tenancy

**File:** `app/Models/Project.php`
- ✅ Tambah global scope `project` untuk non-superadmin
- ✅ User hanya bisa lihat project mereka sendiri

**File:** `app/Models/Area.php`
- ✅ Tambah global scope `project` untuk non-superadmin
- ✅ Area ter-filter berdasarkan project_id user

**File:** `app/Models/PenerimaanBarang.php`
- ✅ Tambah global scope `project` untuk non-superadmin
- ✅ Data penerimaan barang ter-filter berdasarkan project_id user

### 3. API Controller Improvements

**File:** `app/Http/Controllers/Api/PenerimaanBarangController.php`

#### Method `getProjects()`
- ✅ Sekarang menggunakan global scope otomatis
- ✅ Non-superadmin hanya lihat project mereka
- ✅ Superadmin lihat semua project di perusahaan mereka

#### Method `getAreasByProject()`
- ✅ Validasi project_id untuk non-superadmin
- ✅ User tidak bisa akses area dari project lain
- ✅ Global scope otomatis filter area

#### Method `store()`
- ✅ Auto-assign `project_id` jika tidak diisi
- ✅ Validasi dilakukan oleh global scope (tidak perlu manual)
- ✅ Non-superadmin otomatis assign ke project mereka

#### Method `update()`
- ✅ Validasi ownership otomatis via global scope
- ✅ User tidak bisa update data project lain

### 4. Testing

**File:** `tests/Feature/Api/MultiTenancyTest.php`
- ✅ Test isolasi data antar perusahaan
- ✅ Test isolasi data antar project
- ✅ Test auto-assignment project_id
- ✅ Test superadmin access
- ✅ Test login response dengan project info

## Cara Kerja Multi-Tenancy

### Level 1: Perusahaan (Company)
```php
// Global scope di semua model
if (auth()->check() && auth()->user()->perusahaan_id) {
    $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
}
```

### Level 2: Project (dalam Perusahaan)
```php
// Global scope untuk non-superadmin
if (!$user->isSuperAdmin()) {
    if ($user->karyawan && $user->karyawan->project_id) {
        $builder->where('project_id', $user->karyawan->project_id);
    }
}
```

## Hasil Setelah Fix

### ✅ User Security Officer
- Hanya bisa lihat project mereka sendiri
- Hanya bisa lihat data penerimaan barang dari project mereka
- Hanya bisa lihat area dari project mereka
- Auto-assign ke project mereka saat create data

### ✅ User Superadmin
- Bisa lihat semua project di perusahaan mereka
- Bisa lihat semua data di perusahaan mereka
- Bisa assign data ke project manapun di perusahaan mereka

### ✅ API Response
- Login mengembalikan `project_id` dan `project` info
- Dropdown project hanya tampil project user
- Dropdown area hanya tampil area dari project user

## Testing

Jalankan test untuk memastikan multi-tenancy berfungsi:

```bash
php artisan test tests/Feature/Api/MultiTenancyTest.php
```

## Security Checklist

- [x] User tidak bisa lihat data perusahaan lain
- [x] User tidak bisa lihat data project lain
- [x] User tidak bisa create/update data untuk project lain
- [x] Auto-assignment project_id untuk non-superadmin
- [x] Global scope otomatis filter semua query
- [x] API endpoint ter-proteksi dengan proper validation
- [x] Test coverage untuk semua scenario multi-tenancy

## Catatan Penting

1. **Global Scope** otomatis bekerja di semua query Eloquent
2. **Superadmin** tetap bisa akses semua data di perusahaan mereka
3. **Project_id** diambil dari `user->karyawan->project_id`
4. **Validation** dilakukan otomatis oleh global scope
5. **API Response** konsisten dengan format yang sudah ada

Dengan perubahan ini, API sudah aman dan sesuai dengan prinsip multi-tenancy yang ketat.