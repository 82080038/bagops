<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once 'AuditLogger.php';

class Auth {
    private $db;
    private $auditLogger;

    public function __construct($database) {
        $this->db = $database;
        $this->auditLogger = new AuditLogger($database);
    }

    public function login($username, $password) {
        try {
            // Ensure we have a valid database connection
            if (!$this->db) {
                throw new Exception("Database connection not available");
            }

            // Auto login for super_admin
            if ($username === 'super_admin') {
                $_SESSION['user_id'] = 260;
                $_SESSION['username'] = 'super_admin';
                $_SESSION['role'] = 'super_admin';
                $_SESSION['logged_in'] = true;
                $_SESSION['login_time'] = time();
                $_SESSION['email'] = 'super_admin@demo.com';
                $_SESSION['full_name'] = 'Super Administrator';
                $_SESSION['avatar'] = null;

                $this->auditLogger->logAction(260, 'LOGIN_SUCCESS', 'Super admin auto login');
                $this->updateLastLogin(260);

                return true;
            } else {
                // Try username field for regular users
                $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND is_active = 1 LIMIT 1");
                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    $storedPassword = (string)($user['password'] ?? '');
                    $isHashedPassword = str_starts_with($storedPassword, '$2y$');
                    $credentialsValid = false;

                    if ($storedPassword === '') {
                        $credentialsValid = false;
                    } elseif ($isHashedPassword) {
                        $credentialsValid = password_verify($password, $storedPassword);
                    } else {
                        $credentialsValid = hash_equals($storedPassword, $password);
                    }

                    if ($credentialsValid) {
                        // Regenerate session ID for security
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        session_regenerate_id(true);

                        // Set session data
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'] ?? $user['nrp'] ?? $user['name'] ?? $username; // Use username, nrp, or name as username
                        $_SESSION['email'] = $user['email'] ?? '';
                        $_SESSION['full_name'] = $user['name'] ?? $user['username'] ?? $user['nrp'] ?? 'User';
                        $_SESSION['role'] = $user['role'] ?? 'user';
                        $_SESSION['avatar'] = $user['avatar'] ?? null;
                        $_SESSION['logged_in'] = true;
                        $_SESSION['login_time'] = time();

                        // Ensure super_admin has correct username
                        if ($user['role'] === 'super_admin') {
                            $_SESSION['username'] = 'super_admin';
                        }

                        // Log successful login
                        $this->auditLogger->logAction($user['id'], 'LOGIN_SUCCESS', 'User logged in successfully');

                        // Update last login
                        $this->updateLastLogin($user['id']);

                        return true;
                    } else {
                        // Log failed login attempt
                        $this->auditLogger->logAction(0, 'LOGIN_FAILED', "Failed login attempt for username: $username");
                    }
                }
            }
        } catch(Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        $userId = $_SESSION['user_id'] ?? 0;
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Log logout before destroying session
        try {
            if ($userId > 0) {
                $this->auditLogger->logAction($userId, 'LOGOUT', 'User logged out');
            }
        } catch (Exception $e) {
            error_log("Logout audit log error: " . $e->getMessage());
        }

        // Destroy session
        session_unset();
        session_destroy();

        // Clear session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    public function isLoggedIn() {
        $loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
        
        // Check session timeout
        if ($loggedIn && isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 7200)) {
            $this->logout();
            return false;
        }
        
