# 🌐 Network Access Guide - Nice Patrol

## Quick Start

### Cara 1: Menggunakan Script (Recommended)

```bash
# Start server
./start-server.sh

# Stop server
./stop-server.sh

# Update IP (jika ganti network)
./update-ip.sh
```

### Cara 2: Manual

```bash
# Terminal 1 - Laravel Server
php artisan serve --host=0.0.0.0 --port=8000

# Terminal 2 - Vite Dev Server
npm run dev -- --host
```

## Akses dari Perangkat Lain

### Dari Komputer Ini
- http://localhost:8000
- http://10.79.202.42:8000

### Dari HP/Tablet/Komputer Lain (1 Network)
- http://10.79.202.42:8000

## 📱 Menggunakan Hotspot HP

### Setup:
1. **Aktifkan Hotspot di HP**
2. **Connect Mac ke Hotspot HP**
3. **Jalankan script update IP:**
   ```bash
   ./update-ip.sh
   ```
4. **Restart server:**
   ```bash
   ./stop-server.sh
   ./start-server.sh
   ```

### Akses dari HP yang sama:
- Buka browser di HP
- Ketik: http://10.79.202.42:8000
- Login dengan akun yang sudah ada

### Akses dari HP/Device lain:
- Connect ke hotspot yang sama
- Buka browser
- Ketik: http://10.79.202.42:8000

## Troubleshooting

### Port Sudah Digunakan
```bash
# Cek proses yang menggunakan port 8000
lsof -i :8000

# Kill proses
kill -9 $(lsof -t -i:8000)
```

### Tidak Bisa Akses dari Perangkat Lain

1. **Cek Firewall macOS**
   - System Settings > Network > Firewall
   - Pastikan tidak block port 8000

2. **Cek IP Address**
   ```bash
   ipconfig getifaddr en0
   ```
   Pastikan IP masih sama (192.168.1.18)

3. **Cek Network**
   - Pastikan semua perangkat di WiFi yang sama
   - Pastikan tidak ada network isolation

### Update IP Address

**Jika ganti network (WiFi ke Hotspot atau sebaliknya):**

```bash
# Auto-detect dan update semua config
./update-ip.sh

# Restart server
./stop-server.sh
./start-server.sh
```

**Manual update jika perlu:**

1. **Cek IP baru:**
   ```bash
   ipconfig getifaddr en0
   ```

2. **Update vite.config.js**
   ```javascript
   hmr: {
       host: '10.79.202.42' // Ganti dengan IP baru
   }
   ```

3. **Update .env**
   ```
   APP_URL=http://10.79.202.42:8000
   ```

4. **Update start-server.sh**
   ```bash
   echo "📡 IP Address: 10.79.202.42" # Ganti dengan IP baru
   ```

## Testing

### Test dari Browser
```
http://192.168.1.18:8000
```

### Test dari cURL
```bash
curl http://10.79.202.42:8000
```

### Test dari HP
1. Buka browser di HP
2. Ketik: http://10.79.202.42:8000
3. Pastikan HP terhubung ke hotspot yang sama

## 🔄 Ganti Network (WiFi ↔ Hotspot)

Jika kamu ganti dari WiFi ke Hotspot HP atau sebaliknya:

```bash
# 1. Update IP otomatis
./update-ip.sh

# 2. Restart server
./stop-server.sh
./start-server.sh

# 3. Cek IP baru
cat SERVER-INFO.txt
```

## 📱 Tips Hotspot HP

1. **Pastikan Hotspot Aktif** sebelum jalankan server
2. **Mac harus connect** ke hotspot HP
3. **IP akan berubah** setiap kali connect/disconnect
4. **Gunakan `update-ip.sh`** untuk auto-update config
5. **Battery HP** - pastikan charging saat jadi hotspot

## Security Note

⚠️ **Development Only!**
- Konfigurasi ini hanya untuk development
- Jangan gunakan di production
- Semua perangkat di network bisa akses aplikasi
