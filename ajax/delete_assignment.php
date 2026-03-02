<?php
// AJAX endpoint untuk delete assignment
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
    
    // Validation
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'ID wajib diisi']);
        exit;
    }
    
    // Delete assignment
    $stmt = $db->prepare("DELETE FROM assignments WHERE id = ?");
    $result = $stmt->execute([$id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Tugas berhasil dihapus']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus tugas']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
