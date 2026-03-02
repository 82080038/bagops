---
description: Add new module to BAGOPS application
---
# Add New Module Workflow

## 1. Database Setup
- Create required tables in `sql/` directory
- Add menu items to `menu` table
- Set up role permissions if needed

## 2. Backend Implementation
- Create AJAX handlers in `ajax/` directory
- Implement CRUD operations
- Add validation and error handling

## 3. Frontend Integration
- Add content functions to `ajax/content.php`
- Create HTML templates
- Add JavaScript for dynamic interactions

## 4. Menu Integration
- Add menu item to database
- Update role permissions
- Test access control

## 5. Testing
- Test all CRUD operations
- Verify role-based access
- Test error scenarios
- Update documentation

## Required Files
- `ajax/get_[module].php`
- `ajax/save_[module].php`
- `ajax/update_[module].php`
- `ajax/delete_[module].php`
- Content function in `ajax/content.php`
