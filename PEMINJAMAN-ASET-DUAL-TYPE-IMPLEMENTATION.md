# Peminjaman Aset Dual Type Implementation

## Overview
Enhanced the peminjaman aset system to support two types of assets: regular assets (DataAset) and vehicles (AsetKendaraan) with searchable select dropdowns for easy asset selection.

## Features Implemented

### 1. **Dual Asset Type Support**
- Added `aset_type` enum field: `data_aset` or `aset_kendaraan`
- Polymorphic relationship support for both asset types
- Dynamic asset loading based on selected type

### 2. **Searchable Asset Selection**
- Real-time search API endpoint for both asset types
- Dynamic dropdown population based on asset type
- Search functionality across multiple fields (name, code, license plate for vehicles)

### 3. **Enhanced User Interface**
- Type selection dropdown with clear labels
- Searchable asset dropdown with detailed information
- Asset information display panel showing code and category
- Responsive design with proper validation

## Database Changes

### Migration: `add_asset_type_to_peminjaman_asets_table`
```php
// Added fields
$table->enum('aset_type', ['data_aset', 'aset_kendaraan'])->default('data_aset');
$table->foreignId('aset_kendaraan_id')->nullable()->constrained('aset_kendaraans');
$table->foreignId('data_aset_id')->nullable()->change(); // Made nullable

// Added indexes
$table->index(['aset_type', 'status_peminjaman']);
$table->index(['aset_kendaraan_id', 'status_peminjaman']);
```

## Model Updates

### PeminjamanAset Model
**New Fillable Fields:**
```php
'aset_type', 'aset_kendaraan_id'
```

**New Relationships:**
```php
public function asetKendaraan()
{
    return $this->belongsTo(AsetKendaraan::class);
}
```

**New Accessors:**
```php
public function getAsetInfoAttribute()      // Returns the actual asset object
public function getAsetNamaAttribute()     // Returns asset name regardless of type
public function getAsetKodeAttribute()     // Returns asset code regardless of type
public function getAsetKategoriAttribute() // Returns asset category regardless of type
public function getAsetTypeLabelAttribute() // Returns human-readable type label
```

**Enhanced Search Scope:**
- Now searches across both DataAset and AsetKendaraan
- Includes vehicle-specific fields (license plate, vehicle name)

### AsetKendaraan Model
**New Accessor:**
```php
public function getNamaKendaraanAttribute()
{
    return "{$this->merk} {$this->model} ({$this->tahun_pembuatan})";
}
```

## Controller Updates

### PeminjamanAsetController
**New Methods:**
```php
public function searchAsets(Request $request)
```
- API endpoint for dynamic asset search
- Supports both asset types with different search criteria
- Returns formatted data for dropdown population

**Enhanced Existing Methods:**
- `index()`: Now loads both asset types with proper relationships
- `create()`: Provides both asset type options and data
- `store()`: Validates and handles both asset types
- `show()`: Loads appropriate asset relationship based on type
- All relationship loading methods now include both asset types

**New Route:**
```php
Route::get('peminjaman-aset-search-asets', [PeminjamanAsetController::class, 'searchAsets'])
    ->name('peminjaman-aset.search-asets');
```

## View Updates

### Create Form (`create.blade.php`)
**New UI Elements:**
1. **Asset Type Selection:**
   ```html
   <select name="aset_type" id="aset_type" required>
       <option value="">Pilih Tipe Aset</option>
       <option value="data_aset">Aset</option>
       <option value="aset_kendaraan">Kendaraan</option>
   </select>
   ```

2. **Dynamic Asset Search:**
   ```html
   <select name="aset_search" id="aset_search" disabled>
       <option value="">Pilih tipe aset terlebih dahulu</option>
   </select>
   ```

3. **Hidden ID Fields:**
   ```html
   <input type="hidden" name="data_aset_id" id="data_aset_id">
   <input type="hidden" name="aset_kendaraan_id" id="aset_kendaraan_id">
   ```

4. **Asset Information Display:**
   ```html
   <div id="aset-info" class="mt-3 p-3 bg-gray-50 rounded-lg hidden">
       <div class="grid grid-cols-2 gap-4 text-sm">
           <div>Kode: <span id="aset-kode"></span></div>
           <div>Kategori: <span id="aset-kategori"></span></div>
       </div>
   </div>
   ```

