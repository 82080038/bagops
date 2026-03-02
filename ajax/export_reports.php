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

// Check if user can access reports module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul laporan']);
    exit();
}

try {
    $pdo = (new Database())->getConnection();
    
    // Get all reports
    $stmt = $pdo->query("
        SELECT r.*, u.nama as created_by_name 
        FROM reports r 
        LEFT JOIN users u ON r.created_by = u.id 
        ORDER BY r.created_at DESC
    ");
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Generate CSV
    $filename = 'reports_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Output CSV header
    echo "ID,Judul,Tipe,Periode Mulai,Periode Selesai,Status,Dibuat Oleh,Tanggal Dibuat,Tanggal Diperbarui\n";
    
    // Output data
    foreach ($reports as $report) {
        echo $report['id'] . ',';
        echo '"' . str_replace('"', '""', $report['title']) . '",';
        echo $report['type'] . ',';
        echo $report['period_start'] . ',';
        echo $report['period_end'] . ',';
        echo $report['status'] . ',';
        echo '"' . str_replace('"', '""', $report['created_by_name']) . '",';
        echo $report['created_at'] . ',';
        echo $report['updated_at'] . "\n";
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
