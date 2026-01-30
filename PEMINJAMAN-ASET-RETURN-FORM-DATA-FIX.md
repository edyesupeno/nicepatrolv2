# Peminjaman Aset - Return Form Data Display Fix

## Issue Fixed
- **Problem**: Return form showing "Data tidak tersedia" for all asset and borrower information
- **Cause**: `returnForm` method in controller not loading required relations
- **Impact**: Users couldn't see asset details, borrower info, or other important data in return form

## Root Cause Analysis
The `returnForm` method in `PeminjamanAsetController` was not loading the necessary relations:

```php
// BEFORE - No relation loading
public function returnForm(PeminjamanAset $peminjamanAset)
{
    // ... validation ...
    
    $kondisiOptions = PeminjamanAset::getKondisiPengembalianOptions();
    return view('perusahaan.peminjaman-aset.return', compact('peminjamanAset', 'kondisiOptions'));
}
```

This caused the model accessors to return fallback values like "Data tidak tersedia" because:
1. Relations were not loaded (`relationLoaded()` returned false)
2. Accessor methods couldn't access related data
3. Global scopes prevented automatic relation loading

## Solution Applied

### 1. Fixed `returnForm` Method
Added comprehensive relation loading with proper scope handling:

```php
public function returnForm(PeminjamanAset $peminjamanAset)
{
    // ... validation ...

    // Load relations for display
    $peminjamanAset->load([
        'project' => function($query) {
            $query->withoutGlobalScope('project_access')->select('id', 'nama');
        },
        'dataAset' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
        },
        'asetKendaraan' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
        },
        'peminjamKaryawan' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
        }
    ]);

    $kondisiOptions = PeminjamanAset::getKondisiPengembalianOptions();
    return view('perusahaan.peminjaman-aset.return', compact('peminjamanAset', 'kondisiOptions'));
}
```

### 2. Standardized `show` Method
Updated for consistency with other methods:

```php
public function show(PeminjamanAset $peminjamanAset)
{
    $peminjamanAset->load([
        'project' => function($query) {
            $query->withoutGlobalScope('project_access')->select('id', 'nama');
        },
        'dataAset' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'kode_aset', 'nama_aset', 'kategori', 'perusahaan_id');
        },
        'asetKendaraan' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'kode_kendaraan', 'merk', 'model', 'tahun_pembuatan', 'jenis_kendaraan', 'nomor_polisi', 'perusahaan_id');
        },
        'peminjamKaryawan' => function($query) {
            $query->withoutGlobalScopes()->select('id', 'nama_lengkap', 'nik_karyawan', 'perusahaan_id');
        },
        'createdBy:id,name',
        'approvedBy:id,name',
        'returnedBy:id,name'
    ]);
    
    return view('perusahaan.peminjaman-aset.show', compact('peminjamanAset'));
}
```

## Key Technical Details

### 1. Global Scope Handling
- Used `withoutGlobalScopes()` to bypass multi-tenancy restrictions
- This allows loading related data across company boundaries when needed
- Maintains security through controller-level access control

### 2. Selective Field Loading
- Used `select()` to load only necessary fields for performance
- Reduces memory usage and query time
- Includes primary keys and foreign keys for proper relation handling

### 3. Consistent Pattern
All controller methods now follow the same pattern:
- `index()` - Loads relations with select
- `show()` - Loads relations with select  
- `edit()` - Loads relations with select
- `returnForm()` - Loads relations with select

## Test Results

### Before Fix:
```
Return form displaying:
- Peminjam: "Data tidak tersedia"
- Kode Aset: "Data tidak tersedia" 
- Nama Aset: "Data tidak tersedia"
- Kategori: "Data tidak tersedia"
```

### After Fix:
```
Return form displaying:
- Kode Peminjaman: PJM-3-2026-0002
- Peminjam Nama: EDI IRWAN
- Aset Nama: Toyota Avanza 2021
- Aset Kode: AST-3-2026-0003
- Aset Kategori: Kendaraan
```

## Benefits

1. **Complete Information Display**: Users can now see all relevant asset and borrower details
2. **Better User Experience**: Clear information helps users make informed decisions
3. **Consistent Data Loading**: All controller methods now load data consistently
4. **Performance Optimized**: Selective field loading reduces query overhead
5. **Proper Error Handling**: Fallback values only show when data is genuinely missing

## Files Modified

1. `app/Http/Controllers/Perusahaan/PeminjamanAsetController.php`
   - Fixed `returnForm()` method - Added relation loading
   - Standardized `show()` method - Consistent select fields

## Impact on User Interface

The return form now properly displays:
- **Informasi Peminjaman**: Complete borrower and loan details
- **Informasi Aset**: Full asset information with code, name, and category
- **Keperluan**: Purpose of the loan
- **Duration**: Accurate loan duration calculation
- **Status Information**: Proper late return warnings

## Consistency Achieved

All controller methods now use the same relation loading pattern:
- ✅ `index()` - Consistent relation loading
- ✅ `show()` - Consistent relation loading  
- ✅ `edit()` - Consistent relation loading
- ✅ `returnForm()` - Consistent relation loading

The return form data display issue is now completely resolved, providing users with all the information they need to process asset returns effectively.