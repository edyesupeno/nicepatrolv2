# Attendance Module - Nice Patrol System

## Overview
The attendance module allows security officers and employees to check in and check out using their mobile devices with selfie capture and location verification.

## Features

### ✅ Implemented Features
1. **Location-based Attendance**
   - Select from available attendance locations for the user's project
   - GPS location verification with radius checking
   - Distance calculation and validation

2. **Selfie Capture**
   - Camera access for selfie capture
   - Photo storage for attendance records
   - Retake functionality

3. **Check In/Check Out**
   - Automatic status detection (can check in, can check out, completed)
   - Work duration calculation
   - Status determination (hadir, terlambat, pulang_cepat, terlambat_pulang_cepat)

4. **Real-time Status Updates**
   - Live attendance button updates
   - Current attendance status display
   - Auto-refresh functionality

## API Endpoints

### Attendance Locations
```
GET /api/v1/absensi/lokasi
```
Returns available attendance locations for the user's project.

### Today's Status
```
GET /api/v1/absensi/today-status
```
Returns current attendance status (can check in, can check out, completed).

### Check In
```
POST /api/v1/absensi/check-in
```
Parameters:
- `lokasi_absensi_id`: Location ID
- `latitude`: Current latitude
- `longitude`: Current longitude
- `foto`: Selfie image file

### Check Out
```
POST /api/v1/absensi/check-out
```
Parameters:
- `lokasi_absensi_id`: Location ID
- `latitude`: Current latitude
- `longitude`: Current longitude
- `foto`: Selfie image file

## Mobile Interface

### Home Page Updates
- **Attendance Button**: Dynamic button that changes based on status
  - Green "Absen Masuk" when can check in
  - Orange "Absen Keluar" when can check out
  - Gray "Absensi Selesai" when completed
- **Status Display**: Shows current check-in and check-out times

### Attendance Page (`/employee/absensi`)
1. **Status Card**: Shows current attendance status
2. **Location Selection**: List of available locations with radius info
3. **Camera Section**: Selfie capture with preview
4. **Submit Button**: Processes the attendance

## Database Structure

### Tables Used
- `kehadirans`: Main attendance records
- `lokasi_absensis`: Attendance locations
- `jadwal_shifts`: Employee shift schedules
- `karyawans`: Employee data
- `users`: User authentication

### Key Fields in `kehadirans`
- `jam_masuk`, `jam_keluar`: Check-in/out times
- `foto_masuk`, `foto_keluar`: Selfie photos
- `lokasi_masuk`, `lokasi_keluar`: Location names
- `status`: Attendance status (hadir, terlambat, pulang_cepat, etc.)
- `durasi_kerja`: Work duration in minutes
- `on_radius`: Whether within location radius

## Security Features

### Multi-tenancy
- All data filtered by `perusahaan_id`
- Users can only access their company's locations
- Global scopes ensure data isolation

### Location Verification
- GPS coordinate validation
- Distance calculation using Haversine formula
- Radius checking with warning for out-of-range

### Photo Storage
- Secure file upload to `storage/app/public/absensi/`
- Unique filename generation
- File cleanup on errors

## Usage Flow

### For Employees
1. **Open App**: Navigate to `/employee/home`
2. **Check Status**: See current attendance status on home page
3. **Start Attendance**: Click attendance button
4. **Select Location**: Choose from available locations
5. **Take Selfie**: Capture photo using device camera
6. **Submit**: Complete check-in or check-out

### For Administrators
1. **Setup Locations**: Create attendance locations in admin panel
2. **Assign Shifts**: Set up employee shift schedules
3. **Monitor**: View attendance records and reports

## Technical Implementation

### Frontend (Mobile PWA)
- **Framework**: Vanilla JavaScript with Tailwind CSS
- **Camera API**: MediaDevices.getUserMedia()
- **Geolocation**: Navigator.geolocation
- **Storage**: localStorage for authentication
- **Notifications**: SweetAlert2 for user feedback

### Backend (Laravel API)
- **Authentication**: Sanctum token-based
- **File Upload**: Laravel Storage with public disk
- **Database**: Eloquent ORM with global scopes
- **Validation**: Form request validation
- **Error Handling**: Try-catch with rollback

### Mobile Features
- **PWA Support**: Service worker and manifest
- **Offline Detection**: Network status monitoring
- **Responsive Design**: Mobile-first approach
- **Touch Optimized**: Gesture-friendly interface

## Configuration

### Environment Variables
```env
MOBILE_DOMAIN=app.nicepatrol.id
API_DOMAIN=api.nicepatrol.id
```

### File Storage
Photos stored in: `storage/app/public/absensi/`
Access via: `storage/absensi/filename.jpg`

## Testing

### Manual Testing Steps
1. **Setup**: Create company, project, locations, employees
2. **Login**: Access mobile app with employee credentials
3. **Location**: Test GPS permission and location detection
4. **Camera**: Test camera permission and photo capture
5. **Attendance**: Complete full check-in/check-out cycle
6. **Validation**: Verify data in database and admin panel

### Test Cases
- ✅ Check-in within radius
- ✅ Check-in outside radius (with warning)
- ✅ Check-out after check-in
- ✅ Prevent duplicate check-in
- ✅ Prevent check-out without check-in
- ✅ Status calculation (hadir, terlambat, etc.)
- ✅ Multi-tenancy isolation

## Troubleshooting

### Common Issues
1. **Camera not working**: Check browser permissions
2. **GPS not accurate**: Ensure location services enabled
3. **API errors**: Check network connection and authentication
4. **File upload fails**: Verify storage permissions

### Debug Tools
- Browser Developer Tools
- Laravel Log Viewer
- API response inspection
- Database query logging

## Future Enhancements

### Planned Features
- [ ] Offline attendance with sync
- [ ] Face recognition validation
- [ ] Attendance analytics dashboard
- [ ] Push notifications for reminders
- [ ] Bulk attendance import/export
- [ ] Advanced reporting features

### Performance Optimizations
- [ ] Image compression before upload
- [ ] Lazy loading for location lists
- [ ] Caching for frequently accessed data
- [ ] Background sync for offline data

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

---

**Last Updated**: January 17, 2026
**Version**: 1.0.0
**Status**: Production Ready ✅