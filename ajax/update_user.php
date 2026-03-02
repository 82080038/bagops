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

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid User ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();

    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'user';
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Username dan email harus diisi']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
        exit;
    }

    // Validate role
    $valid_roles = ['super_admin', 'admin', 'kabag_ops', 'kaur_ops', 'user'];
    if (!in_array($role, $valid_roles)) {
        echo json_encode(['success' => false, 'message' => 'Role tidak valid']);
        exit;
    }

    // Check if username already exists (exclude current user)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$username, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Username sudah digunakan']);
        exit;
    }

    // Check if email already exists (exclude current user)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email sudah terdaftar']);
        exit;
    }

    // Prepare update data
    $updateData = [
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'name' => $name ?: null,
        'phone' => $phone ?: null,
        'is_active' => $is_active,
        'id' => $id
    ];

    // Handle password update if provided
    if (!empty($password)) {
        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password minimal 6 karakter']);
            exit;
        }

        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Konfirmasi password tidak cocok']);
            exit;
        }

        $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    // Build update query
    $setParts = [];
    $params = [];

    foreach ($updateData as $field => $value) {
        if ($field !== 'id') {
            $setParts[] = "{$field} = ?";
            $params[] = $value;
        }
    }

    $params[] = $id; // Add ID for WHERE clause

    $setClause = implode(', ', $setParts);

    // Update user record
    $stmt = $pdo->prepare("
        UPDATE users SET {$setClause}, updated_at = NOW()
        WHERE id = ?
    ");

    $stmt->execute($params);

    echo json_encode([
        'success' => true,
        'message' => 'User berhasil diperbarui'
    ]);

} catch (Exception $e) {
    error_log("Update User error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()
    ]);
}
?>
