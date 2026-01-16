# Postman Collection - Nice Patrol API

## Setup

1. Buat environment baru di Postman dengan variables:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (akan diisi otomatis setelah login)

## 1. Authentication

### Login Superadmin
```
POST {{base_url}}/login
Content-Type: application/json

{
  "email": "superadmin@nicepatrol.id",
  "password": "password"
}
```

Simpan `token` dari response ke environment variable.

### Login Admin ABB
```
POST {{base_url}}/login
Content-Type: application/json

{
  "email": "abb@nicepatrol.id",
  "password": "password"
}
```

### Get Current User
```
GET {{base_url}}/me
Authorization: Bearer {{token}}
```

### Logout
```
POST {{base_url}}/logout
Authorization: Bearer {{token}}
```

## 2. Perusahaan (Superadmin Only)

### List Perusahaan
```
GET {{base_url}}/perusahaans
Authorization: Bearer {{token}}
```

### Create Perusahaan
```
POST {{base_url}}/perusahaans
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "nama": "PT Security Indonesia",
  "kode": "SECINDO",
  "alamat": "Jakarta Selatan",
  "telepon": "021-9999999",
  "email": "info@secindo.co.id",
  "is_active": true
}
```

### Get Perusahaan Detail
```
GET {{base_url}}/perusahaans/1
Authorization: Bearer {{token}}
```

### Update Perusahaan
```
PUT {{base_url}}/perusahaans/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "nama": "PT Security Indonesia Updated",
  "is_active": true
}
```

### Delete Perusahaan
```
DELETE {{base_url}}/perusahaans/1
Authorization: Bearer {{token}}
```

## 3. Users

### List Users
```
GET {{base_url}}/users
Authorization: Bearer {{token}}
```

### Create User (Admin)
```
POST {{base_url}}/users
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "Petugas Budi",
  "email": "budi@nicepatrol.id",
  "password": "password",
  "role": "petugas",
  "perusahaan_id": 1,
  "is_active": true
}
```

### Get User Detail
```
GET {{base_url}}/users/1
Authorization: Bearer {{token}}
```

### Update User
```
PUT {{base_url}}/users/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "name": "Petugas Budi Updated",
  "is_active": true
}
```

### Delete User
```
DELETE {{base_url}}/users/1
Authorization: Bearer {{token}}
```

## 4. Lokasi

### List Lokasi
```
GET {{base_url}}/lokasis
Authorization: Bearer {{token}}
```

### Create Lokasi
```
POST {{base_url}}/lokasis
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "nama": "Gedung A",
  "alamat": "Jl. Sudirman No. 123",
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "is_active": true
}
```

### Get Lokasi Detail
```
GET {{base_url}}/lokasis/1
Authorization: Bearer {{token}}
```

### Update Lokasi
```
PUT {{base_url}}/lokasis/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "nama": "Gedung A - Lantai 1",
  "is_active": true
}
```

### Delete Lokasi
```
DELETE {{base_url}}/lokasis/1
Authorization: Bearer {{token}}
```

## 5. Checkpoint

### List Checkpoint
```
GET {{base_url}}/checkpoints
Authorization: Bearer {{token}}
```

### Create Checkpoint
```
POST {{base_url}}/checkpoints
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "lokasi_id": 1,
  "nama": "Pintu Masuk Utama",
  "kode": "CP-001",
  "deskripsi": "Checkpoint di pintu masuk utama gedung",
  "urutan": 1,
  "is_active": true
}
```

### Get Checkpoint Detail
```
GET {{base_url}}/checkpoints/1
Authorization: Bearer {{token}}
```

### Update Checkpoint
```
PUT {{base_url}}/checkpoints/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "nama": "Pintu Masuk Utama - Updated",
  "urutan": 2,
  "is_active": true
}
```

### Delete Checkpoint
```
DELETE {{base_url}}/checkpoints/1
Authorization: Bearer {{token}}
```

## 6. Patroli

### List Patroli
```
GET {{base_url}}/patrolis
Authorization: Bearer {{token}}
```

### Start Patroli
```
POST {{base_url}}/patrolis
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "lokasi_id": 1,
  "catatan": "Patroli malam shift 1"
}
```

### Get Patroli Detail
```
GET {{base_url}}/patrolis/1
Authorization: Bearer {{token}}
```

### Update Patroli Status
```
PUT {{base_url}}/patrolis/1
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "status": "selesai",
  "catatan": "Patroli selesai, semua normal"
}
```

### Scan Checkpoint (dengan foto)
```
POST {{base_url}}/patrolis/1/scan
Authorization: Bearer {{token}}
Content-Type: multipart/form-data

checkpoint_id: 1
latitude: -6.200000
longitude: 106.816666
catatan: Kondisi normal, tidak ada masalah
status: normal
foto: (pilih file gambar)
```

### Scan Checkpoint (tanpa foto)
```
POST {{base_url}}/patrolis/1/scan
Authorization: Bearer {{token}}
Content-Type: application/json

{
  "checkpoint_id": 1,
  "latitude": "-6.200000",
  "longitude": "106.816666",
  "catatan": "Kondisi normal",
  "status": "normal"
}
```

### Delete Patroli
```
DELETE {{base_url}}/patrolis/1
Authorization: Bearer {{token}}
```

## Testing Flow

### Scenario 1: Superadmin membuat perusahaan baru

1. Login sebagai superadmin
2. Create perusahaan baru
3. Create admin untuk perusahaan tersebut
4. Logout
5. Login sebagai admin perusahaan baru
6. Verify hanya bisa akses data perusahaan sendiri

### Scenario 2: Admin setup lokasi dan checkpoint

1. Login sebagai admin (abb@nicepatrol.id)
2. Create lokasi baru
3. Create beberapa checkpoint di lokasi tersebut
4. Verify checkpoint ter-urut dengan baik

### Scenario 3: Petugas melakukan patroli

1. Login sebagai petugas
2. Start patroli baru di lokasi tertentu
3. Scan checkpoint pertama dengan foto
4. Scan checkpoint kedua dengan catatan
5. Update status patroli menjadi "selesai"
6. Get detail patroli untuk melihat semua scan

### Scenario 4: Multi-tenancy test

1. Login sebagai admin ABB
2. Create lokasi untuk ABB
3. Logout
4. Login sebagai admin BSP
5. List lokasi - verify hanya melihat lokasi BSP
6. Try to access lokasi ABB by ID - should fail or return 404
