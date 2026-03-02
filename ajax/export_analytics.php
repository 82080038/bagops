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

// Check if user can access analytics module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul analytics']);
    exit();
}

try {
    $pdo = (new Database())->getConnection();
    
    // Get analytics data
    $analytics_data = [];
    
    // Personnel statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personel");
    $analytics_data['personnel_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM personel WHERE is_active = 1");
    $analytics_data['personnel_active'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
    
    // Operations statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM operations");
    $analytics_data['operations_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM operations WHERE status = 'completed'");
    $analytics_data['operations_completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
    
    // RENOPS statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM renops");
    $analytics_data['renops_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Reports statistics
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reports");
    $analytics_data['reports_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM reports WHERE status = 'completed'");
    $analytics_data['reports_completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
    
    // Generate CSV
    $filename = 'analytics_export_' . date('Y-m-d_H-i-s') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Output CSV header
    echo "Kategori,Total,Aktif/Selesai,Percentage\n";
    
    // Output data
    echo "Personel," . $analytics_data['personnel_total'] . "," . $analytics_data['personnel_active'] . ",";
    echo ($analytics_data['personnel_total'] > 0 ? number_format(($analytics_data['personnel_active'] / $analytics_data['personnel_total']) * 100, 2) : 0) . "%\n";
    
    echo "Operations," . $analytics_data['operations_total'] . "," . $analytics_data['operations_completed'] . ",";
    echo ($analytics_data['operations_total'] > 0 ? number_format(($analytics_data['operations_completed'] / $analytics_data['operations_total']) * 100, 2) : 0) . "%\n";
    
    echo "RENOPS," . $analytics_data['renops_total'] . ",0,0%\n";
    
    echo "Reports," . $analytics_data['reports_total'] . "," . $analytics_data['reports_completed'] . ",";
    echo ($analytics_data['reports_total'] > 0 ? number_format(($analytics_data['reports_completed'] / $analytics_data['reports_total']) * 100, 2) : 0) . "%\n";
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
