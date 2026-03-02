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

// Check if user can access personnel module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki akses ke modul personel']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get filter parameters
        $search = trim($_POST['search'] ?? '');
        $unit = $_POST['unit'] ?? '';
        $status = $_POST['status'] ?? '';
        $kantor = $_POST['kantor'] ?? '';
        $kantor_jenis = $_POST['kantor_jenis'] ?? '';
        
        // Build query
        $query = "
            SELECT p.*, r.nama as rank_nama, pos.nama as position_nama, u.nama as unit_nama, k.nama as kantor_nama
            FROM personel p 
            LEFT JOIN ranks r ON p.rank_id = r.id 
            LEFT JOIN positions pos ON p.position_id = pos.id 
            LEFT JOIN units u ON p.unit_id = u.id 
            LEFT JOIN kantor k ON p.kantor_id = k.id
            WHERE 1=1
        ";
        $params = [];
        
        // Add search filter
        if (!empty($search)) {
            $query .= " AND (p.nrp LIKE ? OR p.nama LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Add unit filter
        if (!empty($unit)) {
            $query .= " AND p.unit_id = ?";
            $params[] = $unit;
        }
        
        // Add status filter
        if ($status !== '') {
            $query .= " AND p.is_active = ?";
            $params[] = $status;
        }
        
        // Add kantor filter
        if (!empty($kantor)) {
            $query .= " AND p.kantor_id = (SELECT id FROM kantor WHERE nama = ?)";
            $params[] = $kantor;
        }
        
        $query .= " ORDER BY p.nama";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $personnel = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'personnel' => $personnel]);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
