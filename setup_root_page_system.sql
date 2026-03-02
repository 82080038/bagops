-- Root-based Page System Database Structure
-- Enterprise-grade architecture for BAGOPS POLRES SAMOSIR

-- =============================================
-- PAGES TABLE - Master page definitions
-- =============================================
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(100) NOT NULL UNIQUE,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    target_role ENUM('all', 'super_admin', 'admin', 'kabag_ops', 'kaur_ops', 'user') DEFAULT 'all',
    is_active BOOLEAN DEFAULT TRUE,
    order_index INT DEFAULT 0,
    parent_page_id INT NULL,
    page_type ENUM('standard', 'dashboard', 'report', 'settings', 'profile') DEFAULT 'standard',
    layout_type ENUM('default', 'full_width', 'sidebar', 'minimal') DEFAULT 'default',
    requires_auth BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_page_id) REFERENCES pages(id) ON DELETE SET NULL,
    INDEX idx_page_key (page_key),
    INDEX idx_target_role (target_role),
    INDEX idx_is_active (is_active),
    INDEX idx_order_index (order_index)
);

-- =============================================
-- PAGE DETAILS TABLE - Extended page information
-- =============================================
CREATE TABLE IF NOT EXISTS page_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    content_data LONGTEXT,
    template_file VARCHAR(255),
    meta_title VARCHAR(200),
    meta_description TEXT,
    meta_keywords VARCHAR(500),
    custom_css TEXT,
    custom_js TEXT,
    layout_type ENUM('default', 'full_width', 'sidebar', 'minimal') DEFAULT 'default',
    sidebar_enabled BOOLEAN DEFAULT TRUE,
    header_enabled BOOLEAN DEFAULT TRUE,
    footer_enabled BOOLEAN DEFAULT TRUE,
    breadcrumb_enabled BOOLEAN DEFAULT TRUE,
    search_enabled BOOLEAN DEFAULT TRUE,
    notifications_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_id (page_id)
);

-- =============================================
-- PAGE REQUIREMENTS TABLE - Data requirements per page
-- =============================================
CREATE TABLE IF NOT EXISTS page_requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    requirement_type ENUM('table', 'statistic', 'chart', 'filter', 'action', 'permission') NOT NULL,
    requirement_key VARCHAR(100) NOT NULL,
    requirement_value TEXT,
    is_required BOOLEAN DEFAULT TRUE,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    INDEX idx_page_requirement (page_id, requirement_type),
    INDEX idx_requirement_key (requirement_key)
);

-- =============================================
-- PAGE PERMISSIONS TABLE - Fine-grained permissions
-- =============================================
CREATE TABLE IF NOT EXISTS page_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    role_name ENUM('super_admin', 'admin', 'kabag_ops', 'kaur_ops', 'user') NOT NULL,
    permission_type ENUM('view', 'create', 'edit', 'delete', 'export', 'import', 'manage', 'assign_personnel') NOT NULL,
    is_granted BOOLEAN DEFAULT FALSE,
    conditions JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (page_id) REFERENCES pages(id) ON DELETE CASCADE,
    UNIQUE KEY unique_page_role_permission (page_id, role_name, permission_type),
    INDEX idx_role_permission (role_name, permission_type)
);

-- =============================================
-- ACCESS LOG TABLE - Security and audit logging
-- =============================================
CREATE TABLE IF NOT EXISTS access_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page VARCHAR(100) NOT NULL,
    user_id INT NULL,
    user_role VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    access_result ENUM('granted', 'denied', 'redirected') NOT NULL,
    session_id VARCHAR(255),
    access_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_access_time (access_time),
    INDEX idx_page (page),
    INDEX idx_user_role (user_role),
    INDEX idx_access_result (access_result)
);

-- =============================================
-- INSERT SAMPLE PAGE DATA
-- =============================================

-- Main pages
INSERT INTO pages (page_key, title, description, target_role, order_index, page_type, layout_type) VALUES
('dashboard', 'Dashboard Utama', 'Halaman dashboard dengan statistik real-time', 'all', 1, 'dashboard', 'sidebar'),
('personel', 'Data Personel', 'Manajemen data personel kepolisian', 'all', 2, 'standard', 'sidebar'),
('operations', 'Data Operasi', 'Manajemen operasi kepolisian', 'kabag_ops', 3, 'standard', 'sidebar'),
('reports', 'Laporan', 'Sistem pelaporan operasional', 'all', 4, 'report', 'sidebar'),
('assignments', 'Tugas', 'Manajemen tugas dan penugasan', 'all', 5, 'standard', 'sidebar'),
('settings', 'Pengaturan', 'Pengaturan sistem', 'super_admin', 6, 'settings', 'sidebar'),
('profile', 'Profile', 'Profile pengguna', 'all', 7, 'profile', 'minimal'),
('help', 'Bantuan', 'Panduan dan bantuan sistem', 'all', 8, 'standard', 'full_width');

