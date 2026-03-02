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

    // Get form data
    $kode_aar = trim($_POST['kode_aar'] ?? '');
    $nomor_aar = trim($_POST['nomor_aar'] ?? '');
    $tanggal_aar = $_POST['tanggal_aar'] ?? null;
    $waktu_pelaksanaan = $_POST['waktu_pelaksanaan'] ?? null;
    $operasi_id = !empty($_POST['operasi_id']) ? (int)$_POST['operasi_id'] : null;
    $renops_id = !empty($_POST['renops_id']) ? (int)$_POST['renops_id'] : null;
    $lokasi_pelaksanaan = trim($_POST['lokasi_pelaksanaan'] ?? '');
    $ringkas_eksekusi = trim($_POST['ringkas_eksekusi'] ?? '');
    $yang_berjalan_baik = trim($_POST['yang_berjalan_baik'] ?? '');
    $yang_perlu_diperbaiki = trim($_POST['yang_perlu_diperbaiki'] ?? '');
    $hambatan_kendala = trim($_POST['hambatan_kendala'] ?? '');
    $insiden_pelanggaran = trim($_POST['insiden_pelanggaran'] ?? '');
    $rekomendasi_tindak_lanjut = trim($_POST['rekomendasi_tindak_lanjut'] ?? '');
    $status = $_POST['status'] ?? 'DRAFT';

    // Handle JSON fields
    $data_metrik = $_POST['data_metrik'] ?? '{}';
    $perubahan_sop = $_POST['perubahan_sop'] ?? '{}';

    // Validate required fields
    if (empty($tanggal_aar)) {
        echo json_encode(['success' => false, 'message' => 'Tanggal AAR harus diisi']);
        exit;
    }

    if (empty($lokasi_pelaksanaan)) {
        echo json_encode(['success' => false, 'message' => 'Lokasi pelaksanaan harus diisi']);
        exit;
    }

    // Validate status
    $validStatuses = ['DRAFT', 'SUBMITTED'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit;
    }

    // Insert AAR record
    $stmt = $pdo->prepare("
        INSERT INTO aar (
            kode_aar, nomor_aar, tanggal_aar, waktu_pelaksanaan,
            operasi_id, renops_id, lokasi_pelaksanaan,
            ringkas_eksekusi, yang_berjalan_baik, yang_perlu_diperbaiki,
            hambatan_kendala, insiden_pelanggaran, data_metrik,
            rekomendasi_tindak_lanjut, perubahan_sop, status,
            created_by, created_at, updated_at
        ) VALUES (
            ?, ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, NOW(), NOW()
        )
    ");

    $stmt->execute([
        $kode_aar ?: null,
        $nomor_aar ?: null,
        $tanggal_aar,
        $waktu_pelaksanaan ?: null,
        $operasi_id,
        $renops_id,
        $lokasi_pelaksanaan,
        $ringkas_eksekusi ?: null,
        $yang_berjalan_baik ?: null,
        $yang_perlu_diperbaiki ?: null,
        $hambatan_kendala ?: null,
        $insiden_pelanggaran ?: null,
        $data_metrik,
        $rekomendasi_tindak_lanjut ?: null,
        $perubahan_sop,
        $status,
        $currentUser['id']
    ]);

    $aarId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'AAR berhasil dibuat',
        'aar_id' => $aarId
    ]);

} catch (Exception $e) {
    error_log("Save AAR error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat menyimpan AAR: ' . $e->getMessage()
    ]);
}
?>
