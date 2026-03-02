<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get filter parameters
    $search = trim($_POST['search'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $priority = trim($_POST['priority'] ?? '');

    // Build query
    $whereConditions = [];
    $params = [];

    if (!empty($search)) {
        $whereConditions[] = "(s.title LIKE ? OR s.objective LIKE ? OR s.description LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    if (!empty($status)) {
        $whereConditions[] = "s.status = ?";
        $params[] = $status;
    }

    if (!empty($priority)) {
        $whereConditions[] = "s.priority = ?";
        $params[] = $priority;
    }

    // Add user access control (only show own records unless admin)
    if ($currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        $whereConditions[] = "s.created_by = ?";
        $params[] = $currentUser['id'];
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get filtered SPRIN records
    $stmt = $pdo->prepare("
        SELECT s.*, u.name as created_by_name
        FROM sprin s
        LEFT JOIN users u ON s.created_by = u.id
        {$whereClause}
        ORDER BY s.created_at DESC
        LIMIT 100
    ");

    $stmt->execute($params);
    $sprin_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for response
    $formatted_data = array_map(function($sprin) {
        return [
            'id' => $sprin['id'],
            'title' => $sprin['title'],
            'objective' => $sprin['objective'],
            'priority' => $sprin['priority'],
            'priority_text' => match($sprin['priority']) {
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
                'critical' => 'Kritis',
                default => ucfirst($sprin['priority'])
            },
            'status' => $sprin['status'],
            'status_text' => match($sprin['status']) {
                'draft' => 'Draft',
                'review' => 'Review',
                'approved' => 'Disetujui',
                'rejected' => 'Ditolak',
                default => ucfirst($sprin['status'])
            },
            'deadline' => $sprin['deadline'],
            'description' => $sprin['description'],
            'created_by_name' => $sprin['created_by_name'],
            'created_at' => $sprin['created_at'],
            'created_at_formatted' => date('d/m/Y H:i', strtotime($sprin['created_at']))
        ];
    }, $sprin_list);

    echo json_encode([
        'success' => true,
        'sprin' => $formatted_data,
        'total' => count($formatted_data),
        'filters_applied' => [
            'search' => $search,
            'status' => $status,
            'priority' => $priority
        ]
    ]);

} catch (Exception $e) {
    error_log("Filter SPRIN error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memfilter data SPRIN: ' . $e->getMessage()
    ]);
}
?>
