<?php
// Assign personel to position

require_once '../config/database.php';

$db = (new Database())->getConnection();

$positionId = $_POST['position_id'] ?? 0;
$personelId = $_POST['personel_id'] ?? null;

if (!$positionId) {
    echo json_encode(['success' => false, 'message' => 'Position ID tidak valid']);
    exit;
}

// Update position with personel
$stmt = $db->prepare("UPDATE kantor_positions SET personel_id = ? WHERE id = ?");
$result = $stmt->execute([$personelId ?: null, $positionId]);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal update position']);
}
?>
