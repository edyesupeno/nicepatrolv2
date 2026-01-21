# Komponen Payroll - Project Selection & Nilai Maksimal Feature

## 📋 **Overview**
Implementasi fitur project selection dan nilai maksimal untuk komponen payroll. User dapat memilih apakah komponen berlaku untuk semua project (global) atau project spesifik, serta menambahkan batasan nilai maksimal untuk tipe perhitungan per hari.

## 🎯 **Features Implemented**

### 1. **Project Selection**
- ✅ **Global Scope**: Komponen berlaku untuk semua project di perusahaan
- ✅ **Project Specific**: Komponen hanya berlaku untuk project tertentu
- ✅ **Filter by Project**: Filter komponen berdasarkan project di halaman index
- ✅ **Automatic perusahaan_id**: Otomatis mengambil perusahaan_id dari project yang dipilih

### 2. **Nilai Maksimal**
- ✅ **Per Day Calculation Limit**: Batasan nilai maksimal untuk "Per Hari Masuk" dan "Lembur Per Hari"
- ✅ **Dynamic UI**: Field nilai maksimal hanya muncul untuk tipe perhitungan yang relevan
- ✅ **Calculation Logic**: Otomatis membatasi nilai jika melebihi maksimal

### 3. **UI/UX Enhancement**
- ✅ **Project Column**: Menampilkan project scope di tabel (Global/Project Name)
- ✅ **Project Filter**: Dropdown filter untuk project di search section
- ✅ **Smart Form**: Radio button untuk memilih scope + dropdown project
- ✅ **Visual Indicators**: Icon dan badge untuk membedakan global vs project-specific

## 🔧 **Technical Implementation**

### **Form Structure**
```html
<!-- Project Scope Selection -->
○ Semua Project (Global) - project_id = NULL
○ Project Spesifik - project_id = selected_project_id
  └── [Dropdown Project List]

<!-- Nilai Maksimal (only for per-day types) -->
Nilai Maksimal Per Bulan: [300000] Rp
```

### **Business Logic**
```php
// Global component (project_id = NULL)
$komponen = KomponenPayroll::create([
    'project_id' => null, // Berlaku untuk semua project
    'perusahaan_id' => auth()->user()->perusahaan_id
]);

// Project-specific component
$komponen = KomponenPayroll::create([
    'project_id' => $selectedProjectId, // Hanya untuk project ini
    'perusahaan_id' => auth()->user()->perusahaan_id
]);
```

### **Filtering Logic**
```php
// Filter komponen berdasarkan project
if ($request->project_id === 'global') {
    $query->whereNull('project_id'); // Hanya global
} elseif ($request->project_id) {
    $query->where('project_id', $request->project_id); // Project spesifik
}
// Kosong = semua (global + project-specific)
```

## 📊 **Usage Examples**

### **Scenario 1: Global Component**
```
Komponen: Tunjangan Transport
Scope: Global (Semua Project)
Nilai: Rp 200,000/bulan

→ Berlaku untuk: Project A, Project B, Project C, dll
```

### **Scenario 2: Project-Specific Component**
```
Komponen: Tunjangan Site Migas
Scope: Project Spesifik (Project Migas Offshore)
Nilai: Rp 500,000/bulan

→ Berlaku untuk: Hanya karyawan di Project Migas Offshore
```

### **Scenario 3: Per-Day with Maximum Limit**
```
Komponen: Uang Makan Lembur
Scope: Project Spesifik (Project Construction)
Tipe: Lembur Per Hari
Nilai: Rp 25,000/hari
Nilai Maksimal: Rp 600,000/bulan

Karyawan A (20 hari lembur): 25,000 × 20 = 500,000 ✅
Karyawan B (30 hari lembur): 25,000 × 30 = 750,000 → dibatasi 600,000 ⚠️
```

## 🎨 **UI Features**

### **Table Display**
| Nama | Kode | Project | Jenis | Nilai |
|------|------|---------|-------|-------|
| Transport | TRANS | 🌐 Global | Tunjangan | Rp 200,000 |
| Site Migas | MIGAS | 🏢 Project Migas | Tunjangan | Rp 500,000 |
| Uang Makan | MAKAN | 🏗️ Project Construction | Tunjangan | Rp 15,000/hari<br>Max: Rp 400,000 |

### **Filter Options**
```
[Search] [Project: All ▼] [Status: All ▼] [Kategori: All ▼] [Cari] [Reset]

Project Options:
- Semua Project (show all)
- Global Only (show only global components)
- Project A (show only Project A components)
- Project B (show only Project B components)
```

### **Form Behavior**
- **Global Selected**: Project dropdown disabled
- **Specific Selected**: Project dropdown enabled + required
- **Per Day Type**: Nilai maksimal field appears
- **Other Types**: Nilai maksimal field hidden

## 🔒 **Data Safety & Validation**

### **Validation Rules**
```php
'project_scope' => 'required|in:global,specific',
'project_id' => 'nullable|exists:projects,id',

// Custom validation
if ($scope === 'specific' && empty($project_id)) {
    return error('Project wajib dipilih untuk cakupan spesifik');
}
```

### **Backward Compatibility**
- ✅ Existing components remain global (project_id = NULL)
- ✅ No data migration needed
- ✅ All existing functionality preserved
- ✅ Gradual adoption possible

## 📁 **Files Modified**

### **Controllers**
- `app/Http/Controllers/Perusahaan/KomponenPayrollController.php`
  - Added project filtering in index()
  - Enhanced validation in store() and update()
  - Added project data to view

### **Views**
- `resources/views/perusahaan/payroll/komponen.blade.php`
  - Added project filter dropdown
  - Added project column to table
  - Added project scope selection in form
  - Enhanced JavaScript for dynamic behavior

### **Models**
- `app/Models/KomponenPayroll.php` (already updated in previous implementation)

## 🧪 **Testing Scenarios**

### **Test Cases**
1. ✅ Create global component (project_id = NULL)
2. ✅ Create project-specific component
3. ✅ Filter by "Global Only"
4. ✅ Filter by specific project
5. ✅ Edit component and change scope
6. ✅ Validation when project_id missing for specific scope
7. ✅ UI behavior for project scope selection

### **Manual Testing Steps**
1. Go to `/perusahaan/komponen-payroll`
2. Test project filter dropdown
3. Click "Tambah Komponen"
4. Test project scope radio buttons
5. Verify project dropdown enable/disable
6. Create both global and project-specific components
7. Verify table display shows correct project info

## 🚀 **Future Enhancements**

### **Potential Improvements**
- Bulk assign components to multiple projects
- Copy component from one project to another
- Project-based component templates
- Analytics: component usage by project
- Mass update project assignments

## ✅ **Completion Status**

- [x] Project selection in form
- [x] Project filtering in index
- [x] Project column in table
- [x] Validation for project scope
- [x] UI/UX enhancements
- [x] JavaScript dynamic behavior
- [x] Backward compatibility
- [x] Documentation

**Status: COMPLETED** ✅

The project selection feature is now fully implemented with proper validation, filtering, and user-friendly interface.