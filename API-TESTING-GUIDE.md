# API Testing Guide - Penerimaan Barang

## ✅ **Endpoint URLs yang Benar**

### **Base URL:**
- **Local**: `http://localhost:8000/api/v1`
- **Development**: `https://devapi.nicepatrol.id/v1`
- **Production**: `https://api.nicepatrol.id/v1`

### **Penerimaan Barang Endpoints:**

1. **Get Projects (Dropdown)**
   ```
   GET {{base_url}}/penerimaan-barang-projects
   ```

2. **Get Areas by Project**
   ```
   GET {{base_url}}/penerimaan-barang-areas/{project_id}
   ```
   Contoh: `GET http://localhost:8000/api/v1/penerimaan-barang-areas/1`

3. **Get All Penerimaan Barang**
   ```
   GET {{base_url}}/penerimaan-barang
   ```

4. **Create Penerimaan Barang**
   ```
   POST {{base_url}}/penerimaan-barang
   ```

5. **Get by Hash ID**
   ```
   GET {{base_url}}/penerimaan-barang/{hash_id}
   ```

6. **Update by Hash ID**
   ```
   PUT {{base_url}}/penerimaan-barang/{hash_id}
   ```

7. **Delete by Hash ID**
   ```
   DELETE {{base_url}}/penerimaan-barang/{hash_id}
   ```

## 🔐 **Testing Steps (Urutan yang Benar)**

### **Step 1: Login**
```
POST {{base_url}}/login
Content-Type: application/json

{
    "email": "edy@gmail.com",
    "password": "12345678"
}
```

**Response akan berisi:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 7,
            "name": "Muhammad Edi Suarno",
            "project_id": 1,
            "project": {
                "id": 1,
                "nama": "Kantor Jakarta"
            }
        },
        "token": "1|abc123..."
    }
}
```

**Simpan token untuk request selanjutnya!**

### **Step 2: Set Authorization Header**
Untuk semua request selanjutnya, tambahkan header:
```
Authorization: Bearer {token_dari_login}
```

### **Step 3: Test Get Projects**
```
GET {{base_url}}/penerimaan-barang-projects
Authorization: Bearer {your_token}
```

### **Step 4: Test Get Areas**
```
GET {{base_url}}/penerimaan-barang-areas/1
Authorization: Bearer {your_token}
```

### **Step 5: Test Create Penerimaan Barang**
```
POST {{base_url}}/penerimaan-barang
Authorization: Bearer {your_token}
Content-Type: application/json

{
    "nama_barang": "Test Laptop",
    "kategori_barang": "Elektronik",
    "jumlah_barang": 1,
    "satuan": "unit",
    "kondisi_barang": "Baik",
    "pengirim": "PT. Test",
    "tujuan_departemen": "IT",
    "tanggal_terima": "2026-01-20"
}
```

## 🚨 **Common Errors & Solutions**

### **Error 404 Not Found**
❌ **Salah**: `/penerimaan-barang/areas/1`  
✅ **Benar**: `/penerimaan-barang-areas/1`

### **Error 401 Unauthorized**
- Pastikan sudah login dan dapat token
- Pastikan header Authorization sudah benar: `Bearer {token}`

### **Error 403 Forbidden**
- User tidak punya akses ke project tersebut
- Coba login dengan user yang berbeda

### **Error 422 Validation Error**
- Cek required fields
- Cek format data (tanggal, enum values, dll)

## 📋 **Testing Checklist**

### **Multi-Tenancy Testing:**

1. **Login dengan user berbeda:**
   - `security@nicepatrol.id` (Security Officer)
   - `admin@nicepatrol.id` (Admin)
   - `superadmin@nicepatrol.id` (Superadmin)

2. **Test filtering per user:**
   - Security Officer: Hanya lihat data mereka sendiri
   - Admin: Lihat semua data di perusahaan
   - Superadmin: Lihat semua data

3. **Test project access:**
   - User hanya bisa akses project mereka
   - Error 403 jika akses project lain

## 🔧 **Postman Collection Variables**

Set variables ini di Postman:
```
base_url: http://localhost:8000/api/v1
token: (akan di-set otomatis setelah login)
project_id: (akan di-set otomatis setelah login)
```

## 📝 **Sample Test Data**

### **Login Credentials:**
```json
{
    "email": "edy@gmail.com",
    "password": "12345678"
}
```

### **Create Penerimaan Barang:**
```json
{
    "nama_barang": "Laptop Dell Inspiron 15",
    "kategori_barang": "Elektronik",
    "jumlah_barang": 2,
    "satuan": "unit",
    "kondisi_barang": "Baik",
    "pengirim": "PT. Supplier ABC",
    "tujuan_departemen": "IT Department",
    "tanggal_terima": "2026-01-20",
    "keterangan": "Laptop untuk karyawan baru"
}
```

## 🎯 **Expected Results**

### **Get Projects Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama": "Kantor Jakarta"
        }
    ]
}
```

### **Get Areas Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama": "Gudang A",
            "alamat": "Lantai 1"
        }
    ]
}
```

### **Create Response:**
```json
{
    "success": true,
    "message": "Penerimaan barang berhasil dibuat",
    "data": {
        "id": 1,
        "hash_id": "abc123def456",
        "nomor_penerimaan": "PB20260120001",
        "nama_barang": "Laptop Dell Inspiron 15",
        "created_by": 6,
        "createdBy": {
            "id": 6,
            "name": "Yundi"
        }
    }
}
```

## 🔍 **Debug Tips**

1. **Cek Laravel Log:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Test dengan cURL:**
   ```bash
   curl -X GET "http://localhost:8000/api/v1/penerimaan-barang-projects" \
        -H "Authorization: Bearer your_token" \
        -H "Accept: application/json"
   ```

3. **Cek Database:**
   ```sql
   SELECT * FROM penerimaan_barangs WHERE created_by = 6;
   ```

Ikuti panduan ini untuk testing yang benar! 🚀