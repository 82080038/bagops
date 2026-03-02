<?php

// Docker environment configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load environment variables
function getEnvVar($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

return [
    'db' => [
        'host' => getEnvVar('DB_HOST', 'mysql'),
        'port' => getEnvVar('DB_PORT', '3306'),
        'name' => getEnvVar('DB_NAME', 'bagops_db'),
        'user' => getEnvVar('DB_USER', 'root'),
        'pass' => getEnvVar('DB_PASSWORD', 'rootpassword'),
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => getEnvVar('APP_BASE_URL', 'http://localhost:8080'),
        'timezone' => getEnvVar('TIMEZONE', 'Asia/Jakarta'),
        'debug' => getEnvVar('APP_DEBUG', 'true') === 'true',
        'env' => getEnvVar('APP_ENV', 'development'),
    ],
    'session' => [
        'name' => 'BAGOPS_SESSION',
        'lifetime' => (int)getEnvVar('SESSION_LIFETIME', '7200'),
        'path' => getEnvVar('SESSION_PATH', '/'),
        'domain' => getEnvVar('SESSION_DOMAIN', ''),
        'secure' => false,
        'httponly' => true,
    ],
    'error_reporting' => E_ALL,
    'display_errors' => 1,
    'log_errors' => 1,
    'error_log' => __DIR__ . '/../storage/logs/error.log',
];
