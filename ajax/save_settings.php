<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication and admin access
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

// Only admins can manage settings
$currentUser = $auth->getCurrentUser();
if ($currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();

    // Get settings data from POST
    $settings = $_POST['settings'] ?? [];

    if (empty($settings)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada pengaturan yang dikirim']);
        exit;
    }

    $updatedCount = 0;
    $errors = [];

    // Process each setting
    foreach ($settings as $settingKey => $settingValue) {
        try {
            // Get setting info to determine type
            $stmt = $pdo->prepare("SELECT setting_type, is_system FROM settings WHERE setting_key = ?");
            $stmt->execute([$settingKey]);
            $settingInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$settingInfo) {
                $errors[] = "Setting '{$settingKey}' tidak ditemukan";
                continue;
            }

            // Prevent modification of system settings by non-super-admin
            if ($settingInfo['is_system'] && $currentUser['role'] !== 'super_admin') {
                $errors[] = "Setting system '{$settingKey}' hanya dapat diubah oleh super admin";
                continue;
            }

            // Process value based on type
            $processedValue = $settingValue;

            switch ($settingInfo['setting_type']) {
                case 'boolean':
                    $processedValue = ($settingValue === 'true' || $settingValue === '1') ? 'true' : 'false';
                    break;

                case 'integer':
                    $processedValue = (string)(int)$settingValue;
                    break;

                case 'json':
                    // Convert comma-separated string to JSON array
                    if (is_string($settingValue) && strpos($settingValue, ',') !== false) {
                        $arrayValues = array_map('trim', explode(',', $settingValue));
                        $processedValue = json_encode($arrayValues);
                    } else {
                        $processedValue = json_encode([$settingValue]);
                    }
                    break;

                default: // string
                    $processedValue = (string)$settingValue;
            }

            // Update setting
            $stmt = $pdo->prepare("
                UPDATE settings
                SET setting_value = ?, updated_at = NOW(), updated_by = ?
                WHERE setting_key = ?
            ");
            $stmt->execute([$processedValue, $currentUser['id'], $settingKey]);
            $updatedCount++;

        } catch (Exception $e) {
            $errors[] = "Error updating '{$settingKey}': " . $e->getMessage();
        }
    }

    // Clear any cached config (if caching is implemented)
    // This would clear any cached configuration values

    $message = "Berhasil memperbarui {$updatedCount} pengaturan";
    if (!empty($errors)) {
        $message .= ". Beberapa error: " . implode('; ', $errors);
    }

    echo json_encode([
        'success' => true,
        'message' => $message,
        'updated_count' => $updatedCount,
        'errors' => $errors
    ]);

} catch (Exception $e) {
    error_log("Save Settings error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan pengaturan: ' . $e->getMessage()
    ]);
}
?>
