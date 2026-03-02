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

// Check if user can access reports module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul laporan']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get form data
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? '';
        $period_start = $_POST['period_start'] ?? '';
        $period_end = $_POST['period_end'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $created_by = $currentUser['id'];
        
        // Validate required fields
        if (empty($title) || empty($type) || empty($period_start) || empty($content)) {
            throw new Exception('Field wajib harus diisi');
        }
        
        // Handle file upload
        $file_path = '';
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/reports/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_name = 'report_' . time() . '_' . basename($_FILES['attachment']['name']);
            $file_path = $upload_dir . $file_name;
            
            if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
                throw new Exception('Gagal mengupload file');
            }
            
            $file_path = 'uploads/reports/' . $file_name;
        }
        
        // Insert new report
        $stmt = $pdo->prepare("
            INSERT INTO reports (title, type, period_start, period_end, description, content, file_path, created_by, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([$title, $type, $period_start, $period_end, $description, $content, $file_path, $created_by]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Laporan berhasil dibuat']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
