# Questionnaire Integration Summary

## Perubahan yang Dilakukan

### 1. Label "Bertemu" → "Nama Petugas"
✅ **Sebelum**: "Security Officer Penerima"
✅ **Sesudah**: "Nama Petugas"

Perubahan ini membuat label lebih umum dan tidak terbatas pada security officer saja.

### 2. Tombol Kuesioner Dinamis
✅ **Kondisi**: Muncul hanya jika project memiliki `enable_questionnaire = true`
✅ **Posisi**: Di sebelah tombol "Simpan Data Tamu"
✅ **Warna**: Hijau untuk membedakan dari tombol utama

### 3. Modal Kuesioner Interaktif
✅ **Loading State**: Indikator loading saat mengambil data
✅ **Error Handling**: Pesan error jika kuesioner tidak tersedia
✅ **Dynamic Questions**: Mendukung berbagai tipe pertanyaan
✅ **Responsive Design**: Bekerja di desktop dan mobile

## Fitur Kuesioner

### Tipe Pertanyaan yang Didukung:
1. **Text Input** - Input teks sederhana
2. **Textarea** - Input teks panjang
3. **Radio Button** - Pilihan tunggal
4. **Checkbox** - Pilihan multiple
5. **Select Dropdown** - Pilihan dari dropdown

### Validasi:
- ✅ **Required Fields**: Pertanyaan wajib divalidasi
- ✅ **Dynamic Validation**: Berdasarkan pengaturan per pertanyaan
- ✅ **Client-side Validation**: Validasi real-time di browser

### Data Flow:
1. **Load Questionnaire**: Ambil dari `/perusahaan/buku-tamu/kuesioner-by-area`
2. **Render Questions**: Generate form berdasarkan tipe pertanyaan
3. **Save Answers**: Simpan jawaban dalam format JSON
4. **Submit with Form**: Kirim bersama data tamu utama

## Files yang Dimodifikasi

### 1. Frontend (Blade Template)
**File**: `resources/views/perusahaan/buku-tamu/create.blade.php`

**Perubahan**:
- ✅ Label "Nama Petugas" 
- ✅ Tombol "Isi Kuesioner" dengan kondisi
- ✅ Modal kuesioner lengkap
- ✅ JavaScript functions untuk kuesioner
- ✅ Dynamic form rendering

### 2. Backend (Controller)
**File**: `app/Http/Controllers/Perusahaan/BukuTamuController.php`

**Perubahan**:
- ✅ Method `getKuesionerByArea()` - Ambil kuesioner berdasarkan area
- ✅ Enhanced `store()` method - Handle jawaban kuesioner JSON
- ✅ Updated validation rules - Support kuesioner_answers sebagai string

### 3. Routes
**File**: `routes/web.php`

**Perubahan**:
- ✅ Route baru: `GET /perusahaan/buku-tamu/kuesioner-by-area`

## JavaScript Functions Baru

### Core Functions:
```javascript
openQuestionnaire()        // Buka modal kuesioner
closeQuestionnaire()       // Tutup modal kuesioner
loadQuestionnaire()        // Load data dari API
renderQuestionnaire()      // Render form questions
createQuestionElement()    // Generate HTML per pertanyaan
saveQuestionnaire()        // Simpan jawaban ke form utama
```

### Question Types Handling:
- `text` → Input field
- `textarea` → Textarea field  
- `radio` → Radio buttons
- `checkbox` → Checkboxes
- `select` → Dropdown select

## API Endpoint Baru

### GET `/perusahaan/buku-tamu/kuesioner-by-area`

**Parameters**:
- `project_id` (required) - ID project
- `area_id` (required) - ID area

