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

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die('Invalid Laphar ID');
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
        die('Laphar not found');
    }

    // Check if user can access this Laphar (creator or admin)
    if ($laphar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        die('Access denied');
    }

    // Set headers for PDF download
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="LAPHAR_' . date('Y-m-d', strtotime($laphar['tanggal_laporan'])) . '_' . $laphar['shift'] . '.html"');

    // Generate HTML content that can be saved as PDF
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LAPHAR - <?php echo date('d/m/Y', strtotime($laphar['tanggal_laporan'])) . ' ' . ucfirst($laphar['shift']); ?></title>
        <style>
            body {
                font-family: 'Times New Roman', serif;
                font-size: 12px;
                line-height: 1.4;
                margin: 20px;
                color: #000;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 2px solid #000;
                padding-bottom: 10px;
            }
            .header h1 {
                font-size: 18px;
                margin: 0;
                text-transform: uppercase;
                font-weight: bold;
            }
            .header h2 {
                font-size: 14px;
                margin: 5px 0;
                font-weight: normal;
            }
            .section {
                margin-bottom: 20px;
            }
            .section h3 {
                font-size: 14px;
                margin-bottom: 8px;
                text-decoration: underline;
                font-weight: bold;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }
            .info-table td {
                padding: 4px 8px;
                border: 1px solid #ccc;
            }
            .info-table .label {
                width: 30%;
                font-weight: bold;
                background-color: #f5f5f5;
            }
            .content-box {
                border: 1px solid #000;
                padding: 10px;
                margin-bottom: 15px;
                min-height: 40px;
            }
            .activity-section {
                margin-bottom: 15px;
            }
            .activity-header {
                font-weight: bold;
                margin-bottom: 5px;
            }
            .checkbox-result {
                margin-left: 20px;
            }
            .signature-section {
                margin-top: 40px;
                display: table;
                width: 100%;
            }
            .signature-box {
                display: table-cell;
                width: 50%;
                text-align: center;
                vertical-align: top;
            }
            .signature-line {
                border-bottom: 1px solid #000;
                margin-top: 40px;
                padding-bottom: 5px;
            }
        </style>
    </head>
    <body>

        <!-- Header -->
        <div class="header">
            <h1>KESATUAN REPUBLIK INDONESIA</h1>
            <h1>POLISI DAERAH SUMATERA UTARA</h1>
            <h2>RESOR SAMOSIR</h2>
            <h2>LAPORAN HARIAN POLISI (LAPHAR)</h2>
        </div>

        <!-- Laphar Information -->
        <div class="section">
            <h3>INFORMASI LAPORAN</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Tanggal Laporan</td>
                    <td><?php echo date('d F Y', strtotime($laphar['tanggal_laporan'])); ?></td>
                    <td class="label">Shift</td>
                    <td>
                        <?php
                        switch ($laphar['shift']) {
                            case 'pagi': echo 'PAGI (06:00 - 14:00)';
                                break;
                            case 'siang': echo 'SIANG (14:00 - 22:00)';
                                break;
                            case 'malam': echo 'MALAM (22:00 - 06:00)';
                                break;
                            default: echo strtoupper($laphar['shift']);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td class="label">Lokasi/Wilayah</td>
                    <td colspan="3"><?php echo htmlspecialchars($laphar['lokasi']); ?></td>
                </tr>
            </table>
        </div>

        <!-- Personnel Information -->
        <div class="section">
            <h3>PERSONEL YANG BERTUGAS</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Kanit/Kapolsek</td>
                    <td><?php echo htmlspecialchars($laphar['kanit_nama'] ?: '-'); ?></td>
                    <td class="label">Jumlah Personel</td>
                    <td><?php echo htmlspecialchars($laphar['jumlah_personel'] ?: '0'); ?> orang</td>
                </tr>
                <tr>
                    <td class="label">Jumlah Mobil</td>
                    <td><?php echo htmlspecialchars($laphar['jumlah_mobil'] ?: '0'); ?> unit</td>
                    <td class="label">Pelapor</td>
                    <td><?php echo htmlspecialchars($laphar['created_by_name'] ?: '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Activities Conducted -->
        <div class="section">
            <h3>KEGIATAN YANG DILAKUKAN</h3>

            <?php
            $kegiatan = $laphar['kegiatan'];
            if ($kegiatan) {
                if (is_string($kegiatan)) {
                    $kegiatan = json_decode($kegiatan, true);
                }

                $kegiatanDetail = $laphar['kegiatan_detail'];
                if ($kegiatanDetail && is_string($kegiatanDetail)) {
                    $kegiatanDetail = json_decode($kegiatanDetail, true);
                }

                $activities = [
                    'patroli' => 'Patroli Rutin',
                    'pengamanan' => 'Pengamanan Objek Vital',
                    'hukum' => 'Penegakan Hukum',
                    'lain' => 'Kegiatan Lain'
                ];

                foreach ($activities as $key => $label) {
                    echo '<div class="activity-section">';
                    echo '<div class="activity-header">' . $label . ':</div>';
                    echo '<div class="checkbox-result">';
                    if (!empty($kegiatan[$key])) {
                        echo '✓ DILAKUKAN';
                        if (!empty($kegiatanDetail[$key])) {
                            echo '<div class="content-box" style="margin-top: 5px; font-size: 11px;">';
                            echo nl2br(htmlspecialchars($kegiatanDetail[$key]));
                            echo '</div>';
                        }
                    } else {
                        echo '✗ TIDAK DILAKUKAN';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="content-box">Tidak ada data kegiatan tersedia</div>';
            }
            ?>
        </div>

        <!-- Incidents/Cases -->
        <div class="section">
            <h3>KEJADIAN/KASUS YANG TERJADI</h3>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($laphar['kejadian_kasus'] ?: 'Tidak ada kejadian/kasus yang dilaporkan')); ?>
            </div>
        </div>

        <!-- Situation & Conditions -->
        <div class="section">
            <h3>SITUASI & KONDISI WILAYAH</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Situasi Umum</td>
                    <td>
                        <?php
                        switch ($laphar['situasi_umum']) {
                            case 'aman': echo 'AMAN';
                                break;
                            case 'waspada': echo 'WASPADA';
                                break;
                            case 'siaga': echo 'SIAGA';
                                break;
                            case 'darurat': echo 'DARURAT';
                                break;
                            default: echo strtoupper($laphar['situasi_umum'] ?: '-');
                        }
                        ?>
                    </td>
                    <td class="label">Cuaca</td>
                    <td>
                        <?php
                        switch ($laphar['cuaca']) {
                            case 'cerah': echo 'CERAH';
                                break;
                            case 'berawan': echo 'BERAWAN';
                                break;
                            case 'hujan': echo 'HUJAN';
                                break;
                            case 'mendung': echo 'MENDUNG';
                                break;
                            default: echo strtoupper($laphar['cuaca'] ?: '-');
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Notes & Recommendations -->
        <div class="section">
            <h3>CATATAN & REKOMENDASI</h3>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($laphar['catatan_rekomendasi'] ?: 'Tidak ada catatan atau rekomendasi')); ?>
            </div>
        </div>

        <!-- Footer Information -->
        <div class="section">
            <table class="info-table">
                <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td><?php echo htmlspecialchars($laphar['created_by_name'] ?: '-'); ?></td>
                    <td class="label">Tanggal Dibuat</td>
                    <td><?php echo date('d F Y H:i', strtotime($laphar['created_at'])); ?></td>
                </tr>
            </table>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <?php echo htmlspecialchars($laphar['kanit_nama'] ?: $laphar['created_by_name'] ?: 'Kanit/Kapolsek'); ?>
                </div>
                <small>Kanit/Kapolsek</small>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <?php echo htmlspecialchars($laphar['created_by_name'] ?: 'Pelapor'); ?>
                </div>
                <small>Pelapor</small>
            </div>
        </div>

        <!-- Print Instructions -->
        <div style="margin-top: 30px; font-size: 10px; color: #666; text-align: center; border-top: 1px solid #ccc; padding-top: 10px;">
            <p><strong>Petunjuk:</strong> Simpan halaman ini sebagai PDF menggunakan fitur "Print" → "Save as PDF" di browser Anda</p>
            <p>Dokumen ini dibuat secara otomatis oleh Sistem BAGOPS Polres Samosir pada <?php echo date('d F Y H:i:s'); ?></p>
        </div>

    </body>
    </html>
    <?php

} catch (Exception $e) {
    error_log("Export Laphar PDF error: " . $e->getMessage());
    die('Terjadi kesalahan saat membuat PDF Laphar');
}
?>
