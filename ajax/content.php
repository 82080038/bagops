<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';
require_once '../services/dynamic_content_service.php';

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
    
    // Initialize Dynamic Content Service
    $dynamicContentService = new DynamicContentService($auth);
    
    // Get dynamic content with multiple fallback strategies
    $result = $dynamicContentService->getDynamicContent($page, $userRole);
    
    // Add additional metadata
    $result['metadata'] = [
        'page' => $page,
        'user_role' => $userRole,
        'user_id' => $userId,
        'timestamp' => date('Y-m-d H:i:s'),
        'content_source' => $result['source'] ?? 'unknown',
        'performance' => [
            'cache_used' => isset($result['from_cache']) && $result['from_cache'],
            'generation_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
        ]
    ];
    
    // Add page info if available
    if (isset($result['page_info'])) {
        $result['metadata']['page_info'] = $result['page_info'];
    }
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log("Dynamic Content Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error loading content: ' . $e->getMessage(),
        'error_details' => [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
}
?>
