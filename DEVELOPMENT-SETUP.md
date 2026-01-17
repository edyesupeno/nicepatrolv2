# Development Setup - Nice Patrol

## 🚀 Quick Start untuk Development

### 1. Setup Cloudflare Tunnel (One-time setup)

```bash
# Install dan setup Cloudflare Tunnel
./setup-cf-tunnel.sh
```

### 2. Daily Development Workflow

```bash
# Terminal 1: Start Laravel server
php artisan serve --port=8000

# Terminal 2: Start Cloudflare Tunnel
./start-tunnel.sh
```

### 3. Access Application

Development domains yang tersedia:

- **🏢 Dashboard**: `https://devdash.nicepatrol.id` - Admin dashboard
- **🔌 API**: `https://devapi.nicepatrol.id` - API endpoints  
- **📱 Mobile App**: `https://devapp.nicepatrol.id` - Mobile PWA

Semua domain pointing ke `localhost:8000` dengan HTTPS! ✅

## 📱 Testing Mobile Features

### Attendance Module
- **URL**: `https://devapp.nicepatrol.id/security/absensi`
- **Features**: Camera selfie, GPS location, dynamic attendance workflow
- **Test Flow**: Absen Masuk → Istirahat → Kembali Bekerja → Absen Pulang

### Security Officer Dashboard
- **URL**: `https://devapp.nicepatrol.id/security/home`
- **Features**: Dynamic attendance button, shift info, patrol areas

### Admin Dashboard
- **URL**: `https://devdash.nicepatrol.id/perusahaan/kehadiran`
- **Features**: Edit/delete attendance, view reports, manage employees

## 🔧 Environment Configuration

Update your `.env` file:

```env
# Development domains
APP_URL=https://devdash.nicepatrol.id
API_DOMAIN=devapi.nicepatrol.id
MOBILE_DOMAIN=devapp.nicepatrol.id

# HTTPS for all domains
FORCE_HTTPS=true
```

## 🛠️ Troubleshooting

### Camera/GPS Not Working?
1. ✅ Make sure you're using the dev domains (devapp.nicepatrol.id)
2. ✅ Click "Allow" when browser asks for permissions
3. ✅ All domains have HTTPS - no permission issues!

### Domain Not Accessible?
1. ✅ Make sure Cloudflare Tunnel is running: `./start-tunnel.sh`
2. ✅ Check Laravel server: `curl http://localhost:8000`
3. ✅ DNS might take a few minutes to propagate

### Laravel Server Issues?
1. ✅ Check if running: `curl http://localhost:8000`
2. ✅ Restart server: `php artisan serve --port=8000`
3. ✅ Check .env configuration

## 🌐 Domain Structure

```
devdash.nicepatrol.id  → Dashboard/Admin Panel
├── /perusahaan/*      → Company management
├── /admin/*           → System admin
└── /login             → Admin login

devapi.nicepatrol.id   → API Endpoints
├── /v1/auth/*         → Authentication
├── /v1/absensi/*      → Attendance API
└── /v1/patroli/*      → Patrol API

devapp.nicepatrol.id   → Mobile PWA
├── /security/*        → Security officer app
├── /employee/*        → Employee app
└── /login             → Mobile login
```

## 🔗 Useful Commands

```bash
# Check if Laravel is running
curl http://localhost:8000

# Test specific domains
curl https://devdash.nicepatrol.id
curl https://devapi.nicepatrol.id  
curl https://devapp.nicepatrol.id

# Check tunnel status
ps aux | grep cloudflared

# Kill tunnel process
pkill cloudflared

# Restart everything
./start-tunnel.sh
```

## 📝 Benefits of Using Dev Domains

- ✅ **Consistent URLs**: No more changing IPs or random tunnel URLs
- ✅ **HTTPS Everywhere**: Camera and GPS work perfectly
- ✅ **Multi-domain**: Separate domains for dashboard, API, and mobile
- ✅ **Production-like**: Same domain structure as production
- ✅ **Team Friendly**: Everyone uses the same URLs