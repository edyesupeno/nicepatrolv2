# Fitur Patroli Mandiri - Nice Patrol System

## Overview
Fitur "Patroli Mandiri" adalah sistem pelaporan insidental yang memungkinkan petugas security melaporkan kondisi lokasi yang ditemukan saat melakukan patroli mandiri. Fitur ini bersifat insidental dan tidak terjadwal, berbeda dengan patroli rutin.

## Fitur Utama

### 1. **Pelaporan Kondisi Lokasi**
- **Status Aman**: Lokasi dalam kondisi normal dan aman
- **Status Tidak Aman**: Lokasi memiliki kendala atau masalah yang perlu perhatian

### 2. **Jenis Kendala yang Dapat Dilaporkan**
- Kebakaran
- Aset Rusak
- Aset Hilang
- Orang Mencurigakan
- Kabel Terbuka
- Pencurian
- Sabotase
- Demo

### 3. **Sistem Prioritas Otomatis**
- **Rendah**: Kondisi aman atau kendala minor
- **Sedang**: Aset rusak, aset hilang, kabel terbuka
- **Tinggi**: Pencurian, orang mencurigakan, demo
- **Kritis**: Kebakaran, sabotase

### 4. **Dokumentasi Lengkap**
- **GPS Coordinates**: Lokasi presisi dengan latitude/longitude
- **Google Maps Integration**: Link langsung ke Google Maps
- **Foto Dokumentasi**: 
  - Foto lokasi (wajib untuk semua kondisi)
  - Foto kendala (opsional untuk kondisi tidak aman)
- **Catatan Detail**: Deskripsi kendala, catatan petugas, tindakan yang diambil

### 5. **Workflow Review**
- **Submitted**: Laporan baru dari petugas
- **Reviewed**: Sudah direview oleh supervisor
- **Resolved**: Masalah sudah diselesaikan

## Struktur Database

### Tabel: `patroli_mandiri`
```sql
- id (Primary Key)
- perusahaan_id (Foreign Key)
- project_id (Foreign Key) 
- area_patrol_id (Foreign Key, nullable)
- petugas_id (Foreign Key)
- nama_lokasi (String)
- latitude, longitude (Decimal)
- maps_url (String)
- waktu_laporan (DateTime)
- status_lokasi (Enum: aman, tidak_aman)
- jenis_kendala (Enum: kebakaran, aset_rusak, dll)
- deskripsi_kendala (Text)
- catatan_petugas (Text)
- tindakan_yang_diambil (Text)
- foto_lokasi, foto_kendala (String)
- status_laporan (Enum: submitted, reviewed, resolved)
- reviewed_by, reviewed_at (Foreign Key, DateTime)
- review_catatan (Text)
- prioritas (Enum: rendah, sedang, tinggi, kritis)
```

## API Endpoints

### Mobile API (api.nicepatrol.id/v1)
- `GET /patroli-mandiri` - List laporan petugas
- `POST /patroli-mandiri` - Buat laporan baru
- `GET /patroli-mandiri/{id}` - Detail laporan
- `PUT /patroli-mandiri/{id}` - Update laporan (hanya jika belum direview)
- `GET /patroli-mandiri-projects` - List projects
- `GET /patroli-mandiri-areas/{project}` - List areas by project
- `GET /patroli-mandiri-jenis-kendala` - List jenis kendala

### Web Interface (devdash.nicepatrol.id/perusahaan)
- `GET /patroli-mandiri` - Dashboard laporan
- `GET /patroli-mandiri/{id}` - Detail laporan
- `POST /patroli-mandiri/{id}/review` - Review laporan
- `DELETE /patroli-mandiri/{id}` - Hapus laporan
- `GET /patroli-mandiri-stats` - Statistik dashboard

## Mobile Interface

### URL: `devapp.nicepatrol.id/security/patroli-mandiri`

**Fitur Mobile:**
- Form input dengan GPS auto-detection
- Camera integration untuk foto
- Conditional fields (kendala fields muncul jika status tidak aman)
- Real-time validation
- Offline-ready design

**Flow Penggunaan:**
1. Petugas buka form patroli mandiri
2. Pilih project dan area (opsional)
3. Input nama lokasi
4. Ambil koordinat GPS otomatis
5. Pilih status lokasi (aman/tidak aman)
6. Jika tidak aman: pilih jenis kendala, isi deskripsi
7. Ambil foto lokasi (wajib)
8. Ambil foto kendala (opsional jika tidak aman)
9. Isi catatan dan tindakan yang diambil
10. Submit laporan

## Web Dashboard

### URL: `devdash.nicepatrol.id/perusahaan/patroli-mandiri`

**Fitur Dashboard:**
- **Statistics Cards**: Total laporan, lokasi aman/tidak aman, belum direview
- **Advanced Filtering**: Project, status, prioritas, tanggal, pencarian
- **Data Table**: List semua laporan dengan informasi lengkap
- **Review System**: Supervisor bisa review dan update status
- **Detail View**: Lihat semua informasi termasuk foto dan maps
- **Delete Function**: Hapus laporan dengan konfirmasi

