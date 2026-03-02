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
        
        // Get form data
        $event_id = $_POST['event_id'] ?? '';
        $doc_no = trim($_POST['doc_no'] ?? '');
        $command_basis = trim($_POST['command_basis'] ?? '');
        $intel_summary = trim($_POST['intel_summary'] ?? '');
        $objectives = trim($_POST['objectives'] ?? '');
        $forces = trim($_POST['forces'] ?? '');
        $comms_plan = trim($_POST['comms_plan'] ?? '');
        $logistics_plan = trim($_POST['logistics_plan'] ?? '');
        $contingency_plan = trim($_POST['contingency_plan'] ?? '');
        $coordination = trim($_POST['coordination'] ?? '');
        
        // Validate required fields
        if (empty($event_id) || empty($command_basis) || empty($intel_summary) || empty($objectives) || empty($forces)) {
            throw new Exception('Field wajib harus diisi');
        }
        
        // Check if event exists
        $stmt = $pdo->prepare("SELECT id FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        if (!$stmt->fetch()) {
            throw new Exception('Event tidak ditemukan');
        }
        
        // Insert new RENOPS
        $stmt = $pdo->prepare("
            INSERT INTO renops (event_id, doc_no, command_basis, intel_summary, objectives, forces, comms_plan, contingency_plan, logistics_plan, coordination, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        
        $stmt->execute([$event_id, $doc_no, $command_basis, $intel_summary, $objectives, $forces, $comms_plan, $contingency_plan, $logistics_plan, $coordination]);
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'RENOPS berhasil dibuat']);
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
