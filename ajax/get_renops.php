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
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
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
        
        // Get RENOPS data
        $stmt = $pdo->prepare("
            SELECT r.*, e.title as event_title, e.type as event_type, e.start_at, e.location 
            FROM renops r 
            LEFT JOIN events e ON r.event_id = e.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $renops = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$renops) {
            throw new Exception('RENOPS tidak ditemukan');
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $renops]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
