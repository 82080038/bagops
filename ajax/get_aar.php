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
$edit = isset($_POST['edit']) && $_POST['edit'] === 'true';

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid AAR ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get AAR data with security check
    $stmt = $pdo->prepare("
        SELECT a.*, e.title as event_title, o.title as operation_title,
               u.name as created_by_name, u2.name as approved_by_name
        FROM aar a
        LEFT JOIN events e ON a.renops_id = e.id
        LEFT JOIN operations o ON a.operasi_id = o.id
        LEFT JOIN users u ON a.created_by = u.id
        LEFT JOIN users u2 ON a.approved_by = u2.id
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $aar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aar) {
        echo json_encode(['success' => false, 'message' => 'AAR not found']);
        exit;
    }

    // Check if user can access this AAR (creator or admin)
    if ($aar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    if ($edit) {
        // Return data for editing
        echo json_encode([
            'success' => true,
            'edit_data' => $aar
        ]);
    } else {
        // Return formatted content for viewing
        ob_start();
        ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo htmlspecialchars($aar['kode_aar'] ?: $aar['nomor_aar'] ?: 'AAR-' . $aar['id']); ?></h5>
                        <small class="text-muted"><?php echo htmlspecialchars($aar['event_title'] ?: $aar['operation_title'] ?: 'Tidak ada judul'); ?></small>
                    </div>
                    <div class="card-body">
                        <?php if ($aar['ringkas_eksekusi']): ?>
                        <div class="mb-3">
                            <h6>Ringkasan Eksekusi</h6>
                            <p><?php echo nl2br(htmlspecialchars($aar['ringkas_eksekusi'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <?php if ($aar['yang_berjalan_baik']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-success">✅ Yang Berjalan Baik</h6>
                                <p><?php echo nl2br(htmlspecialchars($aar['yang_berjalan_baik'])); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if ($aar['yang_perlu_diperbaiki']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-warning">⚠️ Yang Perlu Diperbaiki</h6>
                                <p><?php echo nl2br(htmlspecialchars($aar['yang_perlu_diperbaiki'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <?php if ($aar['hambatan_kendala']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-danger">🚫 Hambatan & Kendala</h6>
                                <p><?php echo nl2br(htmlspecialchars($aar['hambatan_kendala'])); ?></p>
                            </div>
                            <?php endif; ?>

                            <?php if ($aar['insiden_pelanggaran']): ?>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-danger">⚡ Insiden & Pelanggaran</h6>
                                <p><?php echo nl2br(htmlspecialchars($aar['insiden_pelanggaran'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($aar['rekomendasi_tindak_lanjut']): ?>
                        <div class="mb-3">
                            <h6>💡 Rekomendasi & Tindak Lanjut</h6>
                            <p><?php echo nl2br(htmlspecialchars($aar['rekomendasi_tindak_lanjut'])); ?></p>
                        </div>
                        <?php endif; ?>

                        <?php
                        $dataMetrik = $aar['data_metrik'];
                        if ($dataMetrik) {
                            if (is_string($dataMetrik)) {
                                $dataMetrik = json_decode($dataMetrik, true);
                            }
                            if (is_array($dataMetrik) && (!empty($dataMetrik['response_time']) || !empty($dataMetrik['personel_count']) || !empty($dataMetrik['resource_utilization']))):
                        ?>
                        <div class="mb-3">
                            <h6>📊 Data Metrik</h6>
                            <div class="row">
                                <?php if (!empty($dataMetrik['response_time'])): ?>
                                <div class="col-md-4">
                                    <strong>Response Time:</strong><br>
                                    <?php echo htmlspecialchars($dataMetrik['response_time']); ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($dataMetrik['personel_count'])): ?>
                                <div class="col-md-4">
                                    <strong>Jumlah Personel:</strong><br>
                                    <?php echo htmlspecialchars($dataMetrik['personel_count']); ?> orang
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($dataMetrik['resource_utilization'])): ?>
                                <div class="col-md-4">
                                    <strong>Resource Utilization:</strong><br>
                                    <?php echo htmlspecialchars($dataMetrik['resource_utilization']); ?>%
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; } ?>

                        <?php
                        $perubahanSop = $aar['perubahan_sop'];
                        if ($perubahanSop) {
                            if (is_string($perubahanSop)) {
                                $perubahanSop = json_decode($perubahanSop, true);
                            }
                            if (is_array($perubahanSop) && (!empty($perubahanSop['perubahan']) || !empty($perubahanSop['alasan']))):
                        ?>
                        <div class="mb-3">
                            <h6>🔄 Perubahan SOP</h6>
                            <?php if (!empty($perubahanSop['perubahan'])): ?>
                            <p><strong>Perubahan:</strong><br><?php echo nl2br(htmlspecialchars(is_array($perubahanSop['perubahan']) ? implode("\n", $perubahanSop['perubahan']) : $perubahanSop['perubahan'])); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($perubahanSop['alasan'])): ?>
                            <p><strong>Alasan:</strong><br><?php echo nl2br(htmlspecialchars($perubahanSop['alasan'])); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; } ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi AAR</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Tanggal AAR:</strong><br>
                            <span><?php echo $aar['tanggal_aar'] ? date('d F Y', strtotime($aar['tanggal_aar'])) : date('d F Y', strtotime($aar['created_at'])); ?></span>
                        </div>

                        <?php if ($aar['waktu_pelaksanaan']): ?>
                        <div class="mb-2">
                            <strong>Waktu Pelaksanaan:</strong><br>
                            <span><?php echo date('d/m/Y H:i', strtotime($aar['waktu_pelaksanaan'])); ?></span>
                        </div>
                        <?php endif; ?>

                        <div class="mb-2">
                            <strong>Lokasi:</strong><br>
                            <span><?php echo htmlspecialchars($aar['lokasi_pelaksanaan'] ?: 'Tidak ditentukan'); ?></span>
                        </div>

                        <div class="mb-2">
                            <strong>Status:</strong><br>
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch ($aar['status']) {
                                case 'DRAFT':
                                    $statusClass = 'badge bg-secondary';
                                    $statusText = 'Draft';
                                    break;
                                case 'SUBMITTED':
                                    $statusClass = 'badge bg-info';
                                    $statusText = 'Diajukan';
                                    break;
                                case 'APPROVED':
                                    $statusClass = 'badge bg-success';
                                    $statusText = 'Disetujui';
                                    break;
                                case 'REJECTED':
                                    $statusClass = 'badge bg-danger';
                                    $statusText = 'Ditolak';
                                    break;
                                default:
                                    $statusClass = 'badge bg-secondary';
                                    $statusText = 'Unknown';
                            }
                            ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </div>

                        <div class="mb-2">
                            <strong>Dibuat Oleh:</strong><br>
                            <span><?php echo htmlspecialchars($aar['created_by_name'] ?: 'Tidak diketahui'); ?></span>
                        </div>

                        <?php if ($aar['approved_by_name']): ?>
                        <div class="mb-2">
                            <strong>Disetujui Oleh:</strong><br>
                            <span><?php echo htmlspecialchars($aar['approved_by_name']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($aar['operasi_id'] || $aar['renops_id']): ?>
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Terkait Dengan</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($aar['operation_title']): ?>
                        <div class="mb-2">
                            <strong>Operasi:</strong><br>
                            <span><?php echo htmlspecialchars($aar['operation_title']); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if ($aar['event_title']): ?>
                        <div class="mb-2">
                            <strong>Event:</strong><br>
                            <span><?php echo htmlspecialchars($aar['event_title']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php

        $content = ob_get_clean();

        echo json_encode([
            'success' => true,
            'content' => $content
        ]);
    }

} catch (Exception $e) {
    error_log("Get AAR error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail AAR'
    ]);
}
?>
