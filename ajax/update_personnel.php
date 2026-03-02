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

// Check if user can access personnel module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul personel']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get form data
        $id = $_POST['id'] ?? '';
        $nrp = trim($_POST['nrp'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $rank_id = $_POST['rank_id'] ?? '';
        $position_id = $_POST['position_id'] ?? '';
        $unit_id = $_POST['unit_id'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Validate required fields
        if (empty($id) || empty($nrp) || empty($nama) || empty($rank_id) || empty($position_id) || empty($unit_id)) {
            throw new Exception('Semua field wajib diisi');
        }
        
        // Check if personnel exists
        $stmt = $pdo->prepare("SELECT id FROM personel WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            throw new Exception('Personel tidak ditemukan');
        }
        
        // Check if NRP already exists (excluding current record)
        $stmt = $pdo->prepare("SELECT id FROM personel WHERE nrp = ? AND id != ?");
        $stmt->execute([$nrp, $id]);
        if ($stmt->fetch()) {
            throw new Exception('NRP sudah terdaftar');
        }
        
        // Update personnel
        $stmt = $pdo->prepare("
            UPDATE personel 
            SET nrp = ?, nama = ?, rank_id = ?, position_id = ?, unit_id = ?, phone = ?, email = ?, is_active = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$nrp, $nama, $rank_id, $position_id, $unit_id, $phone, $email, $is_active, $id]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Personel berhasil diperbarui']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
