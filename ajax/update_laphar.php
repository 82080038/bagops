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

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Laphar ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Check if user owns this Laphar or is admin
    $stmt = $pdo->prepare("SELECT created_by FROM laphar WHERE id = ?");
    $stmt->execute([$id]);
    $laphar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$laphar) {
        echo json_encode(['success' => false, 'message' => 'Laphar not found']);
        exit;
    }

    if ($laphar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
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

    // Validate required fields
    if (empty($tanggal_laporan) || empty($shift) || empty($lokasi)) {
        echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
        exit;
    }

    // Validate shift
    $valid_shifts = ['pagi', 'siang', 'malam'];
    if (!in_array($shift, $valid_shifts)) {
        echo json_encode(['success' => false, 'message' => 'Shift tidak valid']);
        exit;
    }

    // Update Laphar record
    $stmt = $pdo->prepare("
        UPDATE laphar SET
            tanggal_laporan = ?,
            shift = ?,
            lokasi = ?,
            kanit_nama = ?,
            jumlah_personel = ?,
            jumlah_mobil = ?,
            kegiatan = ?,
            kegiatan_detail = ?,
            kejadian_kasus = ?,
            situasi_umum = ?,
            cuaca = ?,
            catatan_rekomendasi = ?,
            updated_at = NOW()
        WHERE id = ?
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
        $id
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Laphar berhasil diperbarui'
    ]);

} catch (Exception $e) {
    error_log("Update Laphar error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memperbarui Laphar: ' . $e->getMessage()
    ]);
}
?>
