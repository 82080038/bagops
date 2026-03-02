---
description: Database backup and restore procedures
---
# Database Backup Workflow

## Regular Backup
```bash
# Create backup with timestamp
mysqldump -u root -proot bagops_db > backups/bagops_db_$(date +%Y%m%d_%H%M%S).sql

# Compress backup
gzip backups/bagops_db_*.sql
```

## Before Major Changes
1. Create full backup
2. Test backup integrity
3. Document changes
4. Create migration script if needed

## Restore Procedure
```bash
# Stop application
sudo /opt/lampp/lampp stop

# Drop and recreate database
mysql -u root -proot -e "DROP DATABASE IF EXISTS bagops_db; CREATE DATABASE bagops_db;"

# Restore from backup
mysql -u root -proot bagops_db < backup_file.sql

# Restart services
sudo /opt/lampp/lampp start
```

## Automated Backup
- Set up cron job for daily backups
- Keep 30 days of backups
- Monitor backup success/failure
- Test restore process quarterly
