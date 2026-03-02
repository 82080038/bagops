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

// Check if user can access operations module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul operasi']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get filter parameters
        $search = trim($_POST['search'] ?? '');
        $status = $_POST['status'] ?? '';
        $date = $_POST['date'] ?? '';
        
        // Build query
        $query = "
            SELECT o.*, u.nama as commander_name,
                   DATE_FORMAT(o.start_date, '%d/%m/%Y') as start_date_formatted,
                   DATE_FORMAT(o.start_date, '%d/%m/%Y %H:%i') as start_datetime,
                   CASE 
                       WHEN o.end_date IS NOT NULL THEN DATE_FORMAT(o.end_date, '%d/%m/%Y %H:%i')
                       ELSE '-'
                   END as end_date_formatted
            FROM operations o 
            LEFT JOIN users u ON o.commander_id = u.id 
            WHERE 1=1
        ";
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $query .= " AND (o.title LIKE ? OR o.location LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Add status filter
        if (!empty($status)) {
            $query .= " AND o.status = ?";
            $params[] = $status;
        }
        
        // Add date filter
        if (!empty($date)) {
            $query .= " AND o.start_date = ?";
            $params[] = $date;
        }
        
        $query .= " ORDER BY o.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'operations' => $operations]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
