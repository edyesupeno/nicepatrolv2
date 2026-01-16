# API Documentation - Nice Patrol System

Base URL: `http://localhost:8000/api`

## Authentication

Sistem menggunakan Laravel Sanctum untuk autentikasi berbasis token.

### Headers Required

Untuk semua endpoint yang memerlukan autentikasi:

```
Authorization: Bearer {your-token}
Content-Type: application/json
```

## Response Format

### Success Response

```json
{
  "id": 1,
  "nama": "Data",
  "created_at": "2026-01-14T10:00:00.000000Z",
  "updated_at": "2026-01-14T10:00:00.000000Z"
}
```

### Error Response

```json
{
  "message": "Error message",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

## Endpoints

---

## 1. Authentication

### 1.1 Login

**Endpoint:** `POST /api/login`

**Request Body:**
```json
{
  "email": "superadmin@nicepatrol.id",
  "password": "password"
}
```

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "perusahaan_id": null,
    "name": "Super Admin",
    "email": "superadmin@nicepatrol.id",
    "role": "superadmin",
    "is_active": true,
    "created_at": "2026-01-14T10:00:00.000000Z",
    "updated_at": "2026-01-14T10:00:00.000000Z",
    "perusahaan": null
  },
  "token": "1|abc123def456..."
}
```

**Error Responses:**
- `422 Unprocessable Entity` - Validation error
- `403 Forbidden` - Account not active

---

### 1.2 Logout

**Endpoint:** `POST /api/logout`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
{
  "message": "Logout berhasil"
}
```

---

### 1.3 Get Current User

**Endpoint:** `GET /api/me`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
{
  "user": {
    "id": 1,
    "perusahaan_id": 1,
    "name": "Admin ABB",
    "email": "abb@nicepatrol.id",
    "role": "admin",
    "is_active": true,
    "perusahaan": {
      "id": 1,
      "nama": "PT ABB",
      "kode": "ABB",
      "alamat": "Jakarta",
      "telepon": "021-1234567",
      "email": "info@abb.co.id",
      "is_active": true
    }
  }
}
```

---

## 2. Perusahaan (Superadmin Only)

### 2.1 List Perusahaan

**Endpoint:** `GET /api/perusahaans`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "nama": "PT ABB",
    "kode": "ABB",
    "alamat": "Jakarta",
    "telepon": "021-1234567",
    "email": "info@abb.co.id",
    "is_active": true,
    "created_at": "2026-01-14T10:00:00.000000Z",
    "updated_at": "2026-01-14T10:00:00.000000Z",
    "users": [...]
  }
]
```

---

### 2.2 Create Perusahaan

**Endpoint:** `POST /api/perusahaans`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "nama": "PT Security Indonesia",
  "kode": "SECINDO",
  "alamat": "Jakarta Selatan",
  "telepon": "021-9999999",
  "email": "info@secindo.co.id",
  "is_active": true
}
```

**Validation Rules:**
- `nama`: required, string, max 255
- `kode`: required, string, max 50, unique
- `alamat`: nullable, string
- `telepon`: nullable, string, max 20
- `email`: nullable, email, max 255
- `is_active`: boolean

**Response:** `201 Created`

**Error:** `403 Forbidden` - Only superadmin can create

---

### 2.3 Get Perusahaan Detail

**Endpoint:** `GET /api/perusahaans/{id}`

**Response:** `200 OK`
```json
{
  "id": 1,
  "nama": "PT ABB",
  "kode": "ABB",
  "alamat": "Jakarta",
  "telepon": "021-1234567",
  "email": "info@abb.co.id",
  "is_active": true,
  "users": [...],
  "lokasis": [...]
}
```

---

### 2.4 Update Perusahaan

**Endpoint:** `PUT /api/perusahaans/{id}`

**Request Body:** (all fields optional)
```json
{
  "nama": "PT ABB Updated",
  "is_active": false
}
```

**Response:** `200 OK`

---

### 2.5 Delete Perusahaan

**Endpoint:** `DELETE /api/perusahaans/{id}`

