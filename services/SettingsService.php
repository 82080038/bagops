<?php
/**
 * SettingsService - Business Logic Layer for Settings Management
 * BAGOPS POLRES SAMOSIR - Service Layer Implementation
 */

class SettingsService {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->auth = new Auth($this->db);
    }
    
    /**
     * Get all settings
     */
    public function getAllSettings() {
        try {
            $sql = "SELECT * FROM settings ORDER BY setting_key";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $settings = [];
            while ($row = $stmt->fetch()) {
                $settings[$row['setting_key']] = [
                    'value' => $row['setting_value'],
                    'type' => $row['setting_type'],
                    'description' => $row['description'],
                    'updated_at' => $row['updated_at']
                ];
            }
            
            return $settings;
            
        } catch (Exception $e) {
            throw new Exception("Error getting settings: " . $e->getMessage());
        }
    }
    
    /**
     * Get setting by key
     */
    public function getSetting($key) {
        try {
            $sql = "SELECT setting_value, setting_type FROM settings WHERE setting_key = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            
            if (!$result) {
                return $this->getDefaultSetting($key);
            }
            
            // Convert type
            return $this->convertSettingType($result['setting_value'], $result['setting_type']);
            
        } catch (Exception $e) {
            throw new Exception("Error getting setting: " . $e->getMessage());
        }
    }
    
    /**
     * Update setting
     */
    public function updateSetting($key, $value, $type = 'string') {
        try {
            // Validate setting
            $this->validateSetting($key, $value, $type);
            
            // Check if setting exists
            $checkSql = "SELECT id FROM settings WHERE setting_key = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$key]);
            $exists = $checkStmt->fetch();
            
            if ($exists) {
                // Update existing
                $sql = "UPDATE settings SET setting_value = ?, setting_type = ?, updated_at = NOW() WHERE setting_key = ?";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([$value, $type, $key]);
            } else {
                // Insert new
                $sql = "INSERT INTO settings (setting_key, setting_value, setting_type, description, updated_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    $key,
                    $value,
                    $type,
                    $this->getSettingDescription($key)
                ]);
            }
            
            if (!$result) {
                throw new Exception("Failed to update setting");
            }
            
            // Log activity
            $this->logActivity('SETTING_UPDATED', "Updated setting: {$key}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error updating setting: " . $e->getMessage());
        }
    }
    
    /**
     * Update multiple settings
     */
    public function updateMultipleSettings($settings) {
        try {
            $this->db->beginTransaction();
            
            foreach ($settings as $key => $data) {
                $value = $data['value'];
                $type = $data['type'] ?? 'string';
                
                $this->updateSetting($key, $value, $type);
            }
            
            $this->db->commit();
            
            // Log activity
            $this->logActivity('SETTINGS_BATCH_UPDATED', "Updated " . count($settings) . " settings");
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error updating settings: " . $e->getMessage());
        }
    }
    
    /**
     * Get system statistics for settings page
     */
    public function getSystemStats() {
        try {
            $stats = [];
            
            // Database stats
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM users");
            $stmt->execute();
            $stats['users'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
            $stmt->execute();
            $stats['active_personel'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM operations WHERE status = 'active'");
            $stmt->execute();
            $stats['active_operations'] = $stmt->fetch()['total'];
            
            // Database size (approximate)
            $stmt = $this->db->prepare("SELECT table_name, ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb' FROM information_schema.tables WHERE table_schema = DATABASE() ORDER BY size_mb DESC LIMIT 5");
            $stmt->execute();
            $stats['database_size'] = $stmt->fetchAll();
            
            // Recent activity
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM audit_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stmt->execute();
            $stats['recent_activity'] = $stmt->fetch()['total'];
            
            return $stats;
            
        } catch (Exception $e) {
            throw new Exception("Error getting system stats: " . $e->getMessage());
        }
    }
    
    /**
     * Reset settings to defaults
     */
    public function resetToDefaults() {
        try {
            $this->db->beginTransaction();
            
            $defaults = $this->getDefaultSettings();
            
            foreach ($defaults as $key => $data) {
                $this->updateSetting($key, $data['value'], $data['type']);
            }
            
            $this->db->commit();
            
            // Log activity
            $this->logActivity('SETTINGS_RESET', "Reset all settings to defaults");
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error resetting settings: " . $e->getMessage());
        }
    }
    
    /**
     * Export settings
     */
    public function exportSettings() {
        try {
            $settings = $this->getAllSettings();
            
            // Create JSON export
            $export = [
                'export_date' => date('Y-m-d H:i:s'),
                'exported_by' => $this->auth->getCurrentUserId(),
                'settings' => $settings
            ];
            
            $json = json_encode($export, JSON_PRETTY_PRINT);
            
            // Set headers for download
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="settings_export_' . date('Y-m-d') . '.json"');
            header('Content-Length: ' . strlen($json));
            
            echo $json;
            exit;
            
        } catch (Exception $e) {
            throw new Exception("Error exporting settings: " . $e->getMessage());
        }
    }
    
    /**
     * Import settings
     */
    public function importSettings($jsonFile) {
        try {
            if (!file_exists($jsonFile)) {
                throw new Exception("Import file not found");
            }
            
            $json = file_get_contents($jsonFile);
            $data = json_decode($json, true);
            
            if (!$data || !isset($data['settings'])) {
                throw new Exception("Invalid import file format");
            }
            
            $this->db->beginTransaction();
            
            foreach ($data['settings'] as $key => $setting) {
                $this->updateSetting($key, $setting['value'], $setting['type']);
            }
            
            $this->db->commit();
            
            // Log activity
            $this->logActivity('SETTINGS_IMPORTED', "Imported settings from file");
            
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Error importing settings: " . $e->getMessage());
        }
    }
    
    /**
     * Validate setting
     */
    private function validateSetting($key, $value, $type) {
        // Validate key
        $validKeys = array_keys($this->getDefaultSettings());
        if (!in_array($key, $validKeys)) {
            throw new Exception("Invalid setting key: $key");
        }
        
        // Validate based on type
        switch ($type) {
            case 'boolean':
                if (!is_bool($value) && !in_array($value, ['0', '1', 'true', 'false'])) {
                    throw new Exception("Invalid boolean value for setting: $key");
                }
                break;
                
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    throw new Exception("Invalid integer value for setting: $key");
                }
                break;
                
            case 'string':
                if (!is_string($value)) {
                    throw new Exception("Invalid string value for setting: $key");
                }
                break;
        }
        
        // Specific validations
        switch ($key) {
            case 'session_timeout':
                $value = (int)$value;
                if ($value < 5 || $value > 480) {
                    throw new Exception("Session timeout must be between 5 and 480 minutes");
                }
                break;
                
            case 'max_file_size':
                $value = (int)$value;
                if ($value < 1 || $value > 50) {
                    throw new Exception("Max file size must be between 1 and 50 MB");
                }
                break;
                
            case 'app_name':
                if (strlen($value) < 3 || strlen($value) > 100) {
                    throw new Exception("App name must be between 3 and 100 characters");
                }
                break;
        }
    }
    
    /**
     * Convert setting to proper type
     */
    private function convertSettingType($value, $type) {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int)$value;
            case 'float':
                return (float)$value;
            default:
                return $value;
        }
    }
    
    /**
     * Get default setting value
     */
    private function getDefaultSetting($key) {
        $defaults = $this->getDefaultSettings();
        return $defaults[$key]['value'] ?? null;
    }
    
    /**
     * Get all default settings
     */
    private function getDefaultSettings() {
        return [
            'app_name' => [
                'value' => 'BAGOPS POLRES SAMOSIR',
                'type' => 'string',
                'description' => 'Application name displayed in header'
            ],
            'app_version' => [
                'value' => '1.0.0',
                'type' => 'string',
                'description' => 'Current application version'
            ],
            'session_timeout' => [
                'value' => 120,
                'type' => 'integer',
                'description' => 'Session timeout in minutes'
            ],
            'max_file_size' => [
                'value' => 10,
                'type' => 'integer',
                'description' => 'Maximum file upload size in MB'
            ],
            'debug_mode' => [
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable debug mode for development'
            ],
            'maintenance_mode' => [
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable maintenance mode'
            ],
            'timezone' => [
                'value' => 'Asia/Jakarta',
                'type' => 'string',
                'description' => 'Application timezone'
            ],
            'date_format' => [
                'value' => 'd-m-Y',
                'type' => 'string',
                'description' => 'Date display format'
            ],
            'time_format' => [
                'value' => 'H:i:s',
                'type' => 'string',
                'description' => 'Time display format'
            ],
            'items_per_page' => [
                'value' => 25,
                'type' => 'integer',
                'description' => 'Default number of items per page'
            ]
        ];
    }
    
    /**
     * Get setting description
     */
    private function getSettingDescription($key) {
        $defaults = $this->getDefaultSettings();
        return $defaults[$key]['description'] ?? '';
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $description) {
        try {
            $userId = $this->auth->getCurrentUserId();
            $stmt = $this->db->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $userId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ]);
        } catch (Exception $e) {
            // Log error but don't throw
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
?>
