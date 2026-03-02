<?php
// AJAX endpoint untuk update assignment
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
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    $role_assignment = $_POST['role_assignment'] ?? null;
    
    // Validation
    if (empty($id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'ID dan status wajib diisi']);
        exit;
    }
    
    // Update assignment
    if ($role_assignment) {
        $stmt = $db->prepare("
            UPDATE assignments 
            SET status = ?, role_assignment = ? 
            WHERE id = ?
        ");
        $result = $stmt->execute([$status, $role_assignment, $id]);
    } else {
        $stmt = $db->prepare("
            UPDATE assignments 
            SET status = ? 
            WHERE id = ?
        ");
        $result = $stmt->execute([$status, $id]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui tugas']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
