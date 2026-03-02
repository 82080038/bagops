<?php
/**
 * Reports API Endpoints
 * AJAX handlers for report generation and management
 */

session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/ReportGenerator.php';

header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $reportGenerator = new ReportGenerator($db, $auth);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'generate_operation':
            $operation_id = $_POST['operation_id'] ?? 0;
            $format = $_POST['format'] ?? 'pdf';
            echo json_encode($reportGenerator->generateOperationReport($operation_id, $format));
            break;
            
        case 'generate_personnel':
            $filters = [
                'pangkat' => $_POST['pangkat'] ?? null,
                'jabatan' => $_POST['jabatan'] ?? null,
                'unit' => $_POST['unit'] ?? null
            ];
            $format = $_POST['format'] ?? 'pdf';
            echo json_encode($reportGenerator->generatePersonnelReport($filters, $format));
            break;
            
        case 'generate_monthly':
            $month = $_POST['month'] ?? date('n');
            $year = $_POST['year'] ?? date('Y');
            $format = $_POST['format'] ?? 'pdf';
            echo json_encode($reportGenerator->generateMonthlyReport($month, $year, $format));
            break;
            
        case 'list_reports':
            echo json_encode(['success' => true, 'data' => $reportGenerator->getAvailableReports()]);
            break;
            
        case 'delete_report':
            $filename = $_POST['filename'] ?? '';
            echo json_encode($reportGenerator->deleteReport($filename));
            break;
            
        case 'download_report':
            $filename = $_GET['filename'] ?? '';
            $reports = $reportGenerator->getAvailableReports();
            
            $report = null;
            foreach ($reports as $r) {
                if ($r['filename'] === $filename) {
                    $report = $r;
                    break;
                }
            }
            
            if ($report && file_exists($report['filepath'])) {
                header('Content-Type: text/html');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . $report['size']);
                header('Cache-Control: private, no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                readfile($report['filepath']);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Report not found']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Reports API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
