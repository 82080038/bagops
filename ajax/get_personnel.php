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

// Check if user can access personnel module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul personel']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get personnel ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('ID personel tidak valid');
        }
        
        // Get personnel data
        $stmt = $pdo->prepare("
            SELECT p.*, r.nama as rank_nama, pos.nama as position_nama, u.nama as unit_nama 
            FROM personel p 
            LEFT JOIN ranks r ON p.rank_id = r.id 
            LEFT JOIN positions pos ON p.position_id = pos.id 
            LEFT JOIN unit u ON p.unit_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $personnel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$personnel) {
            throw new Exception('Personel tidak ditemukan');
        }
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $personnel]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
