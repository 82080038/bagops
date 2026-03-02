# BAGOPS Application Development Rules

## Architecture Principles
- Follow MVC-like separation: config/ (models), classes/ (controllers), ajax/ (handlers), templates/ (views)
- Use PDO for database operations with prepared statements
- Implement proper error handling and logging
- Maintain role-based access control (RBAC) throughout the application
- Use audit logging for all user activities

## Database Standards
- Use InnoDB engine for all tables
- Primary keys should be `id` with AUTO_INCREMENT
- Use `created_at` and `updated_at` timestamps
- Foreign keys must have proper constraints
- Use utf8mb4 charset for full Unicode support
- Implement audit logging with access_log table

## Security Requirements
- All user inputs must be sanitized and validated
- Password hashing with PASSWORD_DEFAULT (bcrypt)
- Session regeneration on login with 2-hour timeout
- CSRF protection for forms (planned)
- SQL injection prevention with prepared statements
- XSS prevention with proper output escaping
- Comprehensive audit logging for all access attempts

## Code Style
- PHP 8+ compatible code
- PSR-4 autoloading standards for classes
- Use meaningful variable and function names
- Add proper PHPDoc comments
- Indent with 4 spaces (no tabs)
- Maximum line length: 120 characters

## Authentication & Authorization
- 5-tier role system: super_admin, admin, kabag_ops, kaur_ops, user
- Module-based access control with database-driven permissions
- Session timeout: 2 hours with auto-logout
- Login attempts must be logged
- Password complexity requirements
- Audit trail for all authentication events

## AJAX Standards
- All AJAX endpoints must validate authentication
- Return JSON responses with structure: {success: boolean, message: string, data: mixed}
- Implement proper error handling
- Use POST method for data modifications
- Include audit logging for all AJAX requests
- Rate limiting for API endpoints (planned)

## File Organization
```
/bagops/
├── config/          # Configuration files
├── classes/         # PHP classes (Auth, AuditLogger)
├── ajax/           # AJAX handlers
├── templates/      # View templates
├── storage/        # File storage, logs
├── sql/            # Database scripts
├── docs/           # Documentation
└── public/         # Public assets
```

## Data Validation Rules
- NRP: 8-20 characters, alphanumeric
- Email: Valid email format
- Phone: 10-15 digits, optional + prefix
- Names: 2-100 characters, letters and spaces
- Dates: YYYY-MM-DD format
- Files: Max 10MB, allowed types: pdf, doc, docx, xls, xlsx

## Error Handling
- Use try-catch blocks for database operations
- Log errors to storage/logs/error.log
- Show user-friendly messages
- Include error details in debug mode only
- Implement proper HTTP status codes
- Audit log all error events

## Performance Guidelines
- Use database indexes for frequently queried columns
- Implement pagination for large datasets
- Cache frequently accessed data (planned)
- Optimize images and assets
- Minimize external dependencies
- Monitor query performance with EXPLAIN

## Testing Requirements
- Test all CRUD operations
- Verify role-based access controls
- Test authentication flows
- Validate input sanitization
- Test error scenarios
- Include security testing
- Performance testing for all modules

## Deployment Standards
- Environment-specific configurations
- Proper file permissions (755 for directories, 644 for files)
- Database migration scripts
- Backup procedures
- Monitoring and logging setup
- Security audit before deployment

## Documentation Standards
- Maintain comprehensive technical documentation
- Update API documentation for all endpoints
- Include user guides for all features
- Document security procedures
- Keep development roadmap updated
- Maintain change logs

## Security Auditing
- Regular security assessments
- Access log review and analysis
- Vulnerability scanning
- Penetration testing (planned)
- Security patch management
- Incident response procedures

## Quality Assurance
- Code review for all changes
- Automated testing where possible
- User acceptance testing
- Performance benchmarking
- Security validation
- Documentation updates

## Development Workflow
- Follow sprint planning methodology
- Daily standups and progress tracking
- Feature branch development
- Code review before merge
- Testing before deployment
- Documentation updates with each feature

## Current Implementation Status
- ✅ Role-based access control (5 tiers)
- ✅ Session timeout validation (2 hours)
- ✅ Comprehensive audit logging
- ✅ Menu content handlers (17 functions)
- ✅ Database schema (85 tables)
- ✅ Authentication system with security
- 🔄 Mobile responsiveness (in progress)
- 🔄 Basic reporting system (planned)
- 🔄 CSRF protection (planned)
- 🔄 Rate limiting (planned)
