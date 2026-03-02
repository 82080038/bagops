# Docker Setup Guide untuk BAGOPS

## Pengantar

Docker memungkinkan Anda menjalankan aplikasi BAGOPS di mana saja dengan kode dan database yang sama persis. Tidak perlu lagi setup manual PHP, MySQL, atau Apache di setiap komputer.

## Prasyarat

1. **Install Docker Desktop** (Windows/Mac) atau **Docker Engine** (Linux)
   - Windows: https://docs.docker.com/desktop/install/windows-install/
   - Mac: https://docs.docker.com/desktop/install/mac-install/
   - Linux: https://docs.docker.com/engine/install/

2. **Install Docker Compose** (biasanya sudah termasuk dalam Docker Desktop)

## Struktur Docker

```
bagops/
├── Dockerfile              # Konfigurasi container PHP
├── docker-compose.yml      # Orkestrasi semua services
├── .env                    # Environment variables
├── .dockerignore          # File yang diabaikan saat build
├── config/
│   ├── config.docker.php  # Config untuk Docker environment
│   └── database.docker.php # Database config untuk Docker
└── sql/                   # Database initialization scripts
```

## Services yang Dijalankan

1. **app** (PHP 8.2 + Apache) - Port 8080
2. **mysql** (MySQL 8.0) - Port 3306
3. **phpmyadmin** - Port 8081 
4. **redis** (Optional cache) - Port 6379

## Cara Penggunaan

### 1. Start Environment

```bash
# Masuk ke direktori proyek
cd /opt/lampp/htdocs/bagops

# Build dan start semua containers
docker-compose up -d --build

# Lihat status containers
docker-compose ps
```

### 2. Akses Aplikasi

- **BAGOPS Application**: http://localhost/bagops
- **phpMyAdmin**: http://localhost:8081
  - Username: root
  - Password: rootpassword

### 3. Database Management

Database otomatis terbuat dengan:
- Nama: `bagops_db`
- User: `root` / Password: `rootpassword`
- Backup data otomatis di-load dari `backup_20260302_101114.sql`

### 4. Development Workflow

```bash
# Lihat logs
docker-compose logs -f app

# Masuk ke container PHP
docker-compose exec app bash

# Restart specific service
docker-compose restart app

# Stop semua services
docker-compose down

# Hapus semua data (fresh start)
docker-compose down -v
```

## Konfigurasi Environment

Edit file `.env` untuk mengubah pengaturan:

```bash
# Database
DB_HOST=mysql
DB_PASSWORD=rootpassword

# Application
APP_BASE_URL=http://localhost/bagops
APP_DEBUG=true

# Performance
MEMORY_LIMIT=512M
UPLOAD_MAX_FILESIZE=100M
```

## Port Mapping

| Service | Container Port | Host Port | Akses |
|---------|----------------|-----------|-------|
| Apache  | 80             | 80        | http://localhost/bagops |
| MySQL   | 3306           | 3306      | localhost:3306 |
| phpMyAdmin | 80         | 8081      | http://localhost:8081 |
| Redis   | 6379           | 6379      | localhost:6379 |

## Volume Persistence

- `mysql_data`: Database data persisten
- `redis_data`: Redis cache persisten
- `./storage`: File uploads dan logs
- `./sql`: Database initialization scripts

## Troubleshooting

### Port Conflict
Jika port 80/8081/3306 sudah digunakan:

```bash
# Edit docker-compose.yml, ubah port mapping
ports:
  - "8082:80"  # Ganti ke port lain (akses: http://localhost:8082)
```

### Database Connection Error
```bash
# Cek MySQL container status
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql
```

### Permission Issues
```bash
# Fix permission di Linux
sudo chown -R $USER:$USER .
chmod -R 755 storage/
```

### Build Issues
```bash
# Clean build
docker-compose down --rmi all
docker-compose build --no-cache
docker-compose up -d
```

## Development vs Production

### Development (Default)
- Error reporting enabled
- Debug mode on
- Hot reload dengan volume mounting

### Production Setup
```bash
# Buat .env.production
cp .env .env.production

# Edit .env.production:
APP_ENV=production
APP_DEBUG=false
```

## Backup dan Restore

### Backup Database
```bash
# Export database
docker-compose exec mysql mysqldump -u root -prootpassword bagops_db > backup.sql

# Backup volume data
docker run --rm -v bagops_mysql_data:/data -v $(pwd):/backup ubuntu tar cvf /backup/mysql_backup.tar /data
```

### Restore Database
```bash
# Import database
docker-compose exec -T mysql mysql -u root -prootpassword bagops_db < backup.sql
```

## Tips Produktivitas

1. **Hot Reload**: Perubahan kode langsung terlihat tanpa restart
2. **Database Isolation**: Data tidak terpengaruh sistem host
3. **Environment Consistency**: Sama persis di semua mesin
4. **Easy Sharing**: Cukup copy folder proyek dan `docker-compose up`

## Commands Cheat Sheet

```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Rebuild
docker-compose up -d --build

# Logs
docker-compose logs -f [service]

# Shell access
docker-compose exec app bash
docker-compose exec mysql mysql -u root -p

# Clean up
docker-compose down -v --rmi all
```

## Next Steps

1. Coba jalankan `docker-compose up -d`
2. Akses http://localhost/bagops
3. Test semua fitur aplikasi
4. Explore phpMyAdmin di http://localhost:8081
5. Custom environment sesuai kebutuhan

Selamat coding dengan Docker! 🐳
