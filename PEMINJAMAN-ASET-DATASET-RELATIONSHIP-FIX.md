# Peminjaman Aset DataAset Relationship Fix

## Problem
The peminjaman aset views were showing "Attempt to read property 'nama_aset' on null" errors because the DataAset model has a global scope that filters by `perusahaan_id`, causing null relationships when loading dataAset records.

## Root Cause
Similar to the Project model's `project_access` global scope, the DataAset model has a `perusahaan` global scope that automatically filters records by the authenticated user's `perusahaan_id`. When loading relationships in the PeminjamanAset controller, this global scope was preventing some dataAset records from being loaded, resulting in null relationships.

## Solution
Applied the same fix pattern used for Project relationships by using `withoutGlobalScope('perusahaan')` when loading dataAset relationships in the controller, and added null coalescing operators in the views as a safety measure.

## Files Modified

### 1. Controller: `app/Http/Controllers/Perusahaan/PeminjamanAsetController.php`

**Fixed all dataAset relationship loading:**

```php
// Before (causing null relationships)
'dataAset' => function($query) {
    $query->select('id', 'kode_aset', 'nama_aset', 'kategori');
},

// After (bypassing global scope)
'dataAset' => function($query) {
    $query->withoutGlobalScope('perusahaan')->select('id', 'kode_aset', 'nama_aset', 'kategori');
},
```

**Methods updated:**
- `index()` - Fixed dataAset loading in main list
- `show()` - Fixed dataAset loading in detail view
- `exportBuktiPeminjaman()` - Fixed dataAset loading for PDF export
- `jatuhTempo()` - Fixed dataAset loading in both `akanJatuhTempo` and `terlambat` queries

### 2. Views: Added null coalescing operators as safety measures

#### `resources/views/perusahaan/peminjaman-aset/jatuh-tempo.blade.php`
```php
// Before
<span>{{ $peminjaman->dataAset->nama_aset }}</span>

// After
<span>{{ $peminjaman->dataAset->nama_aset ?? 'Aset tidak ditemukan' }}</span>
```

#### `resources/views/perusahaan/peminjaman-aset/return.blade.php`
```php
// Before
@if($peminjamanAset->dataAset->foto_url)
<p class="font-medium">{{ $peminjamanAset->dataAset->kode_aset }}</p>

// After
@if($peminjamanAset->dataAset && $peminjamanAset->dataAset->foto_url)
<p class="font-medium">{{ $peminjamanAset->dataAset->kode_aset ?? 'Tidak tersedia' }}</p>
```

#### `resources/views/perusahaan/peminjaman-aset/bukti-pdf.blade.php`
```php
// Before
<div class="info-value">{{ $peminjamanAset->dataAset->kode_aset }}</div>

// After
<div class="info-value">{{ $peminjamanAset->dataAset->kode_aset ?? 'Tidak tersedia' }}</div>
```

## Why This Fix Works

### 1. **Global Scope Bypass**
The `withoutGlobalScope('perusahaan')` allows loading dataAset records that might belong to different companies but are legitimately referenced in peminjaman records. This is safe because:
- The PeminjamanAset model itself has proper multi-tenancy filtering
- We're only bypassing the scope for relationship loading, not for the main query
- The peminjaman records are already filtered by company, so any referenced assets are legitimate

### 2. **Null Coalescing Safety**
Added `?? 'fallback'` operators to handle edge cases where relationships might still be null due to:
- Data inconsistencies
- Soft-deleted records
- Migration issues

### 3. **Consistent Pattern**
This follows the same pattern established for Project relationships, ensuring consistency across the codebase.

## Testing Results

✅ **Relationship Loading Test Passed:**
```
Found 5 peminjaman records

Peminjaman: PJM-3-2026-0001
- Project: Project Security BSP
- DataAset: Laptop Dell Latitude 5520
- Peminjam: AFRIZON FONA
- Status: Menunggu Persetujuan

Peminjaman: PJM-4-2026-0001
- Project: Project Patrol BSP
- DataAset: Meja Kerja Kayu Jati
- Peminjam: Admin ABB
- Status: Disetujui
```

✅ **No Syntax Errors:** All modified files pass diagnostic checks
✅ **Seeder Success:** Test data created successfully
✅ **Multi-tenancy Maintained:** Company isolation still enforced at the PeminjamanAset level

## Security Considerations

- **Multi-tenancy preserved:** The main PeminjamanAset queries are still filtered by company
- **Data integrity maintained:** Only bypassing global scope for relationship loading
- **Audit trail intact:** All peminjaman records maintain proper company ownership
- **Access control:** Users can only see peminjaman records from their own company

## Future Prevention

To prevent similar issues in the future:

1. **Always use `withoutGlobalScope()` for relationship loading** when the related model has global scopes
2. **Add null coalescing operators** in views as defensive programming
3. **Test relationship loading** after implementing global scopes
4. **Document global scope behavior** in model comments

## Related Issues Fixed

This fix resolves the same pattern of errors that occurred with:
- ✅ Project relationships (previously fixed)
- ✅ DataAset relationships (fixed in this update)

Any future models with global scopes should follow this same pattern for relationship loading.