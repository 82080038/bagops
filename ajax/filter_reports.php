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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get filter parameters
        $search = trim($_POST['search'] ?? '');
        $type = $_POST['type'] ?? '';
        $status = $_POST['status'] ?? '';
        $date = $_POST['date'] ?? '';
        
        // Build query
        $query = "
            SELECT r.*, u.nama as created_by_name,
                   DATE_FORMAT(r.period_start, '%d/%m/%Y') as period_start_formatted,
                   DATE_FORMAT(r.created_at, '%d/%m/%Y %H:%i') as created_at_formatted
            FROM reports r 
            LEFT JOIN users u ON r.created_by = u.id 
            WHERE 1=1
        ";
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $query .= " AND (r.title LIKE ? OR r.description LIKE ? OR r.content LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Add type filter
        if (!empty($type)) {
            $query .= " AND r.type = ?";
            $params[] = $type;
        }
        
        // Add status filter
        if (!empty($status)) {
            $query .= " AND r.status = ?";
            $params[] = $status;
        }
        
        // Add date filter
        if (!empty($date)) {
            $query .= " AND DATE(r.period_start) = ?";
            $params[] = $date;
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'reports' => $reports]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
