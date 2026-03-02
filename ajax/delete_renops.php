<?php
// Start session and include required files
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Check authentication
require_once '../classes/Auth.php';
$auth = new Auth((new Database())->getConnection());
if (!$auth->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check if user can access RENOPS module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul RENOPS']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get RENOPS ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('ID RENOPS tidak valid');
        }
        
        // Check if RENOPS exists
        $stmt = $pdo->prepare("SELECT id FROM renops WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('RENOPS tidak ditemukan');
        }
        
        // Delete RENOPS
        $stmt = $pdo->prepare("DELETE FROM renops WHERE id = ?");
        $stmt->execute([$id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'RENOPS berhasil dihapus']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
