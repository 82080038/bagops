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

// Check if user can access RENOPS module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul RENOPS']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get filter parameters
        $search = trim($_POST['search'] ?? '');
        $event_type = $_POST['event_type'] ?? '';
        $date = $_POST['date'] ?? '';
        
        // Build query
        $query = "
            SELECT r.*, e.title as event_title, e.type as event_type, e.start_at, e.location,
                   DATE_FORMAT(e.start_at, '%d/%m/%Y %H:%i') as start_date_formatted
            FROM renops r 
            LEFT JOIN events e ON r.event_id = e.id 
            WHERE 1=1
        ";
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $query .= " AND (r.doc_no LIKE ? OR e.title LIKE ? OR e.location LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Add event type filter
        if (!empty($event_type)) {
            $query .= " AND e.type = ?";
            $params[] = $event_type;
        }
        
        // Add date filter
        if (!empty($date)) {
            $query .= " AND DATE(e.start_at) = ?";
            $params[] = $date;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $renops = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'renops' => $renops]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
