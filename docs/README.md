# Sistem Patroli Keamanan - Nice Patrol v2

Sistem manajemen patroli keamanan dengan multi-tenancy untuk perusahaan security.

## 🚀 Quick Start

```bash
# 1. Start PostgreSQL
docker compose up -d

# 2. Run migrations & seed
php artisan migrate:fresh --seed

# 3. Start server
php artisan serve
```

API: **http://localhost:8000/api**

## 📋 Fitur Utama

- ✅ **Multi-Tenancy** - Isolasi data per perusahaan
- 👥 **Role Management** - Superadmin, Admin, Petugas
- 📍 **Manajemen Lokasi** - Kelola lokasi patroli
- 🎯 **Checkpoint System** - Titik pemeriksaan terstruktur
- 📱 **Real-time Patrol** - Tracking patroli dengan GPS
- 📸 **Photo Upload** - Dokumentasi kondisi checkpoint
- 🔐 **API Authentication** - Laravel Sanctum

## 🔐 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Superadmin | superadmin@nicepatrol.id | password |
| Admin ABB | abb@nicepatrol.id | password |
| Admin BSP | bsp@nicepatrol.id | password |

## 📚 Dokumentasi

- **[QUICK_START.md](QUICK_START.md)** - Setup & testing dalam 5 menit
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Detail lengkap semua endpoint
- **[POSTMAN_COLLECTION.md](POSTMAN_COLLECTION.md)** - Collection untuk Postman
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Guide deploy production

## 🛠️ Tech Stack

- Laravel 12
- PostgreSQL 16 (Docker)
- Laravel Sanctum
- PHP 8.2+

## 📊 Database Schema

```
perusahaans (companies)
├── users (superadmin/admin/petugas)
├── lokasis (locations)
│   └── checkpoints
└── patrolis (patrol records)
    └── patroli_details (checkpoint scans)
```

## 🔄 API Endpoints

### Authentication
- `POST /api/login` - Login
- `POST /api/logout` - Logout
- `GET /api/me` - Current user

### Resources (CRUD)
- `/api/perusahaans` - Perusahaan (superadmin only)
- `/api/users` - Users
- `/api/lokasis` - Lokasi
- `/api/checkpoints` - Checkpoint
- `/api/patrolis` - Patroli

### Special
- `POST /api/patrolis/{id}/scan` - Scan checkpoint

## 🎯 Multi-Tenancy

Sistem menggunakan `perusahaan_id` untuk isolasi data:

- **Superadmin**: Akses semua data, kelola perusahaan
- **Admin**: Akses data perusahaan sendiri, kelola users & lokasi
- **Petugas**: Akses data perusahaan sendiri, lakukan patroli

## 🐳 Docker

```bash
# Start
docker compose up -d

# Stop
docker compose down

# Logs
docker compose logs -f

# PostgreSQL CLI
docker exec -it patrol_postgres psql -U patrol_user -d patrol_db
```

## 🧪 Testing API

### cURL Example

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"superadmin@nicepatrol.id","password":"password"}'

# Get user info
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Postman

Lihat **[POSTMAN_COLLECTION.md](POSTMAN_COLLECTION.md)** untuk collection lengkap.

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/Api/  # API Controllers
│   └── Models/                # Eloquent Models
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Data seeders
├── routes/
│   └── api.php               # API routes
├── docker-compose.yml        # PostgreSQL setup
└── storage/
    └── app/public/
        └── patroli-photos/   # Uploaded photos
```

## 🔧 Useful Commands

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# View routes
php artisan route:list

# Tinker (REPL)
php artisan tinker

# Format code
./vendor/bin/pint

# View logs
tail -f storage/logs/laravel.log
```

## 📝 License

Proprietary - Nice Patrol System

---

**Dibuat dengan ❤️ untuk kebutuhan patroli keamanan modern**
