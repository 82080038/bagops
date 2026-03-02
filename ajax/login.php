<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

try {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Username dan password harus diisi']);
        exit();
    }
    
    $auth = new Auth((new Database())->getConnection());
    
    if ($auth->login($username, $password)) {
        $user = $auth->getCurrentUser();
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'full_name' => $user['full_name']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
