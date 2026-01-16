# Quick Start Guide - Nice Patrol System

## 🚀 Setup dalam 5 Menit

### 1. Start PostgreSQL

```bash
docker compose up -d
```

Tunggu beberapa detik sampai PostgreSQL siap.

### 2. Run Migrations & Seed Data

```bash
php artisan migrate:fresh --seed
```

### 3. Start Laravel Server

```bash
php artisan serve
```

Server akan berjalan di: **http://localhost:8000**

---

## ✅ Test API

### Login sebagai Superadmin

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@nicepatrol.id",
    "password": "password"
  }'
```

Simpan `token` dari response.

### Get Current User

```bash
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### List Perusahaan

```bash
curl -X GET http://localhost:8000/api/perusahaans \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## 📱 Test dengan Postman

1. Import collection dari file `POSTMAN_COLLECTION.md`
2. Buat environment dengan variable:
   - `base_url`: `http://localhost:8000/api`
   - `token`: (akan diisi otomatis setelah login)
3. Test endpoint Login
4. Copy token ke environment variable
5. Test endpoint lainnya

---

## 🔐 Akun Default

| Role | Email | Password | Perusahaan |
|------|-------|----------|------------|
| Superadmin | superadmin@nicepatrol.id | password | - |
| Admin ABB | abb@nicepatrol.id | password | PT ABB |
| Admin BSP | bsp@nicepatrol.id | password | PT BSP |

---

## 📊 Database Info

- **Host:** localhost:5432
- **Database:** patrol_db
- **Username:** patrol_user
- **Password:** patrol_password

### Connect via psql

```bash
docker exec -it patrol_postgres psql -U patrol_user -d patrol_db
```

### Useful SQL Commands

```sql
-- List all tables
\dt

-- Show users
SELECT id, name, email, role, perusahaan_id FROM users;

-- Show perusahaan
SELECT * FROM perusahaans;

-- Exit
\q
```

---

## 🎯 Workflow Example

### Scenario: Admin ABB Setup Lokasi & Checkpoint

#### 1. Login sebagai Admin ABB

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "abb@nicepatrol.id",
    "password": "password"
  }'
```

#### 2. Create Lokasi

```bash
curl -X POST http://localhost:8000/api/lokasis \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Gedung Kantor Pusat",
    "alamat": "Jl. Sudirman No. 123, Jakarta",
    "latitude": "-6.200000",
    "longitude": "106.816666",
    "is_active": true
  }'
```

#### 3. Create Checkpoint

```bash
curl -X POST http://localhost:8000/api/checkpoints \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_id": 1,
    "nama": "Pintu Masuk Utama",
    "kode": "CP-001",
    "deskripsi": "Checkpoint di pintu masuk utama gedung",
    "urutan": 1,
    "is_active": true
  }'
```

#### 4. Create User Petugas

```bash
curl -X POST http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Petugas Budi",
    "email": "budi@nicepatrol.id",
    "password": "password",
    "role": "petugas",
    "is_active": true
  }'
```

---

### Scenario: Petugas Melakukan Patroli

#### 1. Login sebagai Petugas

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "budi@nicepatrol.id",
    "password": "password"
  }'
```

#### 2. Start Patroli

```bash
curl -X POST http://localhost:8000/api/patrolis \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_id": 1,
    "catatan": "Patroli malam shift 1"
  }'
```

Response akan berisi `patroli_id`, misalnya: `1`

#### 3. Scan Checkpoint (tanpa foto)

```bash
curl -X POST http://localhost:8000/api/patrolis/1/scan \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "checkpoint_id": 1,
    "latitude": "-6.200000",
    "longitude": "106.816666",
    "catatan": "Kondisi normal, tidak ada masalah",
    "status": "normal"
  }'
```

#### 4. Selesaikan Patroli

```bash
curl -X PUT http://localhost:8000/api/patrolis/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "status": "selesai",
    "catatan": "Patroli selesai, semua checkpoint normal"
  }'
```

#### 5. Lihat Detail Patroli

```bash
curl -X GET http://localhost:8000/api/patrolis/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## 🛠️ Troubleshooting

### PostgreSQL tidak bisa connect

```bash
# Check container status
docker ps

# Restart container
docker compose restart

# View logs
docker compose logs postgres
```

### Migration error

```bash
# Drop all tables and re-migrate
php artisan migrate:fresh --seed
```

### Clear cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### Storage link error

```bash
# Remove old link
rm public/storage

# Create new link
php artisan storage:link
```

---

## 📚 Next Steps

1. Baca **API_DOCUMENTATION.md** untuk detail lengkap semua endpoint
2. Baca **POSTMAN_COLLECTION.md** untuk testing dengan Postman
3. Baca **DEPLOYMENT.md** untuk deploy ke production
4. Baca **README.md** untuk informasi lengkap sistem

---

## 🔥 Tips

### View All Routes

```bash
php artisan route:list
```

### Laravel Tinker (REPL)

```bash
php artisan tinker

# Test query
>>> App\Models\User::all();
>>> App\Models\Perusahaan::with('users')->get();
```

### View Logs

```bash
tail -f storage/logs/laravel.log
```

### Database Query Log

Tambahkan di `AppServiceProvider`:

```php
DB::listen(function($query) {
    Log::info($query->sql, $query->bindings);
});
```

---

## 💡 Development Tips

### Auto-reload dengan Laravel Pail

```bash
php artisan pail
```

### Format code dengan Pint

```bash
./vendor/bin/pint
```

### Run tests

```bash
php artisan test
```

---

Selamat mencoba! 🎉
