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

    // Check if settings table exists, create if not
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'settings'");
    if ($tableCheck->rowCount() == 0) {
        // Create settings table
        $pdo->exec("
            CREATE TABLE settings (
                id INT PRIMARY KEY AUTO_INCREMENT,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                setting_type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
                description TEXT,
                category VARCHAR(50) DEFAULT 'general',
                is_system BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                updated_by INT,
                FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
            )
        ");

        // Insert default settings
        $defaultSettings = [
            ['app_name', 'BAGOPS Polres Samosir', 'string', 'Nama aplikasi', 'general', true],
            ['maintenance_mode', 'false', 'boolean', 'Mode maintenance', 'system', true],
            ['debug_mode', 'true', 'boolean', 'Mode debug', 'system', true],
            ['max_upload_size', '2097152', 'integer', 'Ukuran maksimal upload (bytes)', 'upload', false],
            ['allowed_file_types', '["pdf","jpg","jpeg","png","doc","docx"]', 'json', 'Tipe file yang diizinkan', 'upload', false],
            ['session_lifetime', '7200', 'integer', 'Lama sesi (detik)', 'security', false],
            ['password_min_length', '6', 'integer', 'Panjang minimal password', 'security', false],
            ['email_notifications', 'false', 'boolean', 'Aktifkan notifikasi email', 'notifications', false],
            ['sms_gateway_enabled', 'false', 'boolean', 'Aktifkan SMS gateway', 'notifications', false],
            ['auto_backup', 'true', 'boolean', 'Backup otomatis database', 'system', false]
        ];

        $stmt = $pdo->prepare("
            INSERT INTO settings (setting_key, setting_value, setting_type, description, category, is_system)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($defaultSettings as $setting) {
            $stmt->execute($setting);
        }
    }

    // Get settings data
    $stmt = $pdo->query("
        SELECT * FROM settings
        ORDER BY category, setting_key
    ");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group settings by category
    $groupedSettings = [];
    foreach ($settings as $setting) {
        $category = $setting['category'];
        if (!isset($groupedSettings[$category])) {
            $groupedSettings[$category] = [];
        }
        $groupedSettings[$category][] = $setting;
    }

    // Return formatted content for viewing/editing
    ob_start();
    ?>
    <div class="settings-container">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Pengaturan Sistem:</strong> Hati-hati dalam mengubah pengaturan sistem. Perubahan dapat mempengaruhi fungsionalitas aplikasi.
        </div>

        <form id="settingsForm">
            <?php foreach ($groupedSettings as $category => $categorySettings): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-<?php
                                switch ($category) {
                                    case 'general': echo 'cogs';
                                        break;
                                    case 'system': echo 'server';
                                        break;
                                    case 'upload': echo 'upload';
                                        break;
                                    case 'security': echo 'shield-alt';
                                        break;
                                    case 'notifications': echo 'bell';
                                        break;
                                    default: echo 'cog';
                                }
                            ?> me-2"></i>
                            <?php echo ucfirst($category); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($categorySettings as $setting): ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">
                                        <?php echo htmlspecialchars($setting['setting_key']); ?>
                                        <?php if ($setting['is_system']): ?>
                                            <span class="badge bg-warning ms-1">System</span>
                                        <?php endif; ?>
                                    </label>
                                    <?php if ($setting['description']): ?>
                                        <small class="form-text text-muted"><?php echo htmlspecialchars($setting['description']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    $value = $setting['setting_value'];
                                    $inputName = 'settings[' . $setting['setting_key'] . ']';
                                    $inputId = 'setting_' . $setting['setting_key'];

                                    switch ($setting['setting_type']) {
                                        case 'boolean':
                                            $checked = ($value === 'true' || $value === '1') ? 'checked' : '';
                                            echo '<div class="form-check form-switch">';
                                            echo '<input class="form-check-input" type="checkbox" id="' . $inputId . '" name="' . $inputName . '" value="true" ' . $checked . '>';
                                            echo '<label class="form-check-label" for="' . $inputId . '">';
                                            echo ($value === 'true' || $value === '1') ? 'Enabled' : 'Disabled';
                                            echo '</label>';
                                            echo '</div>';
                                            break;

                                        case 'integer':
                                            echo '<input type="number" class="form-control" id="' . $inputId . '" name="' . $inputName . '" value="' . htmlspecialchars($value) . '">';
                                            break;

                                        case 'json':
                                            $jsonValue = json_decode($value, true);
                                            if (is_array($jsonValue)) {
                                                $displayValue = implode(', ', $jsonValue);
                                            } else {
                                                $displayValue = $value;
                                            }
                                            echo '<input type="text" class="form-control" id="' . $inputId . '" name="' . $inputName . '" value="' . htmlspecialchars($displayValue) . '" placeholder="Comma-separated values">';
                                            echo '<small class="form-text text-muted">Gunakan koma untuk memisahkan nilai</small>';
                                            break;

                                        default: // string
                                            echo '<input type="text" class="form-control" id="' . $inputId . '" name="' . $inputName . '" value="' . htmlspecialchars($value) . '">';
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </div>

    <script>
        // Update checkbox labels dynamically
        document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const label = this.parentNode.querySelector('.form-check-label');
                if (label) {
                    label.textContent = this.checked ? 'Enabled' : 'Disabled';
                }
            });
        });
    </script>
    <?php

    $content = ob_get_clean();

    echo json_encode([
        'success' => true,
        'content' => $content,
        'settings' => $settings
    ]);

} catch (Exception $e) {
    error_log("Get Settings error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat pengaturan: ' . $e->getMessage()
    ]);
}
?>