-- Page details
INSERT INTO page_details (page_id, meta_title, meta_description, meta_keywords, layout_type) VALUES
(1, 'Dashboard - BAGOPS POLRES SAMOSIR', 'Dashboard utama sistem BAGOPS POLRES SAMOSIR dengan statistik real-time', 'dashboard, bagops, polres, samosir, statistik', 'sidebar'),
(2, 'Data Personel - BAGOPS POLRES SAMOSIR', 'Manajemen data personel kepolisian POLRES SAMOSIR', 'personel, data, kepolisian, pegawai, bagops', 'sidebar'),
(3, 'Data Operasi - BAGOPS POLRES SAMOSIR', 'Manajemen operasi kepolisian POLRES SAMOSIR', 'operasi, kegiatan, polisi, bagops', 'sidebar'),
(4, 'Laporan - BAGOPS POLRES SAMOSIR', 'Sistem pelaporan operasional POLRES SAMOSIR', 'laporan, report, dokumentasi, bagops', 'sidebar'),
(5, 'Tugas - BAGOPS POLRES SAMOSIR', 'Manajemen tugas dan penugasan personel', 'tugas, assignment, penugasan, bagops', 'sidebar'),
(6, 'Pengaturan - BAGOPS POLRES SAMOSIR', 'Pengaturan sistem BAGOPS POLRES SAMOSIR', 'settings, pengaturan, konfigurasi, admin', 'sidebar'),
(7, 'Profile - BAGOPS POLRES SAMOSIR', 'Profile pengguna sistem BAGOPS', 'profile, user, pengguna, akun', 'minimal'),
(8, 'Bantuan - BAGOPS POLRES SAMOSIR', 'Panduan dan bantuan sistem BAGOPS', 'bantuan, help, panduan, tutorial', 'full_width');

-- Page requirements for dashboard
INSERT INTO page_requirements (page_id, requirement_type, requirement_key, requirement_value, is_required, order_index) VALUES
(1, 'table', 'personel', 'personel table with active records', TRUE, 1),
(1, 'table', 'operations', 'operations table with active status', TRUE, 2),
(1, 'table', 'daily_reports', 'daily reports for today', TRUE, 3),
(1, 'table', 'assignments', 'pending assignments', TRUE, 4),
(1, 'statistic', 'total_personel', 'COUNT of active personel', TRUE, 10),
(1, 'statistic', 'active_operations', 'COUNT of active operations', TRUE, 11),
(1, 'statistic', 'today_reports', 'COUNT of today reports', TRUE, 12),
(1, 'statistic', 'pending_tasks', 'COUNT of pending assignments', TRUE, 13),
(1, 'chart', 'personel_chart', 'personel distribution by rank', TRUE, 20),
(1, 'chart', 'operations_chart', 'operations distribution by status', TRUE, 21),
(1, 'permission', 'view_dashboard', 'Basic dashboard access', TRUE, 30);

-- Page requirements for personel
INSERT INTO page_requirements (page_id, requirement_type, requirement_key, requirement_value, is_required, order_index) VALUES
(2, 'table', 'personel', 'Complete personel data with joins', TRUE, 1),
(2, 'table', 'pangkat', 'Rank data for filters', TRUE, 2),
(2, 'table', 'jabatan', 'Position data for filters', TRUE, 3),
(2, 'table', 'kantor', 'Office data for filters', TRUE, 4),
(2, 'filter', 'unit', 'Office/unit filter dropdown', TRUE, 10),
(2, 'filter', 'pangkat', 'Rank filter dropdown', TRUE, 11),
(2, 'filter', 'status', 'Status filter (active/inactive)', TRUE, 12),
(2, 'action', 'create', 'Create new personel', TRUE, 20),
(2, 'action', 'edit', 'Edit existing personel', TRUE, 21),
(2, 'action', 'delete', 'Delete personel', TRUE, 22),
(2, 'action', 'import', 'Import personel from Excel', TRUE, 23),
(2, 'action', 'export', 'Export personel to Excel', TRUE, 24),
(2, 'permission', 'view_personel', 'View personel data', TRUE, 30),
(2, 'permission', 'manage_personel', 'Full personel management', TRUE, 31);

-- Page requirements for operations
INSERT INTO page_requirements (page_id, requirement_type, requirement_key, requirement_value, is_required, order_index) VALUES
(3, 'table', 'operations', 'Operations with personnel count', TRUE, 1),
(3, 'table', 'operation_personnel', 'Personnel assigned to operations', TRUE, 2),
(3, 'table', 'operation_reports', 'Reports related to operations', TRUE, 3),
(3, 'filter', 'status', 'Operation status filter', TRUE, 10),
(3, 'filter', 'date_range', 'Date range filter', TRUE, 11),
(3, 'filter', 'type', 'Operation type filter', TRUE, 12),
(3, 'action', 'create', 'Create new operation', TRUE, 20),
(3, 'action', 'edit', 'Edit existing operation', TRUE, 21),
(3, 'action', 'delete', 'Delete operation', TRUE, 22),
(3, 'action', 'assign_personnel', 'Assign personnel to operation', TRUE, 23),
(3, 'permission', 'view_operations', 'View operations data', TRUE, 30),
(3, 'permission', 'manage_operations', 'Full operations management', TRUE, 31);

