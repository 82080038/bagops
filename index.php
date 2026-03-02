<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'classes/Auth.php';

// Debug: Log untuk troubleshooting
error_log("BAGOPS index.php accessed - " . date('Y-m-d H:i:s'));
error_log("REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'not set'));
error_log("PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'not set'));

// Check if user is logged in
$auth = new Auth((new Database())->getConnection());

// Debug: Log authentication status
error_log("Auth status: " . ($auth->isLoggedIn() ? 'logged_in' : 'not_logged_in'));

// Redirect based on authentication status
if (!$auth->isLoggedIn()) {
    error_log("Redirecting to login.php");
    header('Location: login.php');
    exit();
}

// If logged in, redirect to root page system
error_log("Redirecting to simple_root_system.php");
header('Location: simple_root_system.php?page=dashboard');
exit();
?>
