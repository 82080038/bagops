# Remote Development Guide untuk BAGOPS

## 🌐 Coding di Mana Sana Tanpa Bawa Laptop

Anda bisa coding BAGOPS di laptop/PC mana pun di dunia dengan setup yang sama persis!

## 🚀 Cara 1: Git + GitHub (Recommended)

### **Setup di Laptop Baru:**

```bash
# 1. Install prerequisites
# - XAMPP (Windows/Mac) atau LAMP (Linux)
# - Git
# - Docker (optional)

# 2. Clone repository
git clone https://github.com/82080038/bagops.git
cd bagops

# 3. Setup environment
cp .env.example .env
# Edit .env sesuai kebutuhan

# 4. Start development
# Dengan XAMPP:
# - Copy folder ke htdocs
# - Start Apache & MySQL
# - Import database dari sql/bagops_db.sql

# Dengan Docker:
docker-compose up -d --build

# 5. Akses aplikasi
# XAMPP: http://localhost/bagops
# Docker: http://localhost/bagops
```

### **Workflow Sehari-hari:**

```bash
# Pull changes terbaru
git pull origin main

# Coding/development...
# Edit file, tambah fitur, dll

# Test changes
# Buka browser, test fitur baru

# Commit changes
git add .
git commit -m "Add new feature: description"

# Push ke GitHub
git push origin main
```

## 🔄 Cara 2: GitHub Codespaces (Cloud IDE)

### **Setup:**
1. Buka GitHub repository
2. Klik "Code" → "Codespaces" → "Create codespace"
3. Tunggu environment setup (±2 menit)
4. Coding langsung di browser!

### **Keuntungan:**
- ✅ Tidak perlu install apa-apa
- ✅ Environment sudah siap
- ✅ Bisa dari HP/tablet
- ✅ Auto-save ke GitHub

## 🌍 Cara 3: Remote Development Server

### **Setup VPS:**
```bash
# Sewa VPS (DigitalOcean, Vultr, dll)
# Install LAMP stack
# Clone repository
# Setup domain
# Coding via SSH atau Git
```

## 📱 Cara 4: Mobile Development

### **Dari HP/Android:**
1. **Termux** (Linux terminal)
2. **AIDE** (PHP IDE)
3. **GitHub Mobile** (Git management)
4. **VNC Viewer** (Remote desktop)

### **Setup Termux:**
```bash
pkg install php apache2 mysql git
git clone https://github.com/82080038/bagops.git
# Start development server
php -S localhost:8080 -t bagops
```

## 🗂️ Database Synchronization

### **Option 1: Git SQL Dump**
```bash
# Export database
mysqldump -u root -p bagops_db > backup.sql

# Commit ke Git
git add backup.sql
git commit -m "Update database"
git push
```

### **Option 2: Cloud Database**
- MySQL di AWS RDS
- PlanetScale
- Supabase
- Railway

### **Option 3: Docker Volume**
```bash
# Export volume
docker run --rm -v bagops_mysql_data:/data -v $(pwd):/backup ubuntu tar cvf /backup/mysql_backup.tar /data

# Import di laptop lain
docker run --rm -v bagops_mysql_data:/data -v $(pwd):/backup ubuntu xvf /backup/mysql_backup.tar -C /
```

## 🔄 Real-time Collaboration

### **GitHub Flow:**
1. **Branch untuk fitur baru**
```bash
git checkout -b feature/new-dashboard
```

2. **Pull Request untuk review**
3. **Merge ke main setelah approve**

### **Live Share (VS Code):**
- Install Live Share extension
- Share session dengan team
- Real-time coding bersama

## 📋 Environment Checklist

### **Di Setiap Laptop/PC:**

#### **Required Software:**
- [ ] Git
- [ ] PHP 8+
- [ ] MySQL/MariaDB
- [ ] Web server (Apache/Nginx)
- [ ] Code editor (VS Code recommended)

#### **VS Code Extensions:**
- [ ] PHP Intelephense
- [ ] MySQL
- [ ] Docker
- [ ] GitLens
- [ ] Live Share

#### **Setup Commands:**
```bash
# Clone repo
git clone https://github.com/82080038/bagops.git

# Install dependencies
composer install  # jika ada

# Setup database
mysql -u root -p < sql/bagops_db.sql

# Copy config
cp config/config.example.php config/config.php
# Edit config.php

# Start server
# XAMPP: Start via Control Panel
# Docker: docker-compose up -d
# PHP built-in: php -S localhost:8080
```

## 🚀 Quick Start di Laptop Baru

### **One-liner Setup:**
```bash
curl -sSL https://raw.githubusercontent.com/82080038/bagops/main/setup.sh | bash
```

### **Manual Setup (5 menit):**
```bash
# 1. Clone
git clone https://github.com/82080038/bagops.git && cd bagops

# 2. Docker setup (fastest)
docker-compose up -d --build

# 3. Done! Akses http://localhost/bagops
```

## 🌟 Best Practices

### **1. Commit Often**
```bash
# Setiap selesai fitur kecil
git add . && git commit -m "Fix: bug description"

# Setiap hari
git push origin main
```

### **2. Environment Management**
```bash
# Development
cp .env.example .env.dev

# Production  
cp .env.example .env.prod
```

### **3. Backup Strategy**
```bash
# Auto-backup database
crontab -e
# Add: 0 */6 * * * mysqldump -u root -p bagops_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### **4. Security**
- Jangan commit `.env` dengan password asli
- Gunakan environment variables
- Enable 2FA di GitHub

## 📞 Troubleshooting

### **Connection Issues:**
```bash
# Cek koneksi GitHub
ssh -T git@github.com

# Reset git config
git remote set-url origin https://github.com/82080038/bagops.git
```

### **Database Issues:**
```bash
# Reset database
mysql -u root -p -e "DROP DATABASE bagops_db; CREATE DATABASE bagops_db;"
mysql -u root -p bagops_db < sql/bagops_db.sql
```

### **Docker Issues:**
```bash
# Clean rebuild
docker-compose down -v --rmi all
docker-compose up -d --build
```

## 🎯 Scenario Examples

### **Scenario 1: Di Kantor**
```bash
git pull origin main
docker-compose up -d
# Coding...
git add . && git commit -m "Add report feature"
git push origin main
```

### **Scenario 2: Di Rumah (Laptop Berbeda)**
```bash
git clone https://github.com/82080038/bagops.git
cd bagops
docker-compose up -d
# Lanjut coding dari state terbaru
```

### **Scenario 3: Di Cafe (HP)**
```bash
# Buka GitHub.com
# Edit file via web editor
# Commit via mobile app
```

### **Scenario 4: Di Luar Negeri**
```bash
# Buka GitHub Codespaces
# Coding langsung di browser
# Auto-sync ke repository
```

## 🏆 Success Metrics

- ✅ Bisa setup di laptop baru dalam <5 menit
- ✅ Kode selalu up-to-date di semua device
- ✅ Database synchronized
- ✅ Bisa coding dari mana saja
- ✅ Team collaboration smooth

---

**Sekarang Anda bisa coding BAGOPS dari mana saja di dunia! 🌍✈️**
