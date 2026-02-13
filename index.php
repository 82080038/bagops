<?php
/**
 * Front controller for BAGOPS Application
 * 
 * This file should be placed in the root directory of the application
 * and will automatically redirect all requests to the public/ directory.
 */

// Define the path to the public directory
$publicDir = __DIR__ . '/public';

// Get the request URI and remove query string
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = ltrim($request, '/');

// Remove the base directory from the request if needed
$baseDir = basename(dirname(__DIR__)) . '/' . basename(__DIR__);
if (strpos($request, $baseDir) === 0) {
    $request = substr($request, strlen($baseDir));
}
$request = ltrim($request, '/');

// Build the target URL
$target = $publicDir . '/' . $request;

// If the request is for a file that exists, serve it directly
if ($request && file_exists($target) && is_file($target)) {
    return false;
}

// Otherwise, include the main index.php from the public directory
require $publicDir . '/index.php';
