# BAGOPS Technical Documentation

## 📋 Table of Contents

1. [System Overview](#system-overview)
2. [Database Schema](#database-schema)
3. [Security Implementation](#security-implementation)
4. [Development Roadmap](#development-roadmap)
5. [API Documentation](#api-documentation)
6. [Troubleshooting](#troubleshooting)

---

## 🎯 System Overview

### Application Architecture
BAGOPS POLRES SAMOSIR adalah sistem manajemen operasional kepolisian dengan arsitektur 3-tier:
- **Presentation Layer**: Bootstrap 5, jQuery, AJAX
- **Business Logic Layer**: PHP 8+ classes
- **Data Layer**: MySQL/MariaDB dengan 85 tabel

### Core Components
- **Authentication System**: Role-based access control dengan 5 tier
- **Menu System**: Dynamic menu loading dengan role filtering
- **Audit Logging**: Complete activity tracking
- **Content Management**: 17 modul dengan AJAX handlers

---

## 🗄️ Database Schema

### Core Tables

#### Users & Authentication
```sql
users - User management dengan role-based access
roles - Role definitions dan permissions
access_log - Audit trail untuk semua aktivitas
```

#### Operational Data
```sql
personel - Data personel kepolisian
operations - Manajemen operasi
renops - Rencana operasional
laphar - Laporan harian
aar - After action review
```

#### System Configuration
```sql
menu - Dynamic menu system
submenus - Menu hierarchy
settings - System configuration
```

### Relationships
- `personel.unit_id` → `m_unit_organisasi.id`
- `operations.personel_id` → `personel.id`
- `users.role` → `roles.kode_role`
- `menu.parent_id` → `menu.id` (self-referencing)

---

## 🔒 Security Implementation

### Authentication Flow
1. **Login Process**
   - Username/password validation
   - Session regeneration
   - Audit logging
   - Role assignment

2. **Session Management**
   - 2-hour timeout
   - Auto-logout on timeout
   - Secure cookie configuration
   - Session fixation prevention

3. **Access Control**
   - Module-based permissions
   - Role inheritance
   - Database-driven menu filtering
   - Audit trail for all access attempts

### Security Features Implemented
- ✅ Session timeout validation
- ✅ Comprehensive audit logging
- ✅ SQL injection prevention
- ✅ Input sanitization
- ✅ Role-based access control
- ✅ IP address tracking

### Planned Security Enhancements
- 🔄 CSRF token implementation
- 🔄 Rate limiting
- 🔄 IP-based restrictions
- 🔄 Multi-factor authentication

---

## 🚀 Development Roadmap

### Phase 1: Core Stabilization (Week 1-2)
- [x] Role system implementation
- [x] Security enhancements
- [x] Menu content handlers
- [ ] Database integration testing
- [ ] User experience enhancement

### Phase 2: Feature Enhancement (Week 3-4)
- [ ] Basic reporting system
- [ ] Mobile responsiveness
- [ ] Performance optimization
- [ ] Error handling improvement

### Phase 3: Advanced Features (Month 2)
- [ ] Dynamic permission system
- [ ] Analytics dashboard
- [ ] API development
- [ ] Advanced security features

### Phase 4: Integration & AI (Month 3+)
- [ ] Mobile application
- [ ] AI integration
- [ ] External system integration
- [ ] Advanced analytics

---

## 📡 API Documentation

### AJAX Endpoints

#### Content Loading
```php
POST /ajax/content.php
Parameters:
- page: string (dashboard, personel, operations, etc.)

Response:
{
  "success": boolean,
  "content": string,
  "message": string
}
```

#### Authentication
```php
POST /login.php
Parameters:
- username: string
- password: string

Response:
{
  "success": boolean,
  "message": string,
  "redirect": string (optional)
}
```

### Database Operations

#### CRUD Operations
- **Create**: `save_[module].php`
- **Read**: `get_[module].php`
- **Update**: `update_[module].php`
- **Delete**: `delete_[module].php`

#### Data Validation
- Server-side validation for all inputs
- SQL injection prevention with prepared statements
- XSS prevention with output escaping

---

## 🔧 Troubleshooting

### Common Issues

#### Database Connection
```php
// Check database configuration
$config = require 'config/config.php';
$db = new PDO(
    "mysql:host={$config['db']['host']};dbname={$config['db']['name']}",
    $config['db']['user'],
    $config['db']['pass']
);
```

#### Session Issues
- Check session storage permissions
- Verify session cookie settings
- Clear browser cookies
- Check session timeout configuration

#### AJAX Not Working
- Check browser console for JavaScript errors
- Verify jQuery loaded
- Check network requests
- Verify CSRF tokens (when implemented)

#### Permission Issues
- Verify user role in database
- Check role permissions in Auth class
- Verify menu role assignments
- Check audit logs for access attempts

### Debug Mode
Enable debug mode in `config/config.php`:
```php
'app' => [
    'debug' => true,
    'error_reporting' => E_ALL,
    'display_errors' => 1,
],
```

### Logging
Check error logs:
```bash
tail -f storage/logs/error.log
```

Audit logs:
```sql
SELECT * FROM access_log ORDER BY timestamp DESC LIMIT 10;
```

---

## 📊 Performance Optimization

### Database Optimization
- Add indexes for frequently queried columns
- Use EXPLAIN to analyze slow queries
- Implement query caching
- Use prepared statements

### Application Optimization
- Implement lazy loading for large datasets
- Use caching for frequently accessed data
- Optimize images and assets
- Minimize external dependencies

### Monitoring
- Monitor database query performance
- Track page load times
- Monitor memory usage
- Check error rates

---

## 🧪 Testing

### Unit Testing
```php
// Example test for authentication
public function testLogin() {
    $auth = new Auth($db);
    $result = $auth->login('admin', 'admin123');
    $this->assertTrue($result);
}
```

### Integration Testing
- Test complete user workflows
- Verify data relationships
- Test error scenarios
- Validate security measures

### Security Testing
- SQL injection prevention
- XSS prevention
- Session security
- Access control validation

---

## 📚 Additional Resources

### Documentation Files
- `docs/development_roadmap.md` - Complete development plan
- `docs/sprint_plan.md` - 2-week sprint planning
- `docs/task_list.md` - Prioritized task list
- `docs/security_implementation_report.md` - Security details

### Configuration Files
- `config/config.php` - Application configuration
- `config/database.php` - Database connection
- `.htaccess` - Web server configuration

### Code Examples
- `classes/Auth.php` - Authentication implementation
- `classes/AuditLogger.php` - Audit logging system
- `ajax/content.php` - Content handlers

---

## 🔄 Maintenance

### Daily Tasks
- Review access logs for anomalies
- Monitor failed login attempts
- Check system performance

### Weekly Tasks
- Audit user permissions
- Review role assignments
- Update documentation

### Monthly Tasks
- Security assessment
- Performance optimization
- User feedback review

---

**Last Updated**: March 2026
**Version**: 1.0.0
**Maintainers**: BAGOPS Development Team
