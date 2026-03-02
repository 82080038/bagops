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
    die('Invalid AAR ID');
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
        die('AAR not found');
    }

    // Check if user can access this AAR
    if ($aar['created_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        die('Access denied');
    }

    // Set headers for PDF download
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="AAR_' . $aar['kode_aar'] . '_' . date('Y-m-d') . '.html"');

    // Generate HTML content that can be saved as PDF
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>AAR - <?php echo htmlspecialchars($aar['kode_aar'] ?: $aar['nomor_aar'] ?: 'AAR-' . $aar['id']); ?></title>
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
                min-height: 60px;
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
            .status-badge {
                display: inline-block;
                padding: 3px 8px;
                border: 1px solid #000;
                font-size: 10px;
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body>

        <!-- Header -->
        <div class="header">
            <h1>KESATUAN REPUBLIK INDONESIA</h1>
            <h1>POLISI DAERAH SUMATERA UTARA</h1>
            <h2>RESOR SAMOSIR</h2>
            <h2>AFTER ACTION REVIEW (AAR)</h2>
        </div>

        <!-- AAR Information -->
        <div class="section">
            <h3>INFORMASI AAR</h3>
            <table class="info-table">
                <tr>
                    <td class="label">Kode AAR</td>
                    <td><?php echo htmlspecialchars($aar['kode_aar'] ?: '-'); ?></td>
                    <td class="label">Nomor AAR</td>
                    <td><?php echo htmlspecialchars($aar['nomor_aar'] ?: '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal AAR</td>
                    <td><?php echo $aar['tanggal_aar'] ? date('d F Y', strtotime($aar['tanggal_aar'])) : date('d F Y', strtotime($aar['created_at'])); ?></td>
                    <td class="label">Waktu Pelaksanaan</td>
                    <td><?php echo $aar['waktu_pelaksanaan'] ? date('d/m/Y H:i', strtotime($aar['waktu_pelaksanaan'])) : '-'; ?></td>
                </tr>
                <tr>
                    <td class="label">Operasi</td>
                    <td><?php echo htmlspecialchars($aar['operation_title'] ?: '-'); ?></td>
                    <td class="label">Event</td>
                    <td><?php echo htmlspecialchars($aar['event_title'] ?: '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Lokasi Pelaksanaan</td>
                    <td colspan="3"><?php echo htmlspecialchars($aar['lokasi_pelaksanaan'] ?: '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Status</td>
                    <td colspan="3">
                        <span class="status-badge">
                            <?php
                            switch ($aar['status']) {
                                case 'DRAFT': echo 'DRAFT';
                                    break;
                                case 'SUBMITTED': echo 'DIJUKAN UNTUK REVIEW';
                                    break;
                                case 'APPROVED': echo 'DISETUJUI';
                                    break;
                                case 'REJECTED': echo 'DITOLAK';
                                    break;
                                default: echo 'UNKNOWN';
                            }
                            ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Ringkasan Eksekusi -->
        <div class="section">
            <h3>1. RINGKASAN EKSEKUSI</h3>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['ringkas_eksekusi'] ?: 'Tidak ada ringkasan eksekusi')); ?>
            </div>
        </div>

        <!-- Evaluasi -->
        <div class="section">
            <h3>2. EVALUASI</h3>

            <h4>Yang Berjalan Baik:</h4>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['yang_berjalan_baik'] ?: 'Tidak ada catatan')); ?>
            </div>

            <h4>Yang Perlu Diperbaiki:</h4>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['yang_perlu_diperbaiki'] ?: 'Tidak ada catatan')); ?>
            </div>

            <h4>Hambatan & Kendala:</h4>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['hambatan_kendala'] ?: 'Tidak ada catatan')); ?>
            </div>

            <h4>Insiden & Pelanggaran:</h4>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['insiden_pelanggaran'] ?: 'Tidak ada catatan')); ?>
            </div>
        </div>

        <!-- Data Metrik -->
        <div class="section">
            <h3>3. DATA METRIK</h3>
            <?php
            $dataMetrik = $aar['data_metrik'];
            if ($dataMetrik) {
                if (is_string($dataMetrik)) {
                    $dataMetrik = json_decode($dataMetrik, true);
                }
                if (is_array($dataMetrik)):
            ?>
            <table class="info-table">
                <tr>
                    <td class="label">Response Time</td>
                    <td><?php echo htmlspecialchars($dataMetrik['response_time'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="label">Jumlah Personel</td>
                    <td><?php echo htmlspecialchars($dataMetrik['personel_count'] ?? '0'); ?> orang</td>
                </tr>
                <tr>
                    <td class="label">Resource Utilization</td>
                    <td><?php echo htmlspecialchars($dataMetrik['resource_utilization'] ?? '0'); ?>%</td>
                </tr>
            </table>
            <?php else: ?>
            <div class="content-box">Tidak ada data metrik tersedia</div>
            <?php endif; } else { ?>
            <div class="content-box">Tidak ada data metrik tersedia</div>
            <?php } ?>
        </div>

        <!-- Rekomendasi & Tindak Lanjut -->
        <div class="section">
            <h3>4. REKOMENDASI & TINDAK LANJUT</h3>
            <div class="content-box">
                <?php echo nl2br(htmlspecialchars($aar['rekomendasi_tindak_lanjut'] ?: 'Tidak ada rekomendasi')); ?>
            </div>
        </div>

        <!-- Perubahan SOP -->
        <div class="section">
            <h3>5. PERUBAHAN SOP</h3>
            <?php
            $perubahanSop = $aar['perubahan_sop'];
            if ($perubahanSop) {
                if (is_string($perubahanSop)) {
                    $perubahanSop = json_decode($perubahanSop, true);
                }
                if (is_array($perubahanSop)):
            ?>
            <table class="info-table">
                <tr>
                    <td class="label">Perubahan yang Diperlukan</td>
                    <td><?php echo nl2br(htmlspecialchars(is_array($perubahanSop['perubahan']) ? implode("\n", $perubahanSop['perubahan']) : ($perubahanSop['perubahan'] ?: '-'))); ?></td>
                </tr>
                <tr>
                    <td class="label">Alasan Perubahan</td>
                    <td><?php echo nl2br(htmlspecialchars($perubahanSop['alasan'] ?: '-')); ?></td>
                </tr>
            </table>
            <?php else: ?>
            <div class="content-box">Tidak ada perubahan SOP yang diperlukan</div>
            <?php endif; } else { ?>
            <div class="content-box">Tidak ada perubahan SOP yang diperlukan</div>
            <?php } ?>
        </div>

        <!-- Footer Information -->
        <div class="section">
            <table class="info-table">
                <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td><?php echo htmlspecialchars($aar['created_by_name'] ?: '-'); ?></td>
                    <td class="label">Tanggal Dibuat</td>
                    <td><?php echo date('d F Y H:i', strtotime($aar['created_at'])); ?></td>
                </tr>
                <?php if ($aar['approved_by_name']): ?>
                <tr>
                    <td class="label">Disetujui Oleh</td>
                    <td><?php echo htmlspecialchars($aar['approved_by_name']); ?></td>
                    <td class="label">Tanggal Persetujuan</td>
                    <td><?php echo $aar['updated_at'] !== $aar['created_at'] ? date('d F Y H:i', strtotime($aar['updated_at'])) : '-'; ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    <?php echo htmlspecialchars($aar['created_by_name'] ?: 'Pelapor'); ?>
                </div>
                <small>Dibuat pada: <?php echo date('d F Y', strtotime($aar['created_at'])); ?></small>
            </div>
            <?php if ($aar['approved_by_name']): ?>
            <div class="signature-box">
                <div class="signature-line">
                    <?php echo htmlspecialchars($aar['approved_by_name']); ?>
                </div>
                <small>Disetujui pada: <?php echo $aar['updated_at'] !== $aar['created_at'] ? date('d F Y', strtotime($aar['updated_at'])) : '-'; ?></small>
            </div>
            <?php endif; ?>
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
    error_log("Export AAR PDF error: " . $e->getMessage());
    die('Terjadi kesalahan saat membuat PDF AAR');
}
?>
