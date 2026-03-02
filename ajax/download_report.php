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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get report ID
        $id = $_GET['id'] ?? '';
        
        if (empty($id)) {
            throw new Exception('ID laporan tidak valid');
        }
        
        // Get report data
        $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            throw new Exception('Laporan tidak ditemukan');
        }
        
        // Generate PDF or text file
        $filename = 'laporan_' . $report['type'] . '_' . date('Y-m-d') . '.txt';
        
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Output report content
        echo "LAPORAN " . strtoupper($report['type']) . "\n";
        echo "========================================\n\n";
        echo "Judul: " . $report['title'] . "\n";
        echo "Tipe: " . $report['type'] . "\n";
        echo "Periode: " . date('d/m/Y', strtotime($report['period_start']));
        if (!empty($report['period_end'])) {
            echo " - " . date('d/m/Y', strtotime($report['period_end']));
        }
        echo "\n";
        echo "Dibuat: " . date('d/m/Y H:i', strtotime($report['created_at'])) . "\n";
        echo "Diperbarui: " . date('d/m/Y H:i', strtotime($report['updated_at'])) . "\n\n";
        
        echo "----------------------------------------\n";
        echo "DESKRIPSI\n";
        echo "----------------------------------------\n";
        echo $report['description'] . "\n\n";
        
        echo "----------------------------------------\n";
        echo "ISI LAPORAN\n";
        echo "----------------------------------------\n";
        echo $report['content'] . "\n\n";
        
        echo "========================================\n";
        echo "End of Report\n";
        echo "========================================\n";
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
