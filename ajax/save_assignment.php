<?php
// AJAX endpoint untuk save assignment
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

// Authentication check
$auth = new Auth();
if (!$auth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only POST requests allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $db = (new Database())->getConnection();
    
    // Get POST data
    $personel_id = $_POST['personel_id'] ?? '';
    $operation_id = $_POST['operation_id'] ?? '';
    $role_assignment = $_POST['role_assignment'] ?? '';
    $status = $_POST['status'] ?? 'assigned';
    
    // Validation
    if (empty($personel_id) || empty($operation_id) || empty($role_assignment)) {
        echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
        exit;
    }
    
    // Check if assignment already exists
    $checkStmt = $db->prepare("SELECT id FROM assignments WHERE personel_id = ? AND operation_id = ?");
    $checkStmt->execute([$personel_id, $operation_id]);
    
    if ($checkStmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Personel sudah ditugaskan ke operasi ini']);
        exit;
    }
    
    // Insert new assignment
    $stmt = $db->prepare("
        INSERT INTO assignments (personel_id, operation_id, role_assignment, status, assigned_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    if ($stmt->execute([$personel_id, $operation_id, $role_assignment, $status])) {
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil dibuat']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat tugas']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