**Menu Location:**
- Sidebar → Regu Patroli → Patroli Mandiri (setelah Kru Change)

## Security & Multi-Tenancy

### Keamanan Data:
- **Global Scope**: Otomatis filter berdasarkan `perusahaan_id`
- **Hash ID**: URL menggunakan hash ID, bukan integer ID
- **File Upload**: Validasi tipe file dan ukuran maksimal 2MB
- **Authorization**: Petugas hanya bisa edit laporan sendiri yang belum direview

### Multi-Tenancy:
- Semua data terisolasi per perusahaan
- Auto-assign `perusahaan_id` saat create
- Tidak ada akses cross-company

## File Storage

### Struktur Folder:
```
storage/public/patroli-mandiri/
├── lokasi/          # Foto lokasi
└── kendala/         # Foto kendala
```

### Validasi Upload:
- Format: JPEG, PNG, JPG
- Ukuran maksimal: 2MB
- Auto-delete foto lama saat update

## Integration Points

### Dengan Sistem Existing:
- **Projects**: Menggunakan project yang sudah ada
- **Area Patrol**: Menggunakan area patrol yang sudah ada  
- **Users**: Menggunakan user dengan role security_officer
- **Authentication**: Menggunakan Sanctum token untuk mobile API

### Dengan Mobile App:
- **GPS Integration**: HTML5 Geolocation API
- **Camera**: HTML5 Camera API dengan capture="environment"
- **Offline Support**: Form validation dan data caching
- **PWA Ready**: Responsive design untuk mobile

## Monitoring & Analytics

### Dashboard Statistics:
- Total laporan per periode
- Persentase lokasi aman vs tidak aman
- Distribusi prioritas kendala
- Response time review
- Trend laporan harian/bulanan

### Reporting:
- Export data ke Excel/PDF
- Filter berdasarkan berbagai kriteria
- Grafik dan visualisasi data

## Future Enhancements

### Planned Features:
1. **Push Notifications**: Notifikasi real-time untuk laporan prioritas tinggi
2. **Geofencing**: Validasi lokasi berdasarkan area yang ditugaskan
3. **Voice Notes**: Rekaman suara untuk catatan tambahan
4. **QR Code Integration**: Scan QR code lokasi untuk auto-fill data
5. **Escalation Rules**: Auto-escalation berdasarkan prioritas dan waktu
6. **Mobile Offline Sync**: Simpan laporan offline dan sync saat online
7. **Analytics Dashboard**: Advanced reporting dan analytics
8. **Integration dengan CCTV**: Link ke footage CCTV berdasarkan lokasi dan waktu

## Technical Implementation

### Standards Compliance:
- ✅ Hash ID untuk URL (bukan integer ID)
- ✅ Multi-tenancy dengan global scope
- ✅ SweetAlert2 untuk notifikasi (bukan browser alert)
- ✅ Mobile API menggunakan Sanctum token
- ✅ Database optimization dengan proper indexing
- ✅ File upload dengan validasi keamanan
- ✅ Responsive design untuk mobile

### Performance Optimization:
- Database indexing untuk query yang sering digunakan
- Pagination untuk list data
- Image compression untuk foto upload
- Lazy loading untuk foto di dashboard
- Caching untuk dropdown data (projects, areas)

## Deployment Checklist

### Database:
- [x] Migration executed
- [x] Seeder data created
- [x] Indexes properly set

### Code:
- [x] Models with relationships
- [x] Controllers (Web & API)
- [x] Routes registered
- [x] Views created
- [x] Mobile interface

### Security:
- [x] Multi-tenancy implemented
- [x] Hash ID for URLs
- [x] File upload validation
- [x] Authorization checks

### UI/UX:
- [x] Menu added to sidebar
- [x] Responsive design
- [x] Mobile-friendly interface
- [x] SweetAlert2 integration

### Testing:
- [ ] Unit tests for models
- [ ] Feature tests for controllers
- [ ] API endpoint testing
- [ ] Mobile interface testing
- [ ] Multi-tenancy isolation testing

## Conclusion

Fitur Patroli Mandiri telah berhasil diimplementasikan dengan lengkap sesuai dengan standar sistem Nice Patrol. Fitur ini menyediakan solusi komprehensif untuk pelaporan insidental dengan dokumentasi lengkap, workflow review, dan integrasi yang seamless dengan sistem yang sudah ada.

**Key Benefits:**
- Pelaporan real-time dengan GPS dan foto
- Sistem prioritas otomatis untuk response yang tepat
- Workflow review yang terstruktur
- Mobile-first design untuk kemudahan penggunaan
- Dashboard analytics untuk monitoring dan decision making
- Compliance dengan security dan multi-tenancy standards