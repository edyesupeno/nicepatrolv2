# Project Module Documentation

## Overview
Module Project digunakan untuk mengelola project-project keamanan yang dimiliki oleh setiap perusahaan. Setiap project berelasi dengan Kantor dan memiliki timezone, durasi, dan status aktif.

## Features
- ✅ CRUD Project (Create, Read, Update, Delete)
- ✅ Multi-tenancy isolation (perusahaan_id)
- ✅ Hash ID untuk URL routing
- ✅ SweetAlert2 untuk notifikasi
- ✅ Filter by Kantor
- ✅ Timezone support (WIB, WITA, WIT)
- ✅ Status aktif/tidak aktif
- ✅ Tanggal mulai dan selesai

## Database Schema

### Table: `projects`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| perusahaan_id | bigint | Foreign key ke perusahaans |
| kantor_id | bigint | Foreign key ke kantors |
| nama | string | Nama project |
| timezone | string | Timezone (Asia/Jakarta, Asia/Makassar, Asia/Jayapura) |
| tanggal_mulai | date | Tanggal mulai project |
| tanggal_selesai | date (nullable) | Tanggal selesai project |
| deskripsi | text (nullable) | Deskripsi project |
| is_active | boolean | Status aktif (default: true) |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

## Model Relations

### Project Model
```php
// Belongs to Perusahaan
public function perusahaan(): BelongsTo

// Belongs to Kantor
public function kantor(): BelongsTo
```

### Kantor Model
```php
// Has many Projects
public function projects(): HasMany
```

## Routes
| Method | URI | Name | Controller |
|--------|-----|------|------------|
| GET | /perusahaan/projects | perusahaan.projects.index | ProjectController@index |
| POST | /perusahaan/projects | perusahaan.projects.store | ProjectController@store |
| GET | /perusahaan/projects/{hash_id}/edit | perusahaan.projects.edit | ProjectController@edit |
| PUT | /perusahaan/projects/{hash_id} | perusahaan.projects.update | ProjectController@update |
| DELETE | /perusahaan/projects/{hash_id} | perusahaan.projects.destroy | ProjectController@destroy |

## Multi-Tenancy Implementation

### Global Scope
```php
protected static function booted(): void
{
    static::addGlobalScope('perusahaan', function (Builder $builder) {
        if (auth()->check() && auth()->user()->perusahaan_id) {
            $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
        }
    });
}
```

### Auto-assign perusahaan_id
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    // Auto-assign perusahaan_id
    $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
    
    Project::create($validated);
}
```

## Validation Rules

### Create & Update
```php
[
    'kantor_id' => 'required|exists:kantors,id',
    'nama' => 'required|string|max:255',
    'timezone' => 'required|string',
    'tanggal_mulai' => 'required|date',
    'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
    'deskripsi' => 'nullable|string',
    'is_active' => 'nullable|boolean',
]
```

## UI Features

### Index Page
- Card grid layout (3 columns)
- Filter by Kantor dropdown
- Total project count
- Status badge (Aktif/Selesai)
- Quick actions: Edit, Delete, View Detail
- Pagination

### Modal Create/Edit
- Kantor dropdown (only active kantors)
- Nama project input
- Timezone select (WIB/WITA/WIT)
- Tanggal mulai & selesai date picker
- Deskripsi textarea
- Status select (Aktif/Tidak Aktif)

### Notifications
- Success: "Project berhasil ditambahkan/diupdate/dihapus"
- Confirmation: "Yakin ingin menghapus?" (before delete)
- Error: "Gagal memuat data project"

## Sample Data
Seeder creates 5 sample projects:
- 3 projects for PT ABB (Jakarta - WIB)
- 2 projects for PT BSP (Makassar - WITA)

## Security Checklist
- ✅ Global scope perusahaan_id
- ✅ Auto-assign perusahaan_id on create
- ✅ Hash ID untuk URL
- ✅ CSRF protection
- ✅ Validation error dalam Bahasa Indonesia
- ✅ SweetAlert2 confirmation untuk delete

## Next Steps
- [ ] Implement detail view
- [ ] Add Area module (berelasi dengan Project)
- [ ] Add project statistics to dashboard
- [ ] Add export functionality
- [ ] Add project timeline view
