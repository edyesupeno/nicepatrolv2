# Validasi Aset Check - Foto dan Catatan Wajib

## Overview
Ketika melakukan pengecekan aset dengan status selain "aman", maka **foto dan catatan wajib diisi**.

## Aturan Validasi

### Status Aset
- `aman`: Foto dan catatan opsional
- `bermasalah`: **Foto dan catatan WAJIB**
- `hilang`: **Foto dan catatan WAJIB**

### API Endpoint
```
POST /api/v1/checkpoints/{checkpoint_hash_id}/aset-status
```

### Request Format
```json
{
    "patroli_detail_id": 123,
    "aset_checks": [
        {
            "aset_id": 1,
            "status": "bermasalah",
            "catatan": "CCTV rusak, layar mati total",
            "foto": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQ..."
        },
        {
            "aset_id": 2,
            "status": "aman",
            "catatan": null,
            "foto": null
        }
    ]
}
```

## Validasi Error Response

### Error: Foto Wajib
```json
{
    "success": false,
    "message": "Foto wajib diisi untuk aset dengan status bermasalah",
    "errors": {
        "aset_checks.0.foto": [
            "Foto wajib diisi untuk status bermasalah"
        ]
    }
}
```

### Error: Catatan Wajib
```json
{
    "success": false,
    "message": "Catatan wajib diisi untuk aset dengan status hilang",
    "errors": {
        "aset_checks.0.catatan": [
            "Catatan wajib diisi untuk status hilang"
        ]
    }
}
```

## Success Response
```json
{
    "success": true,
    "message": "Status aset berhasil diperbarui",
    "data": {
        "patrol_detail": {
            "id": 123,
            "status": "bermasalah",
            "catatan": "Ada aset dengan masalah"
        },
        "aset_checks_count": 2
    }
}
```

## Mobile App Implementation

### 1. UI Validation
```javascript
function validateAsetCheck(asetCheck) {
    if (asetCheck.status === 'bermasalah' || asetCheck.status === 'hilang') {
        if (!asetCheck.foto) {
            showError('Foto wajib diisi untuk status ' + asetCheck.status);
            return false;
        }
        
        if (!asetCheck.catatan || asetCheck.catatan.trim() === '') {
            showError('Catatan wajib diisi untuk status ' + asetCheck.status);
            return false;
        }
    }
    return true;
}
```

### 2. Form Behavior
- Ketika user memilih status "Bermasalah" atau "Hilang":
  - Field foto menjadi **required** (tampilkan tanda *)
  - Field catatan menjadi **required** (tampilkan tanda *)
  - Disable tombol submit jika foto atau catatan kosong
  - Tampilkan border merah untuk field yang belum diisi
  - Tampilkan pesan error yang jelas

### 3. User Experience
- Tampilkan pesan yang jelas: "Foto dan catatan wajib diisi untuk aset bermasalah"
- Highlight field yang wajib diisi dengan border merah
- Berikan feedback visual yang jelas (border hijau saat valid)
- Disable submit button dengan pesan yang informatif

### 4. Real-time Validation
- Validasi saat user mengubah status aset
- Validasi saat user mengisi catatan
- Validasi saat user mengambil foto
- Update submit button state secara real-time

## Implementation Status

### ✅ Backend (API) - COMPLETED
- [x] Validasi server-side di `AsetCheckpointController`
- [x] Error response dengan format yang jelas
- [x] Validasi foto dan catatan untuk status bermasalah/hilang
- [x] Proper error handling dan rollback transaction

### ✅ Frontend (Mobile) - COMPLETED
- [x] Client-side validation sebelum submit
- [x] Real-time validation feedback
- [x] Visual indicators untuk required fields
- [x] Individual photo capture untuk setiap aset bermasalah
- [x] Error message yang user-friendly
- [x] Submit button state management

### ✅ User Interface - COMPLETED
- [x] Required field indicators (*)
- [x] Border merah untuk field yang error
- [x] Border hijau untuk field yang valid
- [x] Error messages di bawah field
- [x] Submit button dengan pesan yang informatif
- [x] Photo capture untuk setiap aset individual

## Testing Scenarios

### Test Case 1: Status Aman
```json
{
    "aset_id": 1,
    "status": "aman",
    "catatan": null,
    "foto": null
}
```
**Expected**: ✅ Success

### Test Case 2: Status Bermasalah tanpa Foto
```json
{
    "aset_id": 1,
    "status": "bermasalah",
    "catatan": "Rusak parah",
    "foto": null
}
```
**Expected**: ❌ Error - Foto wajib

### Test Case 3: Status Hilang tanpa Catatan
```json
{
    "aset_id": 1,
    "status": "hilang",
    "catatan": "",
    "foto": "data:image/jpeg;base64,..."
}
```
**Expected**: ❌ Error - Catatan wajib

### Test Case 4: Status Bermasalah Lengkap
```json
{
    "aset_id": 1,
    "status": "bermasalah",
    "catatan": "CCTV mati total, perlu diganti",
    "foto": "data:image/jpeg;base64,..."
}
```
**Expected**: ✅ Success

## Error Handling
- Tampilkan error message yang user-friendly
- Fokus ke field yang error
- Jangan submit form jika ada error
- Berikan guidance yang jelas untuk memperbaiki error
- Handle API validation errors dengan proper feedback

## Features Implemented

### 1. Server-side Validation
- Mandatory photo validation for bermasalah/hilang status
- Mandatory notes validation for bermasalah/hilang status
- Proper error responses with field-specific messages
- Transaction rollback on validation failure

### 2. Client-side Validation
- Real-time validation as user interacts with form
- Visual feedback with border colors (red for error, green for valid)
- Submit button state management with informative messages
- Pre-submit validation to prevent unnecessary API calls

### 3. Enhanced UI/UX
- Individual photo capture for each problematic asset
- Required field indicators with asterisks
- Error messages displayed below relevant fields
- Photo preview and retake functionality
- Clear visual distinction between different asset statuses

### 4. Comprehensive Error Messages
- Field-specific error messages
- User-friendly language in Indonesian
- Clear instructions on how to fix validation errors
- API error handling with proper user feedback

## Summary

The mandatory photo and notes validation for problematic assets has been **fully implemented** with:

1. **Backend validation** in `AsetCheckpointController` with proper error responses
2. **Frontend validation** in mobile app with real-time feedback
3. **Enhanced UI** with individual photo capture and clear visual indicators
4. **Comprehensive error handling** with user-friendly messages
5. **Complete documentation** for developers and testers

The implementation ensures data quality and compliance with business rules while providing an excellent user experience.