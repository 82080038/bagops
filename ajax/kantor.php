<?php
/**
 * Kantor API Endpoints
 * AJAX handlers for kantor CRUD operations
 */

session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/KantorManager.php';

header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $kantorManager = new KantorManager($db, $auth);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            echo json_encode($kantorManager->createKantor($_POST));
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            echo json_encode($kantorManager->updateKantor($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            echo json_encode($kantorManager->deleteKantor($id));
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $kantor = $kantorManager->getKantorDetail($id);
            if ($kantor) {
                echo json_encode(['success' => true, 'data' => $kantor]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Kantor not found']);
            }
            break;
            
        case 'list':
            $filters = [
                'tipe_kantor_polisi' => $_POST['tipe_kantor_polisi'] ?? null,
                'klasifikasi' => $_POST['klasifikasi'] ?? null,
                'level_kompleksitas' => $_POST['level_kompleksitas'] ?? null,
                'status' => $_POST['status'] ?? null,
                'search' => $_POST['search'] ?? null
            ];
            $kantor = $kantorManager->getKantor($filters);
            echo json_encode(['success' => true, 'data' => $kantor]);
            break;
            
        case 'get_stats':
            echo json_encode(['success' => true, 'data' => $kantorManager->getKantorStats()]);
            break;
            
        case 'toggle_status':
            $id = $_POST['id'] ?? 0;
            echo json_encode($kantorManager->toggleStatus($id));
            break;
            
        case 'export':
            $format = $_POST['format'] ?? 'excel';
            echo json_encode($kantorManager->exportKantor($format));
            break;
            
        case 'download_export':
            $filename = $_GET['filename'] ?? '';
            $filepath = __DIR__ . '/../storage/exports/' . $filename;
            
            if (file_exists($filepath)) {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Content-Length: ' . filesize($filepath));
                header('Cache-Control: private, no-cache, no-store, must-revalidate');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                readfile($filepath);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'File not found']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Kantor API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
