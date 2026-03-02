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
    echo json_encode(['success' => false, 'message' => 'Invalid Laphar ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get Laphar data with security check
    $stmt = $pdo->prepare("
        SELECT l.*, u.name as created_by_name
        FROM laphar l
        LEFT JOIN users u ON l.created_by = u.id
        WHERE l.id = ?
    ");
    $stmt->execute([$id]);
    $laphar = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$laphar) {
        echo json_encode(['success' => false, 'message' => 'Laphar not found']);
        exit;
    }

    // Check if user can access this Laphar (creator or admin)
    if ($laphar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }

    if ($edit) {
        // Return data for editing
        echo json_encode([
            'success' => true,
            'edit_data' => $laphar
        ]);
    } else {
        // Return formatted content for viewing
        ob_start();
        ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6>Informasi Laporan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Tanggal:</strong><br>
                            <span><?php echo date('d F Y', strtotime($laphar['tanggal_laporan'])); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Shift:</strong><br>
                            <span class="badge bg-info">
                                <?php
                                switch ($laphar['shift']) {
                                    case 'pagi': echo 'PAGI (06:00-14:00)';
                                        break;
                                    case 'siang': echo 'SIANG (14:00-22:00)';
                                        break;
                                    case 'malam': echo 'MALAM (22:00-06:00)';
                                        break;
                                    default: echo strtoupper($laphar['shift']);
                                }
                                ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Lokasi:</strong><br>
                            <span><?php echo htmlspecialchars($laphar['lokasi']); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Kanit/Kapolsek:</strong><br>
                            <span><?php echo htmlspecialchars($laphar['kanit_nama'] ?: '-'); ?></span>
                        </div>
                        <div class="mb-2">
                            <strong>Jumlah Personel:</strong><br>
                            <span><?php echo htmlspecialchars($laphar['jumlah_personel'] ?: '-'); ?> orang</span>
                        </div>
                        <div class="mb-2">
                            <strong>Jumlah Mobil:</strong><br>
                            <span><?php echo htmlspecialchars($laphar['jumlah_mobil'] ?: '-'); ?> unit</span>
                        </div>
                        <div class="mb-2">
                            <strong>Dibuat Oleh:</strong><br>
                            <span><?php echo htmlspecialchars($laphar['created_by_name'] ?: '-'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h6>Situasi & Kondisi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Situasi Umum:</strong><br>
                            <span class="badge bg-<?php
                                switch ($laphar['situasi_umum']) {
                                    case 'aman': echo 'success';
                                        break;
                                    case 'waspada': echo 'warning';
                                        break;
                                    case 'siaga': echo 'danger';
                                        break;
                                    case 'darurat': echo 'dark';
                                        break;
                                    default: echo 'secondary';
                                }
                            ?>">
                                <?php echo strtoupper($laphar['situasi_umum'] ?: 'aman'); ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Cuaca:</strong><br>
                            <span><?php echo ucfirst($laphar['cuaca'] ?: 'cerah'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $kegiatan = $laphar['kegiatan'] ? json_decode($laphar['kegiatan'], true) : [];
        $kegiatanDetail = $laphar['kegiatan_detail'] ? json_decode($laphar['kegiatan_detail'], true) : [];

        if (!empty($kegiatan)):
        ?>
        <div class="card mb-3">
            <div class="card-header">
                <h6>Kegiatan Yang Dilakukan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php
                    $activities = [
                        'patroli' => 'Patroli Rutin',
                        'pengamanan' => 'Pengamanan Objek Vital',
                        'hukum' => 'Penegakan Hukum',
                        'lain' => 'Kegiatan Lain'
                    ];

                    foreach ($activities as $key => $label) {
                        if (!empty($kegiatan[$key])) {
                            echo '<div class="col-md-6 mb-3">';
                            echo '<h6>' . $label . ' ✓</h6>';
                            if (!empty($kegiatanDetail[$key])) {
                                echo '<div class="border-start border-primary border-3 ps-3">';
                                echo '<small>' . nl2br(htmlspecialchars($kegiatanDetail[$key])) . '</small>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($laphar['kejadian_kasus'])): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h6>Kejadian/Kasus Yang Terjadi</h6>
            </div>
            <div class="card-body">
                <p><?php echo nl2br(htmlspecialchars($laphar['kejadian_kasus'])); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($laphar['catatan_rekomendasi'])): ?>
        <div class="card mb-3">
            <div class="card-header">
                <h6>Catatan & Rekomendasi</h6>
            </div>
            <div class="card-body">
                <p><?php echo nl2br(htmlspecialchars($laphar['catatan_rekomendasi'])); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <?php

        $content = ob_get_clean();

        echo json_encode([
            'success' => true,
            'content' => $content
        ]);
    }

} catch (Exception $e) {
    error_log("Get Laphar error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail Laphar'
    ]);
}
?>
