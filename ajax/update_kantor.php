<?php
// AJAX handler for updating kantor data
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

// Check permissions
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

header('Content-Type: application/json');

try {
    $id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if ($id === 0 || empty($nama) || empty($jenis) || empty($alamat)) {
        echo json_encode(['success' => false, 'message' => 'ID, nama, jenis, dan alamat harus diisi']);
        exit();
    }
    
    // Polres tidak boleh punya parent
    if ($jenis === 'polres') {
        $parentId = null;
    } else {
        // Non-polres harus punya parent
        if (empty($parentId)) {
            echo json_encode(['success' => false, 'message' => 'Polsek dan Pos Polisi harus memiliki kantor induk']);
            exit();
        }
    }
    
    $stmt = $db->prepare("UPDATE kantor SET nama = ?, jenis = ?, parent_id = ?, alamat = ?, telepon = ?, email = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$nama, $jenis, $parentId, $alamat, $telepon, $email, $id]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Data kantor berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data kantor']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
