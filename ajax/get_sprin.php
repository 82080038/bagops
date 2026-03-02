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

$id = $_POST['id'] ?? null;
$edit = isset($_POST['edit']) && $_POST['edit'] === 'true';

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid SPRIN ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get SPRIN data
    $stmt = $pdo->prepare("
        SELECT s.*, u.name as created_by_name
        FROM sprin s
        LEFT JOIN users u ON s.created_by = u.id
        WHERE s.id = ?
    ");
    $stmt->execute([$id]);
    $sprin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sprin) {
        echo json_encode(['success' => false, 'message' => 'SPRIN not found']);
        exit;
    }

    // Check access permissions
    if ($sprin['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    if ($edit) {
        // Return data for editing
        echo json_encode([
            'success' => true,
            'edit_data' => $sprin
        ]);
    } else {
        // Return formatted content for viewing
        ob_start();
        ?>
        <div class="card">
            <div class="card-header">
                <h5><?php echo htmlspecialchars($sprin['title']); ?></h5>
                <div class="d-flex gap-2">
                    <span class="badge bg-<?php
                        switch ($sprin['priority']) {
                            case 'low': echo 'secondary';
                                break;
                            case 'medium': echo 'info';
                                break;
                            case 'high': echo 'warning';
                                break;
                            case 'critical': echo 'danger';
                                break;
                            default: echo 'secondary';
                        }
                    ?>"><?php echo ucfirst($sprin['priority']); ?> Priority</span>
                    <span class="badge bg-<?php
                        switch ($sprin['status']) {
                            case 'draft': echo 'secondary';
                                break;
                            case 'review': echo 'warning';
                                break;
                            case 'approved': echo 'success';
                                break;
                            case 'rejected': echo 'danger';
                                break;
                            default: echo 'secondary';
                        }
                    ?>"><?php echo ucfirst($sprin['status']); ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <?php if (!empty($sprin['objective'])): ?>
                        <div class="mb-3">
                            <h6>Tujuan Intelijen</h6>
                            <p><?php echo nl2br(htmlspecialchars($sprin['objective'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($sprin['description'])): ?>
                        <div class="mb-3">
                            <h6>Deskripsi Detail</h6>
                            <p><?php echo nl2br(htmlspecialchars($sprin['description'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <div class="border-start border-primary border-3 ps-3">
                            <div class="mb-2">
                                <strong>Dibuat Oleh:</strong><br>
                                <?php echo htmlspecialchars($sprin['created_by_name'] ?: '-'); ?>
                            </div>
                            <div class="mb-2">
                                <strong>Tanggal Dibuat:</strong><br>
                                <?php echo date('d F Y H:i', strtotime($sprin['created_at'])); ?>
                            </div>
                            <?php if (!empty($sprin['deadline'])): ?>
                            <div class="mb-2">
                                <strong>Deadline:</strong><br>
                                <?php echo date('d F Y H:i', strtotime($sprin['deadline'])); ?>
                            </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <strong>Status:</strong><br>
                                <span class="badge bg-<?php
                                    switch ($sprin['status']) {
                                        case 'draft': echo 'secondary';
                                            break;
                                        case 'review': echo 'warning';
                                            break;
                                        case 'approved': echo 'success';
                                            break;
                                        case 'rejected': echo 'danger';
                                            break;
                                        default: echo 'secondary';
                                    }
                                ?>"><?php echo ucfirst($sprin['status']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php

        $content = ob_get_clean();

        echo json_encode([
            'success' => true,
            'content' => $content
        ]);
    }

} catch (Exception $e) {
    error_log("Get SPRIN error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail SPRIN'
    ]);
}
?>
