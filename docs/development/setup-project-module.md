# Setup Project Module - Nice Patrol

## Prerequisites
Pastikan Anda sudah menjalankan migration dasar aplikasi sebelumnya.

## Step-by-Step Installation

### 1. Run Migration untuk Jabatan dan Project User

```bash
# Run migration untuk tabel jabatans dan project_user
php artisan migrate
```

Migration yang akan dijalankan:
- `2026_01_14_140000_create_jabatans_table.php` - Tabel master jabatan
- `2026_01_14_140100_create_project_user_table.php` - Tabel relasi project dengan user

### 2. Seed Data Jabatan

```bash
# Seed data jabatan
php artisan db:seed --class=JabatanSeeder
```

Ini akan membuat 7 jabatan:
1. Manager
2. Admin
3. Finance
4. Representative Manager
5. Direktur
6. Admin & Support Coordinator
7. IT Support

### 3. Verifikasi Data

```bash
# Check tabel jabatans
php artisan tinker
>>> \App\Models\Jabatan::all();
```

Anda harus melihat 7 jabatan yang sudah di-seed.

### 4. Test Aplikasi

1. Login ke aplikasi
2. Buka menu **Project** di sidebar
3. Anda akan melihat list project dengan struktur jabatan
4. Jika belum ada karyawan di project, akan muncul pesan: "Belum ada jabatan di project ini"

## Troubleshooting

### Error: Table 'project_user' doesn't exist

**Solusi:**
```bash
php artisan migrate
```

### Error: Class 'Jabatan' not found

**Solusi:**
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Struktur Jabatan Tidak Muncul

**Penyebab:** Belum ada data di tabel `jabatans`

**Solusi:**
```bash
php artisan db:seed --class=JabatanSeeder
```

## Rollback (Jika Diperlukan)

```bash
# Rollback migration terakhir
php artisan migrate:rollback

# Rollback step tertentu
php artisan migrate:rollback --step=2
```

## Next Steps

Setelah setup selesai, Anda bisa:

1. **Tambah Project Baru**
   - Klik tombol "Tambah Project"
   - Isi form dan simpan
   - Project akan muncul dengan status "Belum ada jabatan"

2. **Assign Karyawan ke Project** (Future Feature)
   - Fitur ini akan dikembangkan untuk menambahkan karyawan ke project
   - Pilih jabatan untuk setiap karyawan
   - Struktur jabatan akan otomatis terupdate

3. **View Struktur Jabatan**
   - Setiap project card akan menampilkan struktur jabatan
   - Jumlah karyawan per jabatan
   - Total karyawan di project

## Database Schema

### Table: jabatans
```sql
CREATE TABLE jabatans (
    id BIGINT PRIMARY KEY,
    nama VARCHAR(255),
    kode VARCHAR(255) UNIQUE,
    deskripsi TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Table: project_user
```sql
CREATE TABLE project_user (
    id BIGINT PRIMARY KEY,
    project_id BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE CASCADE,
    jabatan_id BIGINT REFERENCES jabatans(id) ON DELETE CASCADE,
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(project_id, user_id, jabatan_id)
);
```

## Error Handling

Model Project sudah dilengkapi dengan error handling:

```php
// Jika tabel belum ada atau error, return empty array
public function getStrukturJabatanAttribute()
{
    try {
        // ... logic
    } catch (\Exception $e) {
        return [];
    }
}

// Jika tabel belum ada atau error, return 0
public function getTotalKaryawanAttribute()
{
    try {
        // ... logic
    } catch (\Exception $e) {
        return 0;
    }
}
```

Ini memastikan aplikasi tidak error meskipun migration belum dijalankan.

## Notes

- ✅ Aplikasi tetap berjalan meskipun migration belum dijalankan
- ✅ Struktur jabatan akan tampil "Belum ada jabatan" jika data kosong
- ✅ Error handling sudah diterapkan di model
- ✅ Multi-tenancy tetap terjaga dengan global scope