**Response:** `200 OK`
```json
{
  "message": "Perusahaan berhasil dihapus"
}
```

---

## 3. Users

### 3.1 List Users

**Endpoint:** `GET /api/users`

**Headers:** `Authorization: Bearer {token}`

**Note:** 
- Superadmin: melihat semua users
- Admin/Petugas: hanya users di perusahaan sendiri

**Response:** `200 OK`
```json
[
  {
    "id": 2,
    "perusahaan_id": 1,
    "name": "Admin ABB",
    "email": "abb@nicepatrol.id",
    "role": "admin",
    "is_active": true,
    "created_at": "2026-01-14T10:00:00.000000Z",
    "updated_at": "2026-01-14T10:00:00.000000Z",
    "perusahaan": {...}
  }
]
```

---

### 3.2 Create User

**Endpoint:** `POST /api/users`

**Request Body:**
```json
{
  "name": "Petugas Budi",
  "email": "budi@nicepatrol.id",
  "password": "password",
  "role": "petugas",
  "perusahaan_id": 1,
  "is_active": true
}
```

**Validation Rules:**
- `name`: required, string, max 255
- `email`: required, email, unique
- `password`: required, min 6
- `role`: required, in:superadmin,admin,petugas
- `perusahaan_id`: nullable, exists in perusahaans
- `is_active`: boolean

**Note:**
- Non-superadmin tidak bisa create role superadmin
- Non-superadmin otomatis assign ke perusahaan sendiri

**Response:** `201 Created`

---

### 3.3 Get User Detail

**Endpoint:** `GET /api/users/{id}`

**Response:** `200 OK`

---

### 3.4 Update User

**Endpoint:** `PUT /api/users/{id}`

**Request Body:** (all fields optional)
```json
{
  "name": "Petugas Budi Updated",
  "email": "budi.new@nicepatrol.id",
  "password": "newpassword",
  "is_active": false
}
```

**Response:** `200 OK`

---

### 3.5 Delete User

**Endpoint:** `DELETE /api/users/{id}`

**Response:** `200 OK`
```json
{
  "message": "User berhasil dihapus"
}
```

---

## 4. Lokasi

### 4.1 List Lokasi

**Endpoint:** `GET /api/lokasis`

**Headers:** `Authorization: Bearer {token}`

**Note:** Otomatis filtered by perusahaan_id (kecuali superadmin)

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "perusahaan_id": 1,
    "nama": "Gedung A",
    "alamat": "Jl. Sudirman No. 123",
    "latitude": "-6.200000",
    "longitude": "106.816666",
    "is_active": true,
    "created_at": "2026-01-14T10:00:00.000000Z",
    "updated_at": "2026-01-14T10:00:00.000000Z",
    "perusahaan": {...}
  }
]
```

---

### 4.2 Create Lokasi

**Endpoint:** `POST /api/lokasis`

**Request Body:**
```json
{
  "nama": "Gedung A",
  "alamat": "Jl. Sudirman No. 123",
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "is_active": true
}
```

**Validation Rules:**
- `nama`: required, string, max 255
- `alamat`: nullable, string
- `latitude`: nullable, string
- `longitude`: nullable, string
- `is_active`: boolean

**Note:** `perusahaan_id` otomatis diisi dari user yang login

**Response:** `201 Created`

---

### 4.3 Get Lokasi Detail

**Endpoint:** `GET /api/lokasis/{id}`

**Response:** `200 OK`
```json
{
  "id": 1,
  "perusahaan_id": 1,
  "nama": "Gedung A",
  "alamat": "Jl. Sudirman No. 123",
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "is_active": true,
  "checkpoints": [
    {
      "id": 1,
      "nama": "Pintu Masuk Utama",
      "kode": "CP-001",
      "urutan": 1
    }
  ]
}
```

---

### 4.4 Update Lokasi

**Endpoint:** `PUT /api/lokasis/{id}`

**Request Body:** (all fields optional)
```json
{
  "nama": "Gedung A - Updated",
  "is_active": false
}
```

**Response:** `200 OK`

---

### 4.5 Delete Lokasi

**Endpoint:** `DELETE /api/lokasis/{id}`

**Response:** `200 OK`
```json
{
  "message": "Lokasi berhasil dihapus"
}
```

---

## 5. Checkpoint

### 5.1 List Checkpoint

**Endpoint:** `GET /api/checkpoints`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "perusahaan_id": 1,
    "lokasi_id": 1,
    "nama": "Pintu Masuk Utama",
    "kode": "CP-001",
    "deskripsi": "Checkpoint di pintu masuk utama gedung",
    "urutan": 1,
    "is_active": true,
    "created_at": "2026-01-14T10:00:00.000000Z",
    "updated_at": "2026-01-14T10:00:00.000000Z",
    "lokasi": {...}
  }
]
```

