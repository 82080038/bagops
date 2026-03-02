-- Database: aplikasi_dashboard
CREATE DATABASE IF NOT EXISTS aplikasi_dashboard;
USE aplikasi_dashboard;

-- Tabel users untuk autentikasi
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Tabel menu untuk navigasi dinamis
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-circle',
    url VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    order_index INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu(id) ON DELETE SET NULL
);

-- Insert data user admin (password: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- Insert data menu untuk navigasi
INSERT INTO menu (name, icon, url, order_index) VALUES 
('Dashboard', 'fas fa-tachometer-alt', 'dashboard', 1),
('Profile', 'fas fa-user', 'profile', 2),
('Settings', 'fas fa-cog', 'settings', 3),
('Users', 'fas fa-users', 'users', 4),
('Reports', 'fas fa-chart-bar', 'reports', 5);

-- Insert sub menu untuk Users
INSERT INTO menu (name, icon, url, parent_id, order_index) VALUES 
('List Users', 'fas fa-list', 'users/list', 4, 1),
('Add User', 'fas fa-plus', 'users/add', 4, 2);
