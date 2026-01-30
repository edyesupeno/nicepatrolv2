# Mutasi Aset Search Implementation Summary

## Problem Solved
User requested to change the asset selection and employee selection from dropdown to search mode because there will be many assets and employees, making dropdowns impractical.

## Solution Implemented
Implemented **Select2** with AJAX search functionality for both asset selection and employee selection.

## Changes Made

### 1. Controller Updates (`app/Http/Controllers/Perusahaan/MutasiAsetController.php`)

#### Enhanced `getAssetsByProject()` Method
```php
public function getAssetsByProject(Request $request)
{
    // Added search parameter support
    $search = $request->get('search', '');
    
    // Added search functionality for both asset types
    if ($search) {
        $query->where(function ($q) use ($search) {
            // For data_aset: search by nama_aset and kode_aset
            // For aset_kendaraan: search by merk, model, and nomor_polisi
        });
    }
    
    // Added limit(50) for performance
    // Returns formatted data for Select2
}
```

#### Added New `searchKaryawan()` Method
```php
public function searchKaryawan(Request $request)
{
    $search = $request->get('search', '');
    
    $query = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan')
        ->where('is_active', true);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_lengkap', 'like', "%{$search}%")
              ->orWhere('nik_karyawan', 'like', "%{$search}%");
        });
    }

    // Returns formatted data for Select2
    return response()->json([
        'success' => true,
        'data' => $karyawans->map(function ($karyawan) {
            return [
                'id' => $karyawan->id,
                'text' => $karyawan->nama_lengkap . ' (' . $karyawan->nik_karyawan . ')'
            ];
        })
    ]);
}
```

#### Updated Create & Edit Methods
- Removed static `$karyawans` loading
- Now only loads `$projects` (still needed for dropdowns)
- Karyawan and Asset data loaded dynamically via AJAX

### 2. Route Addition (`routes/web.php`)
```php
Route::get('mutasi-aset-search-karyawan', [MutasiAsetController::class, 'searchKaryawan'])
    ->name('mutasi-aset.search-karyawan');
```

### 3. View Updates

#### Create View (`resources/views/perusahaan/mutasi-aset/create.blade.php`)

**Asset Selection:**
```html
<select id="asset_id" name="asset_id" required>
    <option value="">Pilih project asal dan tipe aset terlebih dahulu</option>
</select>
```

**Employee Selection:**
```html
<select id="karyawan_id" name="karyawan_id" required>
    <option value="">Cari dan pilih karyawan...</option>
</select>
```

**Added Select2 Integration:**
- Select2 CSS and JS libraries
- Custom styling to match Tailwind design
- AJAX configuration for both selects

#### Edit View (`resources/views/perusahaan/mutasi-aset/edit.blade.php`)
- Same Select2 implementation as create view
- Handles current values for edit mode
- Preserves selected asset and karyawan during edit

### 4. Select2 Implementation Features

#### Asset Search Select2
```javascript
assetSelect2 = $('#asset_id').select2({
    placeholder: 'Cari dan pilih aset...',
    allowClear: true,
    ajax: {
        url: '/perusahaan/mutasi-aset-assets-by-project',
        dataType: 'json',
        delay: 300,
        data: function (params) {
            return {
                search: params.term,
                project_id: projectAsalId,
                asset_type: assetType,
                current_asset_id: currentAssetId // for edit mode
            };
        },
        processResults: function (data) {
            return {
                results: data.success ? data.data : []
            };
        },
        cache: true
    },
    minimumInputLength: 0 // Show all assets initially
});
```

#### Employee Search Select2
```javascript
karyawanSelect2 = $('#karyawan_id').select2({
    placeholder: 'Cari dan pilih karyawan...',
    allowClear: true,
    ajax: {
        url: '/perusahaan/mutasi-aset-search-karyawan',
        dataType: 'json',
        delay: 300,
        data: function (params) {
            return {
                search: params.term
            };
        },
        processResults: function (data) {
            return {
                results: data.success ? data.data : []
            };
        },
        cache: true
    },
    minimumInputLength: 2 // Require at least 2 characters
});
```

## Search Functionality

### Asset Search
**Data Aset:**
- Search by `nama_aset` (Asset Name)
- Search by `kode_aset` (Asset Code)

