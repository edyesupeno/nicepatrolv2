# Nice Patrol v2 - Sistem Patroli Keamanan SaaS

Sistem manajemen patroli keamanan berbasis SaaS dengan multi-tenancy untuk perusahaan security.

## 🚀 Quick Start

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Start PostgreSQL
docker compose up -d

# 4. Run migrations & seed
php artisan migrate:fresh --seed

# 5. Start server
php artisan serve
```

**Dashboard:** http://localhost:8000

## 📚 Dokumentasi Lengkap

Semua dokumentasi tersedia di folder `docs/`:

- **[docs/README.md](docs/README.md)** - Dokumentasi lengkap sistem
- **[docs/development/setup.md](docs/development/setup.md)** - Setup development
- **[docs/api/endpoints.md](docs/api/endpoints.md)** - API documentation
- **[docs/api/postman-collection.md](docs/api/postman-collection.md)** - Postman collection
- **[docs/deployment/production.md](docs/deployment/production.md)** - Production deployment

## 🔐 Akun Default

| Role | Email | Password |
|------|-------|----------|
| Superadmin | superadmin@nicepatrol.id | password |
| Admin ABB | abb@nicepatrol.id | password |
| Admin BSP | bsp@nicepatrol.id | password |

## 🛠️ Tech Stack

- Laravel 12
- PostgreSQL 16 (Docker)
- Laravel Sanctum
- Hashids (URL obfuscation)
- SweetAlert2
- TailwindCSS

## 📋 Features

- ✅ Multi-tenancy dengan isolasi data
- ✅ Role-based access (Superadmin/Admin/Petugas)
- ✅ Hash ID untuk URL (bukan integer ID)
- ✅ SweetAlert2 untuk notifikasi
- ✅ RESTful API dengan Sanctum
- ✅ Real-time patrol tracking
- ✅ Photo upload & GPS tracking

## 🔒 Security

- CSRF protection
- XSS protection
- SQL injection protection
- Rate limiting
- Hash ID untuk URL obfuscation
- Password hashing dengan bcrypt

## 📖 Project Standards

Project ini mengikuti standards yang didefinisikan di `.kiro/steering/project-standards.md`:

- **URL Routing**: Menggunakan Hash ID, bukan integer ID
- **Notifications**: SweetAlert2, bukan browser alert/confirm
- **Documentation**: Semua file MD di folder `docs/`
- **Code Style**: PSR-12, type hints, meaningful names
- **Security**: CSRF, XSS protection, rate limiting

## 🤝 Contributing

Sebelum commit, pastikan:

- [ ] Semua URL menggunakan hash_id
- [ ] Tidak ada alert/confirm browser
- [ ] Semua notifikasi menggunakan SweetAlert2
- [ ] Code sudah di-format: `./vendor/bin/pint`
- [ ] Validation error dalam Bahasa Indonesia

## 📝 License

Proprietary - Nice Patrol System

---

**Dibuat dengan ❤️ untuk kebutuhan patroli keamanan modern**
