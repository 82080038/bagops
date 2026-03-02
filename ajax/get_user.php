<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication and admin access
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

// Only admins can manage users
$currentUser = $auth->getCurrentUser();
if ($currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_POST['id'] ?? null;
$edit = isset($_POST['edit']) && $_POST['edit'] === 'true';

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();

    // Get user data
    $stmt = $pdo->prepare("
        SELECT id, username, email, role, name, phone, is_active, created_at, updated_at
        FROM users
        WHERE id = ?
    ");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit;
    }

    if ($edit) {
        // Return data for editing (exclude password)
        echo json_encode([
            'success' => true,
            'edit_data' => $user
        ]);
    } else {
        // Return formatted content for viewing
        ob_start();
        ?>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><?php echo htmlspecialchars($user['name'] ?: $user['username']); ?></h5>
                <span class="badge bg-<?php
                    switch ($user['role']) {
                        case 'super_admin': echo 'danger';
                            break;
                        case 'admin': echo 'warning';
                            break;
                        case 'kabag_ops': echo 'info';
                            break;
                        case 'kaur_ops': echo 'primary';
                            break;
                        case 'user': echo 'secondary';
                            break;
                        default: echo 'secondary';
                    }
                ?>"><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Username:</strong><br>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong><br>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Phone:</strong><br>
                            <span><?php echo htmlspecialchars($user['phone'] ?: '-'); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Role:</strong><br>
                            <span><?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'danger'; ?>">
                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Created:</strong><br>
                            <span><?php echo date('d F Y H:i', strtotime($user['created_at'])); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Last Updated:</strong><br>
                            <span><?php echo date('d F Y H:i', strtotime($user['updated_at'])); ?></span>
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
    error_log("Get User error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail user'
    ]);
}
?>
