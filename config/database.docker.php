<?php

// Docker database configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load environment variables
function getEnvVar($key, $default = null) {
    $value = getenv($key);
    return $value !== false ? $value : $default;
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = getEnvVar('DB_HOST', 'mysql');
        $this->db_name = getEnvVar('DB_NAME', 'bagops_db');
        $this->username = getEnvVar('DB_USER', 'root');
        $this->password = getEnvVar('DB_PASSWORD', 'rootpassword');
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . getEnvVar('DB_PORT', '3306') . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES, false,
            ]);
        } catch(PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }

        return $this->conn;
    }

    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1");
            return true;
        } catch(Exception $e) {
            error_log("Database test failed: " . $e->getMessage());
            return false;
        }
    }
}
?>
