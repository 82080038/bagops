<?php
/**
 * Operations API Endpoints
 * AJAX handlers for operations CRUD operations
 */

session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/OperationsManager.php';

header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $operationsManager = new OperationsManager($db, $auth);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            echo json_encode($operationsManager->createOperation($_POST));
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            echo json_encode($operationsManager->updateOperation($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            echo json_encode($operationsManager->deleteOperation($id));
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $operation = $operationsManager->getOperation($id);
            if ($operation) {
                echo json_encode(['success' => true, 'data' => $operation]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Operation not found']);
            }
            break;
            
        case 'list':
            $filters = [
                'status' => $_POST['status'] ?? null,
                'jenis_operasi' => $_POST['jenis_operasi'] ?? null,
                'tanggal_mulai' => $_POST['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $_POST['tanggal_selesai'] ?? null
            ];
            $operations = $operationsManager->getOperations($filters);
            echo json_encode(['success' => true, 'data' => $operations]);
            break;
            
        case 'assign_personnel':
            $operation_id = $_POST['operation_id'] ?? 0;
            $personnel_data = [
                'personel_id' => $_POST['personel_id'],
                'role_assignment' => $_POST['role_assignment'],
                'tugas_khusus' => $_POST['tugas_khusus'] ?? '',
                'tanggal_assign' => $_POST['tanggal_assign'] ?? date('Y-m-d'),
                'jam_mulai' => $_POST['jam_mulai'] ?? '08:00',
                'jam_selesai' => $_POST['jam_selesai'] ?? '16:00'
            ];
            echo json_encode($operationsManager->assignPersonnel($operation_id, $personnel_data));
            break;
            
        case 'get_stats':
            echo json_encode(['success' => true, 'data' => $operationsManager->getOperationStats()]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Operations API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
