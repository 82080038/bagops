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

try {
    $pdo = (new Database())->getConnection();
    
    // Get ranks
    $stmt = $pdo->query("SELECT id, nama FROM ranks ORDER BY nama");
    $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get positions
    $stmt = $pdo->query("SELECT id, nama FROM positions ORDER BY nama");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get units
    $stmt = $pdo->query("SELECT id, nama FROM unit ORDER BY nama");
    $units = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'ranks' => $ranks,
        'positions' => $positions,
        'units' => $units
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
