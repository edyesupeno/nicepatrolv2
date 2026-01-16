# Deployment Guide - Nice Patrol System

## Production Deployment

### 1. Server Requirements

- PHP 8.2+
- PostgreSQL 16+
- Composer
- Nginx/Apache
- SSL Certificate (recommended)

### 2. Environment Setup

Copy `.env.example` ke `.env` dan sesuaikan:

```env
APP_NAME="Nice Patrol"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=your-postgres-host
DB_PORT=5432
DB_DATABASE=patrol_db
DB_USERNAME=patrol_user
DB_PASSWORD=your-secure-password

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis (optional, untuk performance)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Installation Steps

```bash
# Clone repository
git clone <repository-url>
cd nicepatrolv2

# Install dependencies
composer install --optimize-autoloader --no-dev

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --class=SuperAdminSeeder

# Link storage
php artisan storage:link

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/nicepatrolv2/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. PostgreSQL Setup (Production)

```bash
# Login ke PostgreSQL
sudo -u postgres psql

# Create database dan user
CREATE DATABASE patrol_db;
CREATE USER patrol_user WITH ENCRYPTED PASSWORD 'your-secure-password';
GRANT ALL PRIVILEGES ON DATABASE patrol_db TO patrol_user;
\q
```

### 6. File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/nicepatrolv2

# Set permissions
sudo chmod -R 755 /var/www/nicepatrolv2
sudo chmod -R 775 /var/www/nicepatrolv2/storage
sudo chmod -R 775 /var/www/nicepatrolv2/bootstrap/cache
```

### 7. SSL Certificate (Let's Encrypt)

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d your-domain.com

# Auto-renewal
sudo certbot renew --dry-run
```

### 8. Supervisor (Queue Worker)

Jika menggunakan queue, setup supervisor:

```bash
# Install supervisor
sudo apt install supervisor

# Create config
sudo nano /etc/supervisor/conf.d/patrol-worker.conf
```

```ini
[program:patrol-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/nicepatrolv2/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/nicepatrolv2/storage/logs/worker.log
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start patrol-worker:*
```

### 9. Backup Strategy

#### Database Backup

```bash
# Create backup script
nano /usr/local/bin/backup-patrol-db.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/patrol"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR

pg_dump -U patrol_user -h localhost patrol_db | gzip > $BACKUP_DIR/patrol_db_$DATE.sql.gz

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete
```

```bash
# Make executable
chmod +x /usr/local/bin/backup-patrol-db.sh

# Add to crontab (daily at 2 AM)
crontab -e
0 2 * * * /usr/local/bin/backup-patrol-db.sh
```

#### Storage Backup

```bash
# Backup uploaded files
rsync -avz /var/www/nicepatrolv2/storage/app/public/ /backups/patrol/storage/
```

### 10. Monitoring

#### Log Monitoring

```bash
# View Laravel logs
tail -f /var/www/nicepatrolv2/storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log
```

#### Health Check Endpoint

Tambahkan di `routes/api.php`:

```php
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'database' => DB::connection()->getPdo() ? 'connected' : 'disconnected'
    ]);
});
```

### 11. Security Checklist

- [ ] Set `APP_DEBUG=false` di production
- [ ] Generate strong `APP_KEY`
- [ ] Use HTTPS dengan SSL certificate
- [ ] Set strong database password
- [ ] Configure firewall (UFW)
- [ ] Disable directory listing
- [ ] Set proper file permissions
- [ ] Enable rate limiting
- [ ] Regular security updates
- [ ] Backup database regularly

### 12. Performance Optimization

```bash
# Install OPcache
sudo apt install php8.2-opcache

# Install Redis
sudo apt install redis-server
composer require predis/predis

# Update .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 13. Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --secret="your-secret-token"

# Access site during maintenance
https://your-domain.com/your-secret-token

# Disable maintenance mode
php artisan up
```

### 14. Update Deployment

```bash
# Pull latest code
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
```

## Docker Production Deployment

### docker-compose.prod.yml

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: patrol_app
    restart: unless-stopped
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    volumes:
      - ./storage:/var/www/html/storage
    networks:
      - patrol_network
    depends_on:
      - postgres
      - redis

  nginx:
    image: nginx:alpine
    container_name: patrol_nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./public:/var/www/html/public
      - ./nginx.conf:/etc/nginx/conf.d/default.conf
      - ./ssl:/etc/nginx/ssl
    networks:
      - patrol_network
    depends_on:
      - app

  postgres:
    image: postgres:16-alpine
    container_name: patrol_postgres
    restart: unless-stopped
    environment:
      POSTGRES_DB: patrol_db
      POSTGRES_USER: patrol_user
      POSTGRES_PASSWORD: ${DB_PASSWORD}
    volumes:
      - postgres_data:/var/lib/postgresql/data
    networks:
      - patrol_network

  redis:
    image: redis:alpine
    container_name: patrol_redis
    restart: unless-stopped
    networks:
      - patrol_network

volumes:
  postgres_data:

networks:
  patrol_network:
    driver: bridge
```

## Troubleshooting

### Issue: 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chown -R www-data:www-data storage bootstrap/cache
```

### Issue: Database connection failed

```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check PostgreSQL status
sudo systemctl status postgresql
```

### Issue: Storage link not working

```bash
# Remove old link
rm public/storage

# Create new link
php artisan storage:link
```
