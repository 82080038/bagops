<?php
// Audit Logging System for BAGOPS
class AuditLogger {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Log access attempts
     */
    public function logAccess($userId, $module, $accessGranted) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO access_log (user_id, module, access_granted, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $ipAddress = $this->getClientIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            return $stmt->execute([$userId, $module, $accessGranted, $ipAddress, $userAgent]);
        } catch (Exception $e) {
            error_log("Audit log error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log user actions
     */
    public function logAction($userId, $action, $details = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO access_log (user_id, module, access_granted, ip_address, user_agent) 
                VALUES (?, ?, 1, ?, ?)
            ");
            
            $ipAddress = $this->getClientIP();
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            return $stmt->execute([$userId, $action, $ipAddress, $userAgent]);
        } catch (Exception $e) {
            error_log("Action log error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Get access logs for a user
     */
    public function getUserAccessLogs($userId, $limit = 100) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM access_log 
                WHERE user_id = ? 
                ORDER BY timestamp DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get logs error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get failed access attempts
     */
    public function getFailedAccessAttempts($limit = 50) {
        try {
            $stmt = $this->db->prepare("
                SELECT al.*, u.name, u.username 
                FROM access_log al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE al.access_granted = 0 
                ORDER BY al.timestamp DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get failed attempts error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get access statistics
     */
    public function getAccessStats($startDate = null, $endDate = null) {
        try {
            $sql = "
                SELECT 
                    DATE(timestamp) as date,
                    COUNT(*) as total_access,
                    SUM(access_granted) as granted_access,
                    COUNT(*) - SUM(access_granted) as denied_access
                FROM access_log
            ";
            
            $params = [];
            
            if ($startDate && $endDate) {
                $sql .= " WHERE timestamp BETWEEN ? AND ?";
                $params = [$startDate, $endDate];
            }
            
            $sql .= " GROUP BY DATE(timestamp) ORDER BY date DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get stats error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Clean old logs (keep last 90 days)
     */
    public function cleanOldLogs() {
        try {
            $stmt = $this->db->prepare("DELETE FROM access_log WHERE timestamp < DATE_SUB(NOW(), INTERVAL 90 DAY)");
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Clean logs error: " . $e->getMessage());
            return false;
        }
    }
}
?>