**Response Success**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "judul": "Kuesioner Tamu Area Kantor",
    "deskripsi": "Pertanyaan untuk tamu di area kantor",
    "pertanyaans": [
      {
        "id": 1,
        "pertanyaan": "Apakah Anda puas dengan pelayanan?",
        "tipe_jawaban": "radio",
        "opsi_jawaban": ["Sangat Puas", "Puas", "Kurang Puas"],
        "is_required": true,
        "urutan": 1
      }
    ]
  }
}
```

**Response Error**:
```json
{
  "success": false,
  "message": "Kuesioner tidak ditemukan untuk project dan area ini"
}
```

## Database Integration

### Tabel yang Terlibat:
1. **`kuesioner_tamus`** - Master kuesioner per area
2. **`pertanyaan_tamus`** - Pertanyaan dalam kuesioner
3. **`jawaban_kuesioner_tamus`** - Jawaban tamu

### Data Flow:
```
Project + Area → KuesionerTamu → PertanyaanTamu → JawabanKuesionerTamu
```

## User Experience

### Flow Penggunaan:
1. **Pilih Project** → Deteksi apakah ada kuesioner
2. **Pilih Area** → Load kuesioner spesifik area
3. **Isi Data Tamu** → Form utama (Simple/MIGAS)
4. **Klik "Isi Kuesioner"** → Modal terbuka
5. **Jawab Pertanyaan** → Validasi real-time
6. **Simpan Jawaban** → Kembali ke form utama
7. **Submit Form** → Simpan semua data

### Visual Indicators:
- ✅ **Tombol Hijau**: Kuesioner tersedia
- ✅ **Loading Spinner**: Sedang memuat
- ✅ **Error Message**: Kuesioner tidak tersedia
- ✅ **Success Toast**: Jawaban tersimpan

## Testing Instructions

### 1. Setup Test Data
```sql
-- Pastikan project memiliki enable_questionnaire = true
UPDATE projects SET enable_questionnaire = true WHERE id = 4;

-- Pastikan ada kuesioner untuk area tertentu
SELECT * FROM kuesioner_tamus WHERE project_id = 4 AND area_id = 1;
```

### 2. Manual Testing
1. **Pilih Project dengan Questionnaire**: Project Patrol ABB
2. **Pilih Area**: Area yang memiliki kuesioner
3. **Verifikasi Tombol**: Tombol "Isi Kuesioner" muncul
4. **Test Modal**: Klik tombol, modal terbuka
5. **Test Questions**: Jawab semua pertanyaan
6. **Test Save**: Simpan jawaban
7. **Test Submit**: Submit form utama

### 3. Browser Console Testing
```javascript
// Check questionnaire button visibility
console.log('Questionnaire button:', document.getElementById('questionnaire-button').style.display);

// Test API call
fetch('/perusahaan/buku-tamu/kuesioner-by-area?project_id=4&area_id=1')
  .then(r => r.json())
  .then(console.log);

// Check saved answers
console.log('Saved answers:', document.querySelector('input[name="kuesioner_answers"]')?.value);
```

## Error Handling

### Common Issues:
1. **Kuesioner Tidak Ditemukan**: Show error message in modal
2. **Network Error**: Show connection error
3. **Validation Error**: Highlight required fields
4. **JSON Parse Error**: Log error, show generic message

### Debug Tools:
- Console logging untuk setiap step
- Network tab untuk API calls
- Form data inspection

## Future Enhancements

### Possible Improvements:
1. **Question Dependencies**: Pertanyaan bersyarat
2. **File Upload Questions**: Upload dokumen/foto
3. **Signature Questions**: Tanda tangan digital
4. **Auto-save**: Simpan jawaban otomatis
5. **Question Preview**: Preview sebelum publish
6. **Analytics**: Laporan jawaban kuesioner

## Success Criteria

- ✅ Label "Nama Petugas" muncul
- ✅ Tombol kuesioner muncul jika project enable questionnaire
- ✅ Modal kuesioner dapat dibuka/ditutup
- ✅ Pertanyaan ter-render sesuai tipe
- ✅ Jawaban tersimpan dalam format JSON
- ✅ Form submission berhasil dengan jawaban kuesioner
- ✅ Data tersimpan di database dengan benar