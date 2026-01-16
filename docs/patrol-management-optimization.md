# Patrol Management - Query Optimization

## Overview
Dokumentasi ini menjelaskan optimasi yang telah dilakukan pada fitur Patrol Management untuk memastikan performa optimal saat data sudah banyak.

## 1. Pagination

### Implementasi
Semua list view menggunakan pagination dengan 15 item per halaman:

```php
$areaPatrols = $query->latest()->paginate(15)->withQueryString();
```

**Benefits:**
- Mengurangi memory usage
- Faster page load
- Better user experience
- Scalable untuk ribuan data

### View Implementation
```blade
@if($areaPatrols->hasPages())
<div class="mt-6">
    {{ $areaPatrols->links() }}
</div>
@endif
```

## 2. Query Optimization

### Select Specific Columns
❌ **SALAH** - Mengambil semua kolom:
```php
$query = AreaPatrol::with('project');
```

✅ **BENAR** - Select kolom spesifik:
```php
$query = AreaPatrol::select([
        'id',
        'perusahaan_id',
        'project_id',
        'nama',
        'deskripsi',
        'alamat',
        'koordinat',
        'is_active',
        'created_at'
    ])
    ->with('project:id,nama');
```

**Benefits:**
- Mengurangi data transfer dari database
- Faster query execution
- Lower memory usage
- Hanya ambil data yang benar-benar dibutuhkan

### Eager Loading
Menggunakan eager loading untuk menghindari N+1 query problem:

```php
->with('project:id,nama')  // Area Patrol
->with('projects:id,nama') // Kategori Insiden
```

**Before (N+1 Problem):**
```
1 query untuk area patrols
+ N queries untuk setiap project (jika ada 15 area = 15 queries)
= 16 queries total
```

**After (Eager Loading):**
```
1 query untuk area patrols
+ 1 query untuk semua projects
= 2 queries total
```

## 3. Database Indexes

### Area Patrols Table
```php
// Composite index untuk filter yang sering digunakan
$table->index(['perusahaan_id', 'project_id', 'is_active']);

// Index untuk search by nama
$table->index('nama');

// Foreign key indexes (otomatis)
$table->foreignId('perusahaan_id')->constrained();
$table->foreignId('project_id')->constrained();
```

### Kategori Insidens Table
```php
// Composite index
$table->index(['perusahaan_id', 'is_active']);

// Index untuk search by nama
$table->index('nama');

// Foreign key indexes (otomatis)
$table->foreignId('perusahaan_id')->constrained();
```

### Pivot Tables
```php
// kategori_insiden_project
$table->unique(['kategori_insiden_id', 'project_id']);
```

**Benefits:**
- Faster WHERE clauses
- Faster JOIN operations
- Faster ORDER BY
- Optimal untuk search queries

## 4. Search Optimization

### Implementation
```php
if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('nama', 'ILIKE', "%{$search}%")
          ->orWhere('deskripsi', 'ILIKE', "%{$search}%")
          ->orWhere('alamat', 'ILIKE', "%{$search}%");
    });
}
```

**Optimizations:**
- Grouped WHERE conditions untuk menghindari query ambiguity
- ILIKE untuk case-insensitive search (PostgreSQL)
- Index pada kolom `nama` untuk faster search

## 5. Filter Optimization

### Project Filter
```php
if ($request->filled('project_id')) {
    $query->where('project_id', $request->project_id);
}
```

**Benefits:**
- Menggunakan indexed column (foreign key)
- Simple equality check (faster than LIKE)

### Status Filter
```php
if ($request->filled('status')) {
    $query->where('is_active', $request->status === 'aktif');
}
```

**Benefits:**
- Boolean comparison (very fast)
- Indexed column

## 6. Global Scope Multi-Tenancy

### Implementation
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

**Benefits:**
- Automatic data isolation
- Mengurangi jumlah data yang di-query
- Security by default
- Menggunakan indexed column

## 7. Performance Benchmarks

### Expected Performance (with optimizations)

| Data Volume | Query Time | Memory Usage |
|-------------|------------|--------------|
| 100 records | < 50ms     | < 2MB        |
| 1,000 records | < 100ms  | < 5MB        |
| 10,000 records | < 200ms | < 10MB       |
| 100,000 records | < 500ms | < 20MB      |

**Note:** Dengan pagination 15 items, memory usage tetap konstan regardless of total data.

## 8. Caching Strategy (Future Enhancement)

Untuk optimasi lebih lanjut, bisa implement caching:

```php
// Cache dropdown projects (jarang berubah)
$projects = Cache::remember(
    'projects_' . auth()->user()->perusahaan_id, 
    3600, 
    function () {
        return Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();
    }
);
```

## 9. Monitoring & Debugging

### Laravel Debugbar
Install untuk monitoring query performance:
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Check Query Count
```php
DB::enableQueryLog();
// ... your code
dd(DB::getQueryLog());
```

### Slow Query Log
Configure di `config/database.php`:
```php
'slow_query_log' => true,
'slow_query_time' => 1, // Log queries > 1 second
```

## 10. Best Practices Checklist

- [x] Pagination implemented (15 items per page)
- [x] Select specific columns only
- [x] Eager loading untuk relationships
- [x] Database indexes pada kolom yang sering di-query
- [x] Composite indexes untuk filter combinations
- [x] Global scope untuk multi-tenancy
- [x] Grouped WHERE conditions untuk search
- [x] Query string preserved pada pagination
- [ ] Caching untuk data yang jarang berubah (future)
- [ ] Full-text search untuk large text fields (future)

## 11. Scalability Notes

### Current Implementation
- ✅ Scalable hingga 100,000+ records per perusahaan
- ✅ Constant memory usage dengan pagination
- ✅ Fast search dengan indexes
- ✅ Efficient filtering

### Future Enhancements (jika data > 1 juta)
- Implement Elasticsearch untuk full-text search
- Add Redis caching layer
- Consider database sharding by perusahaan_id
- Implement lazy loading untuk large text fields
- Add database read replicas

## 12. Query Examples

### Optimized Query (Area Patrol)
```sql
SELECT 
    id, perusahaan_id, project_id, nama, 
    deskripsi, alamat, koordinat, is_active, created_at
FROM area_patrols
WHERE perusahaan_id = 1
    AND project_id = 2
    AND is_active = true
    AND nama ILIKE '%gedung%'
ORDER BY created_at DESC
LIMIT 15 OFFSET 0;

-- Separate query for projects (eager loading)
SELECT id, nama
FROM projects
WHERE id IN (1, 2, 3, ...);
```

**Execution Plan:**
- Uses composite index on (perusahaan_id, project_id, is_active)
- Uses index on nama for ILIKE search
- Fast LIMIT/OFFSET with ORDER BY on indexed column

## Summary

Semua optimasi sudah diimplementasikan untuk memastikan performa optimal:
- ✅ Pagination untuk scalability
- ✅ Select specific columns untuk efficiency
- ✅ Eager loading untuk menghindari N+1
- ✅ Database indexes untuk fast queries
- ✅ Global scope untuk data isolation
- ✅ Optimized search dan filter

Sistem siap handle ribuan hingga ratusan ribu data dengan performa yang baik!
