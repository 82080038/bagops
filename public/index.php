<?php
// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Set error reporting based on config
error_reporting($config['error_reporting']);
ini_set('display_errors', $config['display_errors']);
ini_set('log_errors', $config['log_errors']);
ini_set('error_log', $config['error_log']);

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Display errors for development
if ($config['app']['debug']) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require __DIR__ . '/../src/router.php';
