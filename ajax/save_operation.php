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
if (!in_array($userRole, ['super_admin', 'admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul operasi']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $start_date = $_POST['start_date'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_date = $_POST['end_date'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        $location = trim($_POST['location'] ?? '');
        $status = $_POST['status'] ?? 'planned';
        $commander_id = $_POST['commander_id'] ?? '';
        $created_by = $currentUser['id'];
        
        // Validate required fields
        if (empty($title) || empty($start_date) || empty($start_time) || empty($location) || empty($commander_id)) {
            throw new Exception('Semua field wajib diisi');
        }
        
        // Insert new operation
        $stmt = $pdo->prepare("
            INSERT INTO operations (title, description, start_date, start_time, end_date, end_time, location, status, commander_id, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([$title, $description, $start_date, $start_time, $end_date, $end_time, $location, $status, $commander_id, $created_by]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Operasi berhasil ditambahkan']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
