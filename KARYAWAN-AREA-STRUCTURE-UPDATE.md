# Karyawan Area Structure Update

## PERUBAHAN STRUKTUR DATA ✅

### MASALAH SEBELUMNYA
- **Inkonsistensi Data**: Karyawan terhubung ke project melalui jabatan
- **Kompleksitas Query**: Harus melalui jabatan untuk mendapat project
- **Area Kerja Tidak Jelas**: Karyawan tidak punya relasi langsung ke area kerja
- **Jabatan Multi-Project**: Satu jabatan bisa di-assign ke multiple projects

### SOLUSI YANG DITERAPKAN
1. ✅ **Direct Project Relation**: Karyawan punya `project_id` langsung
2. ✅ **Area Kerja System**: Many-to-many relationship dengan areas
3. ✅ **Primary Area**: Setiap karyawan punya area utama
4. ✅ **Backward Compatibility**: Tetap support relasi melalui jabatan

## STRUKTUR DATABASE BARU ✅

### 1. **Tabel Karyawan** (Updated)
```sql
-- project_id sudah ada (tidak perlu ditambah)
karyawans:
  - id
  - perusahaan_id
  - user_id
  - project_id (EXISTING - relasi langsung ke project)
  - jabatan_id (tetap ada untuk backward compatibility)
  - ... (fields lainnya)
```

### 2. **Tabel Karyawan Areas** (NEW)
```sql
karyawan_areas:
  - id
  - karyawan_id (FK to karyawans)
  - area_id (FK to areas)
  - is_primary (boolean - area utama karyawan)
  - created_at
  - updated_at
  
UNIQUE(karyawan_id, area_id) -- Prevent duplicates
INDEX(karyawan_id, is_primary) -- Performance
INDEX(area_id) -- Performance
```

## MODEL RELATIONSHIPS ✅

### **Karyawan Model** (Updated)
```php
class Karyawan extends Model
{
    // Existing relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
    
    // NEW: Area kerja relationships
    public function areas()
    {
        return $this->belongsToMany(Area::class, 'karyawan_areas')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    
    public function primaryArea()
    {
        return $this->belongsToMany(Area::class, 'karyawan_areas')
                    ->wherePivot('is_primary', true)
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }
    
    // Helper attribute
    public function getPrimaryAreaAttribute()
    {
        return $this->primaryArea()->first();
    }
}
```

### **User Model** (Updated)
```php
class User extends Model
{
    public function getAccessibleProjectIds(): array
    {
        if ($this->isSuperAdmin()) {
            return Project::withoutGlobalScope('project_access')
                ->where('perusahaan_id', $this->perusahaan_id)
                ->pluck('id')->toArray();
        }
        
        // PRIORITAS 1: Gunakan project_id langsung dari karyawan
        if ($this->karyawan && $this->karyawan->project_id) {
            return [$this->karyawan->project_id];
        }
        
        // FALLBACK: Gunakan project dari jabatan
        if ($this->karyawan && $this->karyawan->jabatan) {
            return $this->karyawan->jabatan->projects()
                ->withoutGlobalScope('project_access')
                ->pluck('projects.id')->toArray();
        }
        
        return [];
    }
    
    public function getFirstAccessibleProject(): ?Project
    {
        // PRIORITAS 1: Gunakan project_id langsung dari karyawan
        if ($this->karyawan && $this->karyawan->project_id) {
            return Project::withoutGlobalScope('project_access')
                ->find($this->karyawan->project_id);
        }
        
        // FALLBACK: Gunakan method lama
        $projectIds = $this->getAccessibleProjectIds();
        
        if (!empty($projectIds)) {
            return Project::withoutGlobalScope('project_access')
                ->whereIn('id', $projectIds)->first();
        }
        
        return null;
    }
}
```

## MIGRATION RESULTS ✅

### **Execution Summary**:
```
✅ Created karyawan_areas pivot table
📊 Processing 4 active karyawans...
   ✅ Karyawan ID 1: 4 areas assigned
   ✅ Karyawan ID 3: 4 areas assigned  
   ✅ Karyawan ID 4: 4 areas assigned
   ✅ Karyawan ID 2: 4 areas assigned
✅ Successfully populated areas for 4/4 karyawans
```

