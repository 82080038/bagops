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

// Check if user can access operations module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul operasi']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get operation ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('ID operasi tidak valid');
        }
        
        // Get operation data
        $stmt = $pdo->prepare("
            SELECT o.*, u.nama as commander_name 
            FROM operations o 
            LEFT JOIN users u ON o.commander_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $operation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$operation) {
            throw new Exception('Operasi tidak ditemukan');
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $operation]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
