# Modul Admin Perusahaan

## Overview

Modul Admin Perusahaan adalah interface terpisah untuk admin perusahaan mengelola data patroli mereka sendiri. Modul ini memiliki tampilan dan struktur folder yang berbeda dari modul Superadmin.

## Struktur Folder

```
app/Http/Controllers/Perusahaan/
├── DashboardController.php
├── ProfilController.php
├── LokasiController.php
├── CheckpointController.php
├── PatroliController.php
└── UserController.php

resources/views/perusahaan/
├── layouts/
│   └── app.blade.php
├── dashboard/
│   └── index.blade.php
├── profil/
│   └── index.blade.php
├── lokasis/
│   └── index.blade.php
├── checkpoints/
│   └── index.blade.php
├── patrolis/
│   ├── index.blade.php
│   └── show.blade.php
└── users/
    └── index.blade.php
```

## Fitur

### 1. Dashboard
- Statistik patroli (hari ini, minggu ini, bulan ini)
- Grafik aktivitas patroli
- Quick actions
- Aktivitas terbaru

**Route:** `/perusahaan/dashboard`

### 2. Profil Perusahaan
- Edit informasi perusahaan
- Upload logo perusahaan
- Statistik perusahaan

**Routes:**
- GET `/perusahaan/profil` - View profil
- PUT `/perusahaan/profil` - Update profil
- POST `/perusahaan/profil/upload-logo` - Upload logo

### 3. Lokasi
- CRUD lokasi kantor
- List dengan pagination
- Modal untuk create/edit
- SweetAlert confirmation untuk delete

**Routes:**
- GET `/perusahaan/lokasis` - List lokasi
- POST `/perusahaan/lokasis` - Create lokasi
- GET `/perusahaan/lokasis/{hash_id}/edit` - Get data untuk edit (JSON)
- PUT `/perusahaan/lokasis/{hash_id}` - Update lokasi
- DELETE `/perusahaan/lokasis/{hash_id}` - Delete lokasi

### 4. Checkpoint
- CRUD checkpoint
- Generate QR Code otomatis
- View & download QR Code
- Filter berdasarkan lokasi

**Routes:**
- GET `/perusahaan/checkpoints` - List checkpoint
- POST `/perusahaan/checkpoints` - Create checkpoint
- GET `/perusahaan/checkpoints/{hash_id}/edit` - Get data untuk edit (JSON)
- PUT `/perusahaan/checkpoints/{hash_id}` - Update checkpoint
- DELETE `/perusahaan/checkpoints/{hash_id}` - Delete checkpoint

**QR Code:**
- Auto-generate saat create: `CP-{UNIQUE_ID}`
- Library: qrcode.js (CDN)
- Download format: PNG

### 5. Petugas (Users)
- CRUD petugas security
- Role: admin atau petugas
- Password hashing otomatis
- Tidak bisa hapus akun sendiri

**Routes:**
- GET `/perusahaan/users` - List petugas
- POST `/perusahaan/users` - Create petugas
- GET `/perusahaan/users/{hash_id}/edit` - Get data untuk edit (JSON)
- PUT `/perusahaan/users/{hash_id}` - Update petugas
- DELETE `/perusahaan/users/{hash_id}` - Delete petugas

**Validasi:**
- Password minimal 8 karakter
- Email unique
- Role: admin atau petugas

### 6. Patroli
- View riwayat patroli (read-only)
- Filter by date range & petugas
- Detail patroli dengan checkpoint
- Statistik patroli

**Routes:**
- GET `/perusahaan/patrolis` - List patroli dengan filter
- GET `/perusahaan/patrolis/{hash_id}` - Detail patroli

**Filter:**
- Tanggal mulai
- Tanggal akhir
- Petugas

## Design System

### Color Scheme
- Primary: Sky Blue (`#0ea5e9`)
- Secondary: Blue (`#0284c7`)
- Gradient: `from-sky-500 to-blue-500`

### Layout
- Sidebar: Blue gradient dengan white text
- Active menu: White background dengan blue text
- Cards: White dengan shadow-sm
- Buttons: Sky-600 dengan hover effect

## Multi-Tenancy

Semua data otomatis ter-filter berdasarkan `perusahaan_id` melalui:

1. **Global Scope** di model (Lokasi, Checkpoint, Patroli)
2. **Manual Filter** di controller (User)
3. **Auto-assign** `perusahaan_id` saat create

## Security

1. **Middleware:** `PerusahaanMiddleware` - Hanya admin/petugas yang bisa akses
2. **Hash ID:** Semua URL menggunakan hash_id, bukan integer ID
3. **Ownership Validation:** Global scope otomatis validasi ownership
4. **CSRF Protection:** Semua form menggunakan @csrf
5. **Password Hashing:** Otomatis dengan Hash::make()

## JavaScript Libraries

1. **SweetAlert2** - Notifikasi & confirmation
2. **QRCode.js** - Generate QR Code
3. **Tailwind CSS** - Styling
4. **Font Awesome** - Icons

## API Endpoints (AJAX)

Beberapa endpoint return JSON untuk AJAX:

```javascript
// Get data untuk edit modal
GET /perusahaan/lokasis/{hash_id}/edit
GET /perusahaan/checkpoints/{hash_id}/edit
GET /perusahaan/users/{hash_id}/edit

// Response format
{
    "id": 1,
    "nama": "...",
    "alamat": "...",
    // ... other fields
}
```

## Testing

Login dengan akun perusahaan:
- Email: `abb@nicepatrol.id` atau `bsp@nicepatrol.id`
- Password: `password`

## Future Enhancements

1. Export PDF/Excel untuk laporan patroli
2. Real-time notification
3. Dashboard analytics dengan chart
4. Mobile app integration
5. Geofencing untuk checkpoint
