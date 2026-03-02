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

    // Get form data
    $title = trim($_POST['title'] ?? '');
    $objective = trim($_POST['objective'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $deadline = $_POST['deadline'] ?? null;
    $description = trim($_POST['description'] ?? '');

    // Validate required fields
    if (empty($title) || empty($objective)) {
        echo json_encode(['success' => false, 'message' => 'Judul dan tujuan intelijen harus diisi']);
        exit;
    }

    // Validate priority
    $valid_priorities = ['low', 'medium', 'high', 'critical'];
    if (!in_array($priority, $valid_priorities)) {
        $priority = 'medium';
    }

    // Check if sprin table exists, if not create it
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'sprin'");
    if ($tableCheck->rowCount() == 0) {
        // Create sprin table
        $pdo->exec("
            CREATE TABLE sprin (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                objective TEXT NOT NULL,
                priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                deadline DATETIME,
                description TEXT,
                status ENUM('draft', 'review', 'approved', 'rejected') DEFAULT 'draft',
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
    }

    // Insert SPRIN record
    $stmt = $pdo->prepare("
        INSERT INTO sprin (
            title, objective, priority, deadline, description, status, created_by, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, 'draft', ?, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $title,
        $objective,
        $priority,
        $deadline ?: null,
        $description ?: null,
        $currentUser['id']
    ]);

    $sprinId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'SPRIN berhasil dibuat',
        'sprin_id' => $sprinId
    ]);

} catch (Exception $e) {
    error_log("Save SPRIN error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan SPRIN: ' . $e->getMessage()
    ]);
}
?>
