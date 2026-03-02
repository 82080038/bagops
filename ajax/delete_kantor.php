<?php
// AJAX handler for deleting kantor data
session_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../classes/Auth.php';

// Initialize database and auth
$db = (new Database())->getConnection();
$auth = new Auth($db);

// Check authentication
if (!$auth->isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check permissions
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

if (!in_array($userRole, ['super_admin', 'admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

header('Content-Type: application/json');

try {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    // Check if kantor exists
    $stmt = $db->prepare("SELECT id FROM kantor WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Data kantor tidak ditemukan']);
        exit();
    }
    
    $stmt = $db->prepare("DELETE FROM kantor WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Data kantor berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus data kantor']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