        return $loggedIn;
    }

    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
        
        // Check session timeout
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 7200)) {
            $this->logout();
            header('Location: login.php?timeout=1');
            exit();
        }
    }

    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            try {
                // Get fresh user data from database
                $stmt = $this->db->prepare("SELECT id, username, nama, role, email FROM users WHERE id = :id LIMIT 1");
                $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user) {
                    return [
                        'id' => $user['id'],
                        'username' => $user['username'] ?? $_SESSION['username'],
                        'nama' => $user['nama'],
                        'email' => $user['email'] ?? '',
                        'full_name' => $user['nama'] ?? $user['username'] ?? 'User',
                        'role' => $user['role'],
                        'avatar' => null
                    ];
                }
            } catch(Exception $e) {
                error_log("Get current user error: " . $e->getMessage());
            }
            
            // Fallback to session data if database fails
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'] ?? '',
                'full_name' => $_SESSION['full_name'],
                'role' => $_SESSION['role'],
                'avatar' => $_SESSION['avatar'] ?? null
            ];
        }
        return null;
    }

    public function register($nrp, $password) {
        try {
            // Ensure we have a valid database connection
            if (!$this->db) {
                throw new Exception("Database connection not available");
            }

            // Check if NRP exists and is active
            $stmt = $this->db->prepare("SELECT * FROM users WHERE nrp = :nrp AND is_active = 1 LIMIT 1");
            $stmt->bindParam(':nrp', $nrp, PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("NRP tidak ditemukan atau tidak aktif");
            }

            if (!empty($user['password'])) {
                throw new Exception("NRP ini sudah terdaftar");
            }

            // Register user with password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE users SET password = :password, updated_at = NOW() WHERE nrp = :nrp");
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':nrp', $nrp, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return true;
            } else {
                throw new Exception("Gagal menyimpan password");
            }

        } catch(Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            throw $e;
        }
    }

    public function isNRPRegistered($nrp) {
        try {
            $stmt = $this->db->prepare("SELECT password FROM users WHERE nrp = :nrp LIMIT 1");
            $stmt->bindParam(':nrp', $nrp, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user && !empty($user['password']);
        } catch(Exception $e) {
            return false;
        }
    }

    public function isNRPValid($nrp) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE nrp = :nrp AND is_active = 1 LIMIT 1");
            $stmt->bindParam(':nrp', $nrp, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
        } catch(Exception $e) {
            return false;
        }
    }

    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userRole = $_SESSION['role'] ?? 'user';
        
        // Super admin has all permissions
        if ($userRole === 'super_admin') {
            return true;
        }
        
        $permissions = $this->getRolePermissions($userRole);
        return in_array($permission, $permissions);
    }
    
    public function canAccessModule($module) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $userRole = $_SESSION['role'] ?? 'user';
        $userId = $_SESSION['user_id'] ?? 0;
        
        // Special check for super_admin username
        if ($_SESSION['username'] === 'super_admin') {
            // $this->auditLogger->logAccess($userId, $module, true);
            return true;
        }
        
        // Map numeric roles to string roles
        $roleMap = [
            1 => 'super_admin',
            2 => 'admin',
            3 => 'kabag_ops',
            4 => 'kaur_ops',
            5 => 'user'
        ];
        $userRole = $roleMap[$userRole] ?? $userRole;
        
        // Super admin can access all modules
        if ($userRole === 'super_admin') {
            $this->auditLogger->logAccess($userId, $module, true);
            return true;
        }
        
        $accessMatrix = [
            'admin' => ['dashboard', 'users', 'personel', 'operations', 'renops', 'posko', 'reports', 'settings', 'pengaturan', 'documents', 'calendar', 'analytics', 'mobile'],
            'kabag_ops' => ['dashboard', 'renops', 'sprin', 'posko', 'reports', 'personel', 'analytics'],
            'kaur_ops' => ['dashboard', 'renops', 'sprin', 'reports', 'personel'],
            'user' => ['dashboard', 'profile', 'assignments', 'reports']
        ];
        
        $hasAccess = in_array($module, $accessMatrix[$userRole] ?? []) ?? false;
        
        // Log access attempt
        $this->auditLogger->logAccess($userId, $module, $hasAccess);
        
        return $hasAccess;
    }
    
    private function getRolePermissions($role) {
        $permissions = [
            'super_admin' => ['all'],
            'admin' => ['view', 'create', 'edit', 'delete', 'approve', 'manage_users'],
            'kabag_ops' => ['view', 'create', 'edit', 'approve', 'monitor'],
            'kaur_ops' => ['view', 'create', 'edit'],
            'user' => ['view', 'report']
        ];
        
        return $permissions[$role] ?? [];
    }
    
    public function isSuperAdmin() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $_SESSION['role'] === 'super_admin';
    }

    private function updateLastLogin($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET updated_at = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();
        } catch(Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }
}
?>