### **Default Area Assignment**:
- Setiap karyawan aktif dengan `project_id` mendapat semua area di project mereka
- Area pertama otomatis menjadi `is_primary = true`
- Area lainnya menjadi `is_primary = false`

## BENEFITS ✅

### 1. **Data Consistency**
- ✅ **Direct Project Access**: Karyawan langsung terhubung ke project
- ✅ **Clear Area Assignment**: Jelas area mana yang bisa diakses karyawan
- ✅ **Primary Area**: Ada area utama untuk setiap karyawan

### 2. **Query Performance**
- ✅ **Simpler Queries**: Tidak perlu join melalui jabatan
- ✅ **Indexed Relations**: Proper indexing untuk performance
- ✅ **Direct Access**: `$karyawan->project_id` langsung tersedia

### 3. **Flexibility**
- ✅ **Multiple Areas**: Karyawan bisa bekerja di multiple areas
- ✅ **Primary Area**: Ada area utama untuk default operations
- ✅ **Easy Management**: Mudah assign/unassign areas

### 4. **Backward Compatibility**
- ✅ **Jabatan Relations**: Tetap support relasi melalui jabatan
- ✅ **Fallback Logic**: Jika project_id kosong, fallback ke jabatan
- ✅ **No Breaking Changes**: Existing code tetap berfungsi

## USAGE EXAMPLES ✅

### **Get Karyawan Areas**
```php
$karyawan = Karyawan::find(1);

// Get all areas
$areas = $karyawan->areas;

// Get primary area
$primaryArea = $karyawan->primary_area;

// Get areas with pivot data
$areasWithPivot = $karyawan->areas()->withPivot('is_primary')->get();
```

### **Assign New Area**
```php
// Assign area to karyawan
$karyawan->areas()->attach($areaId, ['is_primary' => false]);

// Set as primary area (unset others first)
$karyawan->areas()->updateExistingPivot($karyawan->areas->pluck('id'), ['is_primary' => false]);
$karyawan->areas()->updateExistingPivot($areaId, ['is_primary' => true]);
```

### **Check Area Access**
```php
// Check if karyawan has access to area
$hasAccess = $karyawan->areas()->where('area_id', $areaId)->exists();

// Get karyawan in specific area
$karyawansInArea = Area::find($areaId)->karyawans;
```

## API IMPACT ✅

### **Login Response** (Updated)
```json
{
  "user": {
    "project_id": 1,
    "project": {
      "id": 1,
      "nama": "Kantor Jakarta"
    }
  }
}
```

### **Project Access** (Improved)
- User sekarang mendapat project_id langsung dari karyawan
- Lebih akurat dan konsisten
- Fallback ke jabatan jika project_id kosong

## TESTING ✅

### **Verify Area Assignment**
```bash
# Check karyawan areas
SELECT k.nama_lengkap, p.nama as project, a.nama as area, ka.is_primary
FROM karyawans k
JOIN karyawan_areas ka ON k.id = ka.karyawan_id  
JOIN areas a ON ka.area_id = a.id
JOIN projects p ON k.project_id = p.id
ORDER BY k.id, ka.is_primary DESC;
```

### **Test API Access**
```bash
# Login and check project_id
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email": "edy@gmail.com", "password": "12345678"}'
```

## SUMMARY

**COMPLETED CHANGES**:
1. ✅ **Created karyawan_areas pivot table**
2. ✅ **Updated Karyawan model with area relationships**
3. ✅ **Updated User model for direct project access**
4. ✅ **Populated default areas for existing karyawans**
5. ✅ **Maintained backward compatibility**

**BENEFITS**:
- 🎯 **Direct project access** (no more jabatan complexity)
- 🎯 **Clear area assignments** (multiple areas per karyawan)
- 🎯 **Better performance** (simpler queries)
- 🎯 **Data consistency** (no more jabatan-project conflicts)

**STATUS**: **COMPLETED** ✅
Struktur data karyawan sekarang lebih konsisten dan fleksibel!