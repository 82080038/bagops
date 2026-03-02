<?php
/**
 * Assignments API Endpoints
 * AJAX handlers for assignment CRUD operations
 */

session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../classes/AssignmentManager.php';

header('Content-Type: application/json');

try {
    $db = (new Database())->getConnection();
    $auth = new Auth($db);
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit();
    }
    
    $assignmentManager = new AssignmentManager($db, $auth);
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'create':
            echo json_encode($assignmentManager->createAssignment($_POST));
            break;
            
        case 'update':
            $id = $_POST['id'] ?? 0;
            echo json_encode($assignmentManager->updateAssignment($id, $_POST));
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            echo json_encode($assignmentManager->deleteAssignment($id));
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $assignment = $assignmentManager->getAssignment($id);
            if ($assignment) {
                echo json_encode(['success' => true, 'data' => $assignment]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Assignment not found']);
            }
            break;
            
        case 'list':
            $filters = [
                'status_assignment' => $_POST['status_assignment'] ?? null,
                'personel_id' => $_POST['personel_id'] ?? null,
                'operation_id' => $_POST['operation_id'] ?? null,
                'prioritas' => $_POST['prioritas'] ?? null
            ];
            $assignments = $assignmentManager->getAssignments($filters);
            echo json_encode(['success' => true, 'data' => $assignments]);
            break;
            
        case 'update_status':
            $id = $_POST['id'] ?? 0;
            $status = $_POST['status_assignment'] ?? '';
            $catatan = $_POST['catatan'] ?? '';
            echo json_encode($assignmentManager->updateAssignmentStatus($id, $status, $catatan));
            break;
            
        case 'get_stats':
            echo json_encode(['success' => true, 'data' => $assignmentManager->getAssignmentStats()]);
            break;
            
        case 'get_workload':
            $personel_id = $_GET['personel_id'] ?? null;
            echo json_encode(['success' => true, 'data' => $assignmentManager->getPersonnelWorkload($personel_id)]);
            break;
            
        case 'get_overdue':
            echo json_encode(['success' => true, 'data' => $assignmentManager->getOverdueAssignments()]);
            break;
            
        case 'get_upcoming':
            $days = $_GET['days'] ?? 7;
            echo json_encode(['success' => true, 'data' => $assignmentManager->getUpcomingAssignments($days)]);
            break;
            
        case 'create_template':
            echo json_encode($assignmentManager->createTemplate($_POST));
            break;
            
        case 'get_templates':
            echo json_encode(['success' => true, 'data' => $assignmentManager->getTemplates()]);
            break;
            
        case 'create_from_template':
            $template_id = $_POST['template_id'] ?? 0;
            $personel_id = $_POST['personel_id'] ?? 0;
            $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
            $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
            echo json_encode($assignmentManager->createFromTemplate($template_id, $personel_id, $tanggal_mulai, $tanggal_selesai));
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Assignments API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