-- Page permissions by role
INSERT INTO page_permissions (page_id, role_name, permission_type, is_granted) VALUES
-- Dashboard permissions
(1, 'super_admin', 'view', TRUE),
(1, 'admin', 'view', TRUE),
(1, 'kabag_ops', 'view', TRUE),
(1, 'kaur_ops', 'view', TRUE),
(1, 'user', 'view', TRUE),

-- Personel permissions
(2, 'super_admin', 'view', TRUE),
(2, 'super_admin', 'create', TRUE),
(2, 'super_admin', 'edit', TRUE),
(2, 'super_admin', 'delete', TRUE),
(2, 'super_admin', 'import', TRUE),
(2, 'super_admin', 'export', TRUE),
(2, 'admin', 'view', TRUE),
(2, 'admin', 'create', TRUE),
(2, 'admin', 'edit', TRUE),
(2, 'admin', 'delete', TRUE),
(2, 'admin', 'import', TRUE),
(2, 'admin', 'export', TRUE),
(2, 'kabag_ops', 'view', TRUE),
(2, 'kabag_ops', 'edit', TRUE),
(2, 'kabag_ops', 'export', TRUE),
(2, 'kaur_ops', 'view', TRUE),
(2, 'kaur_ops', 'export', TRUE),
(2, 'user', 'view', TRUE),

-- Operations permissions
(3, 'super_admin', 'view', TRUE),
(3, 'super_admin', 'create', TRUE),
(3, 'super_admin', 'edit', TRUE),
(3, 'super_admin', 'delete', TRUE),
(3, 'admin', 'view', TRUE),
(3, 'admin', 'create', TRUE),
(3, 'admin', 'edit', TRUE),
(3, 'admin', 'delete', TRUE),
(3, 'kabag_ops', 'view', TRUE),
(3, 'kabag_ops', 'create', TRUE),
(3, 'kabag_ops', 'edit', TRUE),
(3, 'kabag_ops', 'delete', TRUE),
(3, 'kabag_ops', 'assign_personnel', TRUE),
(3, 'kaur_ops', 'view', TRUE),
(3, 'user', 'view', FALSE),

-- Settings permissions (super_admin only)
(6, 'super_admin', 'view', TRUE),
(6, 'super_admin', 'create', TRUE),
(6, 'super_admin', 'edit', TRUE),
(6, 'super_admin', 'delete', TRUE);

-- =============================================
-- CREATE INDEXES FOR PERFORMANCE
-- =============================================

-- Composite indexes for common queries
CREATE INDEX idx_pages_active_role_order ON pages(is_active, target_role, order_index);
CREATE INDEX idx_access_log_time_result ON access_log(access_time, access_result);
CREATE INDEX idx_page_requirements_page_type ON page_requirements(page_id, requirement_type);
CREATE INDEX idx_page_permissions_role_granted ON page_permissions(role_name, is_granted);

-- =============================================
-- CREATE VIEWS FOR COMMON QUERIES
-- =============================================

-- View for active pages by role
CREATE VIEW active_pages_by_role AS
SELECT 
    p.page_key,
    p.title,
    p.description,
    p.target_role,
    p.page_type,
    p.layout_type,
    pd.meta_title,
    pd.meta_description,
    pd.custom_css,
    pd.custom_js,
    p.order_index
FROM pages p
LEFT JOIN page_details pd ON p.id = pd.page_id
WHERE p.is_active = TRUE
ORDER BY p.target_role, p.order_index;

-- View for user accessible pages
CREATE VIEW user_accessible_pages AS
SELECT 
    p.page_key,
    p.title,
    p.description,
    p.page_type,
    p.layout_type,
    p.target_role,
    p.order_index,
    pd.meta_title,
    pd.custom_css,
    pd.custom_js
FROM pages p
LEFT JOIN page_details pd ON p.id = pd.page_id
WHERE p.is_active = TRUE 
AND (p.target_role = 'all' OR p.target_role = 'user')
ORDER BY p.order_index;

-- =============================================
-- SAMPLE DATA FOR TESTING
-- =============================================

-- Insert some sample access log entries for testing
INSERT INTO access_log (page, user_role, ip_address, user_agent, access_result) VALUES
('dashboard', 'super_admin', '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'granted'),
('personel', 'admin', '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'granted'),
('operations', 'kabag_ops', '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'granted'),
('settings', 'user', '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'denied'),
('nonexistent', 'admin', '127.0.0.1', 'Mozilla/5.0 (Test Browser)', 'denied');

-- =============================================
-- SETUP COMPLETE
-- =============================================

-- The database is now ready for the root-based page system
-- All tables, indexes, views, and sample data are in place
