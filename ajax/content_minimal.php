<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

try {
    $auth = new Auth((new Database())->getConnection());
    $user = $auth->getCurrentUser();
    $userRole = $user['role'] ?? 'user';
    $userId = $user['id'] ?? 0;
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    }
    
    $page = $_POST['page'] ?? 'dashboard';
    $success = false;
    $message = '';
    $content = '';
    
    switch ($page) {
        case 'dashboard':
            $content = '<div class="container-fluid"><h2>Dashboard</h2><p>Welcome to BAGOPS Dashboard</p></div>';
            $success = true;
            break;
        case 'master':
            $content = '<div class="container-fluid"><h2>Data Master</h2><p>Master data management</p></div>';
            $success = true;
            break;
        case 'personel':
            $content = '<div class="container-fluid"><h2>Personel</h2><p>Personel management</p></div>';
            $success = true;
            break;
        case 'operations':
            $content = '<div class="container-fluid"><h2>Operations</h2><p>Operations management</p></div>';
            $success = true;
            break;
        case 'reports':
            $content = '<div class="container-fluid"><h2>Reports</h2><p>Reports management</p></div>';
            $success = true;
            break;
        case 'settings':
            if ($userRole === 'super_admin' || $userRole === 'admin') {
                $content = '<div class="container-fluid"><h2>Settings</h2><p>System settings</p></div>';
                $success = true;
            } else {
                $message = 'Access denied';
            }
            break;
        default:
            $content = '<div class="container-fluid"><h2>' . htmlspecialchars($page) . '</h2><p>Page content</p></div>';
            $success = true;
            break;
    }
    
    echo json_encode(['success' => $success, 'content' => $content, 'message' => $message]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
