<?php
// Toggle jabatan active/inactive

require_once '../config/database.php';

$db = (new Database())->getConnection();

// Check if user is super admin
session_start();
$currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
$userRole = $currentUser['role'] ?? 'user';

if ($userRole != 'super_admin') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

$positionId = $_POST['position_id'] ?? 0;
$isActive = $_POST['is_active'] ?? 0;

if (!$positionId) {
    echo json_encode(['success' => false, 'message' => 'Position ID tidak valid']);
    exit;
}

// Update position status
$stmt = $db->prepare("UPDATE kantor_positions SET is_active = ? WHERE id = ?");
$result = $stmt->execute([$isActive, $positionId]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update status jabatan']);
}
?>
