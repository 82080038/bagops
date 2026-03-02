<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Validate required fields
    $required_fields = ['tanggal_laporan', 'shift', 'lokasi'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' harus diisi']);
            exit;
        }
    }

    // Get form data
    $tanggal_laporan = $_POST['tanggal_laporan'];
    $shift = $_POST['shift'];
    $lokasi = trim($_POST['lokasi']);
    $kanit_nama = trim($_POST['kanit_nama'] ?? '');
    $jumlah_personel = !empty($_POST['jumlah_personel']) ? (int)$_POST['jumlah_personel'] : null;
    $jumlah_mobil = !empty($_POST['jumlah_mobil']) ? (int)$_POST['jumlah_mobil'] : null;

    // Handle JSON fields
    $kegiatan = isset($_POST['kegiatan']) ? json_decode($_POST['kegiatan'], true) : [];
    $kegiatan_detail = isset($_POST['kegiatan_detail']) ? json_decode($_POST['kegiatan_detail'], true) : [];

    // Other fields
    $kejadian_kasus = trim($_POST['kejadian_kasus'] ?? '');
    $situasi_umum = $_POST['situasi_umum'] ?? 'aman';
    $cuaca = $_POST['cuaca'] ?? 'cerah';
    $catatan_rekomendasi = trim($_POST['catatan_rekomendasi'] ?? '');

    // Validate shift
    $valid_shifts = ['pagi', 'siang', 'malam'];
    if (!in_array($shift, $valid_shifts)) {
        echo json_encode(['success' => false, 'message' => 'Shift tidak valid']);
        exit;
    }

    // Validate situasi_umum
    $valid_situasi = ['aman', 'waspada', 'siaga', 'darurat'];
    if (!in_array($situasi_umum, $valid_situasi)) {
        $situasi_umum = 'aman';
    }

    // Validate cuaca
    $valid_cuaca = ['cerah', 'berawan', 'hujan', 'mendung'];
    if (!in_array($cuaca, $valid_cuaca)) {
        $cuaca = 'cerah';
    }

    // Check if laphar table exists, if not create it
    $tableCheck = $pdo->query("SHOW TABLES LIKE 'laphar'");
    if ($tableCheck->rowCount() == 0) {
        // Create laphar table
        $pdo->exec("
            CREATE TABLE laphar (
                id INT PRIMARY KEY AUTO_INCREMENT,
                tanggal_laporan DATE NOT NULL,
                shift ENUM('pagi', 'siang', 'malam') NOT NULL,
                lokasi VARCHAR(255) NOT NULL,
                kanit_nama VARCHAR(100),
                jumlah_personel INT,
                jumlah_mobil INT,
                kegiatan JSON,
                kegiatan_detail JSON,
                kejadian_kasus TEXT,
                situasi_umum ENUM('aman', 'waspada', 'siaga', 'darurat') DEFAULT 'aman',
                cuaca ENUM('cerah', 'berawan', 'hujan', 'mendung') DEFAULT 'cerah',
                catatan_rekomendasi TEXT,
                latitude DECIMAL(10,8),
                longitude DECIMAL(11,8),
                created_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
            )
        ");
    }

    // Insert Laphar record
    $stmt = $pdo->prepare("
        INSERT INTO laphar (
            tanggal_laporan, shift, lokasi, kanit_nama, jumlah_personel, jumlah_mobil,
            kegiatan, kegiatan_detail, kejadian_kasus, situasi_umum, cuaca,
            catatan_rekomendasi, created_by, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $tanggal_laporan,
        $shift,
        $lokasi,
        $kanit_nama ?: null,
        $jumlah_personel,
        $jumlah_mobil,
        json_encode($kegiatan),
        json_encode($kegiatan_detail),
        $kejadian_kasus ?: null,
        $situasi_umum,
        $cuaca,
        $catatan_rekomendasi ?: null,
        $currentUser['id']
    ]);

    $lapharId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Laphar berhasil dibuat',
        'laphar_id' => $lapharId
    ]);

} catch (Exception $e) {
    error_log("Save Laphar error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan Laphar: ' . $e->getMessage()
    ]);
}
?>