---

### 5.2 Create Checkpoint

**Endpoint:** `POST /api/checkpoints`

**Request Body:**
```json
{
  "lokasi_id": 1,
  "nama": "Pintu Masuk Utama",
  "kode": "CP-001",
  "deskripsi": "Checkpoint di pintu masuk utama gedung",
  "urutan": 1,
  "is_active": true
}
```

**Validation Rules:**
- `lokasi_id`: required, exists in lokasis
- `nama`: required, string, max 255
- `kode`: required, string, max 50, unique
- `deskripsi`: nullable, string
- `urutan`: integer
- `is_active`: boolean

**Response:** `201 Created`

---

### 5.3 Get Checkpoint Detail

**Endpoint:** `GET /api/checkpoints/{id}`

**Response:** `200 OK`

---

### 5.4 Update Checkpoint

**Endpoint:** `PUT /api/checkpoints/{id}`

**Request Body:** (all fields optional)
```json
{
  "nama": "Pintu Masuk Utama - Updated",
  "urutan": 2
}
```

**Response:** `200 OK`

---

### 5.5 Delete Checkpoint

**Endpoint:** `DELETE /api/checkpoints/{id}`

**Response:** `200 OK`
```json
{
  "message": "Checkpoint berhasil dihapus"
}
```

---

## 6. Patroli

### 6.1 List Patroli

**Endpoint:** `GET /api/patrolis`

**Headers:** `Authorization: Bearer {token}`

**Response:** `200 OK`
```json
[
  {
    "id": 1,
    "perusahaan_id": 1,
    "lokasi_id": 1,
    "user_id": 2,
    "waktu_mulai": "2026-01-14T20:00:00.000000Z",
    "waktu_selesai": "2026-01-14T22:00:00.000000Z",
    "status": "selesai",
    "catatan": "Patroli malam shift 1",
    "created_at": "2026-01-14T20:00:00.000000Z",
    "updated_at": "2026-01-14T22:00:00.000000Z",
    "lokasi": {...},
    "user": {...},
    "details": [...]
  }
]
```

---

### 6.2 Start Patroli

**Endpoint:** `POST /api/patrolis`

**Request Body:**
```json
{
  "lokasi_id": 1,
  "catatan": "Patroli malam shift 1"
}
```

**Validation Rules:**
- `lokasi_id`: required, exists in lokasis
- `catatan`: nullable, string

**Note:**
- `perusahaan_id` dan `user_id` otomatis diisi
- `waktu_mulai` otomatis set ke waktu sekarang
- `status` otomatis set ke "berlangsung"

**Response:** `201 Created`

---

### 6.3 Get Patroli Detail

**Endpoint:** `GET /api/patrolis/{id}`

**Response:** `200 OK`
```json
{
  "id": 1,
  "perusahaan_id": 1,
  "lokasi_id": 1,
  "user_id": 2,
  "waktu_mulai": "2026-01-14T20:00:00.000000Z",
  "waktu_selesai": null,
  "status": "berlangsung",
  "catatan": "Patroli malam shift 1",
  "lokasi": {
    "id": 1,
    "nama": "Gedung A"
  },
  "user": {
    "id": 2,
    "name": "Petugas Budi"
  },
  "details": [
    {
      "id": 1,
      "checkpoint_id": 1,
      "waktu_scan": "2026-01-14T20:15:00.000000Z",
      "latitude": "-6.200000",
      "longitude": "106.816666",
      "catatan": "Kondisi normal",
      "foto": "patroli-photos/abc123.jpg",
      "status": "normal",
      "checkpoint": {
        "id": 1,
        "nama": "Pintu Masuk Utama",
        "kode": "CP-001"
      }
    }
  ]
}
```

