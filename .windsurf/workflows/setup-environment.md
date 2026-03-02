---
description: Setup new BAGOPS development environment
---
# New Development Environment Setup

## Prerequisites
- XAMPP installed and running
- Git for version control
- Code editor (VS Code recommended)

## Setup Steps

1. **Clone or create project directory**
   ```bash
   cd /opt/lampp/htdocs/
   git clone <repository-url> bagops
   # or create new directory
   ```

2. **Database Setup**
   ```bash
   # Start XAMPP services
   sudo /opt/lampp/lampp start
   
   # Create database and import schema
   mysql -u root -proot -e "CREATE DATABASE bagops_db;"
   mysql -u root -proot bagops_db < sql/bagops_db.sql
   ```

3. **Configuration**
   - Copy `config/config.php.example` to `config/config.php`
   - Update database credentials
   - Set appropriate file permissions

4. **Verify Installation**
   - Access http://localhost/bagops
   - Test login with default credentials
   - Verify all modules are accessible

## Common Issues
- Database connection errors: Check XAMPP status and credentials
- Permission errors: Set proper directory permissions (755)
- Session issues: Clear browser cookies and restart Apache
