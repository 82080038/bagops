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
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $type = $_POST['type'] ?? '';
        $period_start = $_POST['period_start'] ?? '';
        $period_end = $_POST['period_end'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // Validate required fields
        if (empty($id) || empty($title) || empty($type) || empty($period_start) || empty($content)) {
            throw new Exception('Field wajib harus diisi');
        }
        
        // Check if report exists
        $stmt = $pdo->prepare("SELECT id FROM reports WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('Laporan tidak ditemukan');
        }
        
        // Handle file upload
        $file_path = null;
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
        
        // Update report
        $query = "
            UPDATE reports 
            SET title = ?, type = ?, period_start = ?, period_end = ?, description = ?, content = ?, updated_at = NOW()
        ";
        $params = [$title, $type, $period_start, $period_end, $description, $content];
        
        if ($file_path) {
            $query .= ", file_path = ?";
            $params[] = $file_path;
        }
        
        $query .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Laporan berhasil diperbarui']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
