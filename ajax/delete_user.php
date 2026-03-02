<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication and admin access
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

// Only admins can manage users
$currentUser = $auth->getCurrentUser();
if ($currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();

    // Prevent users from deleting themselves
    if ($id == $currentUser['id']) {
        echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus akun sendiri']);
        exit;
    }

    // Prevent deletion of super_admin accounts by non-super_admin
    if ($currentUser['role'] !== 'super_admin') {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $userToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userToDelete && $userToDelete['role'] === 'super_admin') {
            echo json_encode(['success' => false, 'message' => 'Tidak dapat menghapus akun super admin']);
            exit;
        }
    }

    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User tidak ditemukan']);
        exit;
    }

    // Delete user record (consider soft delete in production)
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'User ' . htmlspecialchars($user['username']) . ' berhasil dihapus'
    ]);

} catch (Exception $e) {
    error_log("Delete User error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage()
    ]);
}
?>
