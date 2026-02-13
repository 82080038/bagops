<?php
function db_connection()
{
    static $pdo;
    if ($pdo) return $pdo;
    $config = require __DIR__ . '/../../config/config.php';
    $db = $config['db'];
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']);
    try {
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo 'DB connection error: ' . htmlspecialchars($e->getMessage());
        exit;
    }
    return $pdo;
}
