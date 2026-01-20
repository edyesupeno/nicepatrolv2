# Nice Patrol API - Environment Setup Guide

## 🌐 Available Environments

### 1. **Local Development** 
- **URL**: `http://localhost:8000/api/v1`
- **Status**: ⚪ Local only
- **Usage**: Development pada laptop lokal

### 2. **Development (Laptop)**
- **URL**: `https://devapi.nicepatrol.id/api/v1`
- **Status**: ⚠️ **Unstable** - Mati jika laptop developer dimatikan
- **Usage**: Testing sementara, tidak recommended untuk testing berkelanjutan

### 3. **Staging Server** ⭐ **RECOMMENDED**
- **URL**: `https://stagapi.nicepatrol.id/api/v1`
- **Status**: 🟢 **Stable** - Server staging yang selalu online
- **Usage**: **RECOMMENDED untuk semua testing dan development**

### 4. **Production Live**
- **URL**: `https://apiv1.nicepatrol.id/api/v1`
- **Status**: 🔴 **Live Production** - Gunakan dengan hati-hati
- **Usage**: Production environment, hindari testing di sini

## 🧪 Test Credentials

### Security Officer (Mobile App Testing)
```
Email: edy@gmail.com
Password: 12345678
Role: security_officer
```

### Admin Accounts
```
# Admin ABB
Email: abb@nicepatrol.id
Password: password

# Admin BSP  
Email: bsp@nicepatrol.id
Password: password

# Super Admin
Email: superadmin@nicepatrol.id
Password: password
```

## 📋 Setup Instructions

### Swagger UI Documentation
1. **Akses**: `https://devdash.nicepatrol.id/api-docs`
2. **Pilih Environment**: Staging Server (Recommended)
3. **Login**: Gunakan credentials di atas
4. **Authorize**: Copy token dan klik "Authorize" button

**⚠️ Jika dokumentasi tidak muncul atau tidak update:**
- Klik tombol "🔄 Force Refresh" di halaman dokumentasi
- Atau buka di incognito/private browsing mode
- Atau clear browser cache (Ctrl+Shift+Delete)

### Postman Collection
1. **Import Collection**: `Nice-Patrol-API-Collection.json`
2. **Import Environment**: Pilih salah satu dari folder `environments/`:
   - `Staging-Server.postman_environment.json` ⭐ **RECOMMENDED**
   - `Development-Laptop.postman_environment.json` (Unstable)
   - `Production-Live.postman_environment.json` (Use with caution)
   - `Local-Development.postman_environment.json` (Local only)
3. **Set Environment**: Pilih environment di Postman
4. **Test Login**: Jalankan request "Login" untuk mendapatkan token

## 🎯 Recommendations

### For Testing & Development
- ✅ **Use**: `stagapi.nicepatrol.id` (Staging Server)
- ❌ **Avoid**: `devapi.nicepatrol.id` (Laptop dependent)

### For Production
- ✅ **Use**: `apiv1.nicepatrol.id` (Production Live)
- ⚠️ **Caution**: Test thoroughly on staging first

## 🔧 Environment Variables

Untuk setiap environment, gunakan variables berikut di Postman:

```
{{base_url}} - Base API URL
{{auth_token}} - Authentication token (auto-saved after login)
{{test_email}} - Test email credential
{{test_password}} - Test password credential
```

## 📱 Mobile App Testing

Untuk testing mobile app (PWA), gunakan:
- **Staging**: `https://stagapp.nicepatrol.id`
- **Production**: `https://app.nicepatrol.id`

## 🚀 Quick Start

1. **Import** Postman collection dan staging environment
2. **Set environment** ke "Staging Server (Recommended)"
3. **Run login** request dengan `edy@gmail.com` / `12345678`
4. **Start testing** semua endpoints dengan token yang tersimpan otomatis

## 📦 Available API Endpoints

### Authentication
- `POST /login` - User login
- `POST /logout` - User logout
- `GET /me` - Get current user info

### Penerimaan Barang (Goods Receipt)
- `GET /penerimaan-barang` - List all goods receipts (with pagination & filters)
- `POST /penerimaan-barang` - Create new goods receipt
- `GET /penerimaan-barang/{id}` - Get goods receipt detail
- `PUT /penerimaan-barang/{id}` - Update goods receipt
- `DELETE /penerimaan-barang/{id}` - Delete goods receipt
- `GET /penerimaan-barang-projects` - Get projects dropdown
- `GET /penerimaan-barang-areas/{project_id}` - Get areas by project

### Required Parameters for Penerimaan Barang
- `project_id` - ID of the project (integer, optional)
- `area_id` - ID of the storage area (integer, optional)  
- `pos` - Point of Storage location (string, optional, e.g., "A1-B2-C3")
- `nama_barang` - Item name (string, required)
- `kategori_barang` - Item category (enum: Dokumen, Material, Elektronik, Logistik)
- `kondisi_barang` - Item condition (enum: Baik, Rusak, Segel Terbuka)

### Other Endpoints
- Absensi (Attendance)
- Shift Schedule
- Lokasi (Locations)
- Checkpoints
- Project Contacts

**Happy Testing!** 🎉