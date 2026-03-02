<?php
// AJAX handler for getting kantor data
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

header('Content-Type: application/json');

try {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit();
    }
    
    $stmt = $db->prepare("SELECT * FROM kantor WHERE id = ?");
    $stmt->execute([$id]);
    $kantor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($kantor) {
        echo json_encode(['success' => true, 'data' => $kantor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Data kantor tidak ditemukan']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