**Enhanced JavaScript:**
- Asset type change handler
- Dynamic asset loading via AJAX
- Search functionality with debouncing
- Asset information display
- Form validation for both asset types

### Display Views
**Updated all views to use new accessors:**
- `index.blade.php`: Uses `$peminjaman->aset_nama`, `aset_kode`, `aset_kategori`
- `jatuh-tempo.blade.php`: Uses `$peminjaman->aset_nama`
- `return.blade.php`: Uses `$peminjamanAset->aset_info` for dynamic asset access
- `bukti-pdf.blade.php`: Uses new accessors for PDF generation

## API Functionality

### Search Assets Endpoint
**URL:** `GET /perusahaan/peminjaman-aset-search-asets`

**Parameters:**
- `type`: `data_aset` or `aset_kendaraan`
- `search`: Search term (optional)

**Response Format:**
```json
[
    {
        "id": 1,
        "text": "AST-1-2026-0001 - Laptop Dell Latitude 5520",
        "kode": "AST-1-2026-0001",
        "nama": "Laptop Dell Latitude 5520",
        "kategori": "Elektronik",
        "extra": null
    },
    {
        "id": 2,
        "text": "KND-1-2026-0001 - Toyota Avanza (2021) (B 1234 ABC)",
        "kode": "KND-1-2026-0001",
        "nama": "Toyota Avanza (2021)",
        "kategori": "Kendaraan - mobil",
        "extra": "B 1234 ABC"
    }
]
```

## Validation Rules

### Store/Update Methods
```php
'aset_type' => 'required|in:data_aset,aset_kendaraan',
'data_aset_id' => 'required_if:aset_type,data_aset|nullable|exists:data_asets,id',
'aset_kendaraan_id' => 'required_if:aset_type,aset_kendaraan|nullable|exists:aset_kendaraans,id',
```

## Search Capabilities

### DataAset Search
- `nama_aset` (asset name)
- `kode_aset` (asset code)
- `kategori` (category)

### AsetKendaraan Search
- `merk` (brand)
- `model` (model)
- `kode_kendaraan` (vehicle code)
- `nomor_polisi` (license plate)

## User Experience Improvements

### 1. **Progressive Enhancement**
- Type selection enables asset dropdown
- Real-time search with 300ms debouncing
- Visual feedback during loading states

### 2. **Clear Information Display**
- Asset type badges in listings
- Detailed asset information in selection
- Consistent naming across all views

### 3. **Responsive Design**
- Works on desktop and mobile
- Touch-friendly dropdowns
- Proper validation feedback

## Security & Performance

### 1. **Multi-tenancy Maintained**
- All queries respect company isolation
- Global scopes properly bypassed for relationships
- User access control preserved

### 2. **Performance Optimizations**
- Limited search results (20 items max)
- Efficient database queries with proper indexing
- Lazy loading of asset relationships

### 3. **Input Validation**
- Server-side validation for all inputs
- Client-side validation for better UX
- CSRF protection maintained

## Testing Results

✅ **Database Migration:** Successfully applied
✅ **Model Relationships:** Both asset types load correctly
✅ **Search API:** Returns proper results for both types
✅ **Form Validation:** Handles both asset types properly
✅ **View Rendering:** All views display correct asset information
✅ **Multi-tenancy:** Company isolation maintained

## Usage Instructions

### For Users:
1. Select "Aset" or "Kendaraan" from the type dropdown
2. Search for assets in the searchable dropdown
3. Select the desired asset to see detailed information
4. Complete the rest of the form as usual

### For Developers:
- Use `$peminjaman->aset_nama` for display names
- Use `$peminjaman->aset_info` to access the actual asset object
- Both DataAset and AsetKendaraan relationships are available
- Search API can be extended for additional asset types

## Future Enhancements

### Potential Improvements:
1. **Asset Availability Check:** Prevent double-booking of assets
2. **Asset History:** Track usage patterns and maintenance schedules
3. **Bulk Operations:** Support multiple asset selection
4. **Advanced Filters:** Filter by asset condition, location, etc.
5. **Asset Recommendations:** Suggest similar assets when unavailable

This implementation provides a robust, scalable foundation for managing different types of assets in the peminjaman system while maintaining excellent user experience and system performance.