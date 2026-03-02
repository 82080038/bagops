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
    echo json_encode(['success' => false, 'message' => 'Invalid Laphar ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Check if user owns this Laphar or is admin
    $stmt = $pdo->prepare("SELECT created_by FROM laphar WHERE id = ?");
    $stmt->execute([$id]);
    $laphar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$laphar) {
        echo json_encode(['success' => false, 'message' => 'Laphar not found']);
        exit;
    }

    if ($laphar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    // Delete Laphar record
    $stmt = $pdo->prepare("DELETE FROM laphar WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'success' => true,
        'message' => 'Laphar berhasil dihapus'
    ]);

} catch (Exception $e) {
    error_log("Delete Laphar error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menghapus Laphar: ' . $e->getMessage()
    ]);
}
?>