---

### 6.4 Update Patroli

**Endpoint:** `PUT /api/patrolis/{id}`

**Request Body:**
```json
{
  "status": "selesai",
  "catatan": "Patroli selesai, semua normal"
}
```

**Validation Rules:**
- `status`: in:berlangsung,selesai,dibatalkan
- `catatan`: nullable, string

**Note:** Jika status diubah ke "selesai", `waktu_selesai` otomatis diisi

**Response:** `200 OK`

---

### 6.5 Scan Checkpoint

**Endpoint:** `POST /api/patrolis/{id}/scan`

**Content-Type:** `multipart/form-data` (jika ada foto) atau `application/json`

**Request Body (with photo):**
```
checkpoint_id: 1
latitude: -6.200000
longitude: 106.816666
catatan: Kondisi normal, tidak ada masalah
status: normal
foto: (file upload)
```

**Request Body (without photo):**
```json
{
  "checkpoint_id": 1,
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "catatan": "Kondisi normal",
  "status": "normal"
}
```

**Validation Rules:**
- `checkpoint_id`: required, exists in checkpoints
- `latitude`: nullable, string
- `longitude`: nullable, string
- `catatan`: nullable, string
- `foto`: nullable, image, max 2048KB
- `status`: in:normal,bermasalah

**Note:**
- `waktu_scan` otomatis set ke waktu sekarang
- Foto disimpan di `storage/app/public/patroli-photos`

**Response:** `201 Created`
```json
{
  "id": 1,
  "patroli_id": 1,
  "checkpoint_id": 1,
  "waktu_scan": "2026-01-14T20:15:00.000000Z",
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "catatan": "Kondisi normal",
  "foto": "patroli-photos/abc123.jpg",
  "status": "normal",
  "checkpoint": {
    "id": 1,
    "nama": "Pintu Masuk Utama",
    "kode": "CP-001"
  }
}
```

---

### 6.6 Delete Patroli

**Endpoint:** `DELETE /api/patrolis/{id}`

**Response:** `200 OK`
```json
{
  "message": "Patroli berhasil dihapus"
}
```

---

## Status Codes

- `200 OK` - Request successful
- `201 Created` - Resource created successfully
- `400 Bad Request` - Invalid request
- `401 Unauthorized` - Authentication required
- `403 Forbidden` - Permission denied
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation error
- `500 Internal Server Error` - Server error

## Multi-Tenancy

Sistem menggunakan `perusahaan_id` untuk isolasi data:

1. **Superadmin** (`role: superadmin`, `perusahaan_id: null`)
   - Dapat mengakses semua data
   - Dapat membuat/edit/hapus perusahaan
   - Dapat membuat user untuk perusahaan manapun

2. **Admin** (`role: admin`, `perusahaan_id: X`)
   - Hanya dapat mengakses data perusahaan sendiri
   - Dapat membuat user untuk perusahaan sendiri
   - Dapat mengelola lokasi, checkpoint, patroli

3. **Petugas** (`role: petugas`, `perusahaan_id: X`)
   - Hanya dapat mengakses data perusahaan sendiri
   - Dapat melakukan patroli dan scan checkpoint
   - Dapat melihat data lokasi dan checkpoint

## Rate Limiting

Default Laravel rate limiting:
- 60 requests per minute untuk authenticated users
- 10 requests per minute untuk guest users (login endpoint)

## File Upload

Foto patroli:
- Max size: 2MB
- Allowed types: jpg, jpeg, png, gif
- Storage path: `storage/app/public/patroli-photos`
- Access URL: `http://localhost:8000/storage/patroli-photos/{filename}`

Jangan lupa jalankan: `php artisan storage:link`
