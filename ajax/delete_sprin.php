<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid SPRIN ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Check if user owns this SPRIN or is admin
    $stmt = $pdo->prepare("SELECT created_by FROM sprin WHERE id = ?");
    $stmt->execute([$id]);
    $sprin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sprin) {
        echo json_encode(['success' => false, 'message' => 'SPRIN not found']);
        exit;
    }

    if ($sprin['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    // Delete SPRIN record
    $stmt = $pdo->prepare("DELETE FROM sprin WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'SPRIN berhasil dihapus'
    ]);

} catch (Exception $e) {
    error_log("Delete SPRIN error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus SPRIN: ' . $e->getMessage()
    ]);
}
?>