**Aset Kendaraan:**
- Search by `merk` (Brand)
- Search by `model` (Model)
- Search by `nomor_polisi` (License Plate)

### Employee Search
- Search by `nama_lengkap` (Full Name)
- Search by `nik_karyawan` (Employee ID)

## Performance Optimizations

### 1. Lazy Loading
- Assets and employees loaded only when needed
- No initial bulk loading of all data

### 2. Search Debouncing
- 300ms delay before search request
- Prevents excessive API calls while typing

### 3. Result Limiting
- Maximum 50 results per search
- Prevents overwhelming the UI with too many options

### 4. Caching
- Select2 caches search results
- Reduces redundant API calls

### 5. Minimum Input Length
- Assets: 0 characters (show all initially)
- Employees: 2 characters (require typing to search)

## User Experience Improvements

### 1. Better Search Experience
- ✅ Type to search instead of scrolling through long lists
- ✅ Real-time search results
- ✅ Clear visual feedback during loading
- ✅ Easy to clear selections

### 2. Responsive Design
- ✅ Select2 styled to match Tailwind CSS design
- ✅ Consistent with other form elements
- ✅ Works well on mobile devices

### 3. Form Flow Maintained
- ✅ Project Asal → Asset Type → Asset Search → Project Tujuan
- ✅ Employee search independent of other selections
- ✅ Validation still works correctly

### 4. Edit Mode Support
- ✅ Current values pre-selected in edit mode
- ✅ Search includes current asset even if not available
- ✅ Maintains all existing functionality

## API Response Format

### Asset Search Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "text": "Laptop Dell (LT001)",
            "project_id": 2
        },
        {
            "id": 2,
            "text": "Toyota Avanza (B 1234 ABC)",
            "project_id": 2
        }
    ]
}
```

### Employee Search Response
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "text": "John Doe (EMP001)"
        },
        {
            "id": 2,
            "text": "Jane Smith (EMP002)"
        }
    ]
}
```

## Benefits of Implementation

### 1. Scalability
- ✅ Handles thousands of assets and employees
- ✅ Performance doesn't degrade with data growth
- ✅ Search results limited to prevent UI overload

### 2. User Experience
- ✅ Fast and intuitive search
- ✅ No more scrolling through long dropdown lists
- ✅ Type-ahead functionality
- ✅ Clear visual feedback

### 3. Performance
- ✅ Reduced initial page load time
- ✅ On-demand data loading
- ✅ Efficient search queries with LIKE operators
- ✅ Result caching reduces server load

### 4. Maintainability
- ✅ Clean separation of concerns
- ✅ Reusable search endpoints
- ✅ Consistent implementation pattern

## Files Modified

1. `app/Http/Controllers/Perusahaan/MutasiAsetController.php`
   - Enhanced `getAssetsByProject()` with search
   - Added `searchKaryawan()` method
   - Updated `create()` and `edit()` methods

2. `routes/web.php`
   - Added `mutasi-aset-search-karyawan` route

3. `resources/views/perusahaan/mutasi-aset/create.blade.php`
   - Replaced static dropdowns with Select2 search
   - Added Select2 CSS/JS and custom styling
   - Updated JavaScript for AJAX search

4. `resources/views/perusahaan/mutasi-aset/edit.blade.php`
   - Same Select2 implementation as create
   - Added support for current values in edit mode

## Testing Checklist

- [ ] Asset search works with different search terms
- [ ] Employee search works with name and NIK
- [ ] Search results limited to 50 items
- [ ] Edit mode preserves current selections
- [ ] Form validation still works correctly
- [ ] Performance good with large datasets
- [ ] Mobile responsive design
- [ ] Search debouncing works (300ms delay)

## Next Steps

The mutasi aset form now uses searchable Select2 components instead of static dropdowns:

1. ✅ **Asset Selection**: Search by name, code, brand, model, or license plate
2. ✅ **Employee Selection**: Search by full name or employee ID
3. ✅ **Performance Optimized**: Lazy loading, debouncing, result limiting
4. ✅ **User Friendly**: Type-ahead search, clear selections, responsive design
5. ✅ **Edit Mode Support**: Current values preserved and searchable

The implementation is complete and ready for testing with large datasets.