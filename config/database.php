<?php

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
class Database {
    private $host = 'localhost';
    private $db_name = 'bagops_db';
    private $username = 'root';
    private $password = 'root';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Try with socket connection first
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES, false,
            ]);
        } catch(PDOException $e) {
            // If socket connection fails, try with TCP
            try {
                $dsn = "mysql:host=127.0.0.1;port=3306;dbname=" . $this->db_name . ";charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES, false,
                ]);
            } catch(PDOException $e2) {
                error_log("Database connection failed: " . $e2->getMessage());
                throw new Exception("Database connection failed: " . $e2->getMessage());
            }
        }

        return $this->conn;
    }

    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1");
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
?>
