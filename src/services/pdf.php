<?php
// TCPDF dari paket sistem (php-tcpdf)
require_once '/usr/share/php/tcpdf/tcpdf.php';
require_once __DIR__ . '/../../config/config.php';

function generate_renops_pdf(array $event, array $renops, array $assignments = []): string
{
    $pdf = new TCPDF();
    $pdf->SetCreator('BAGOPS App');
    $pdf->SetAuthor('BAGOPS');
    $pdf->SetTitle('RENOPS - ' . ($event['title'] ?? '')); 
    $pdf->SetMargins(15, 20, 15);
    $pdf->AddPage();

    $html = '<h2 style="text-align:center;">RENCANA OPERASI (RENOPS)</h2>';
    $html .= '<h3>' . htmlspecialchars($event['title'] ?? '') . '</h3>';
    $html .= '<p><strong>Jenis:</strong> ' . htmlspecialchars($event['type'] ?? '') . '<br>';
    $html .= '<strong>Lokasi:</strong> ' . htmlspecialchars($event['location'] ?? '') . '<br>';
    $html .= '<strong>Waktu:</strong> ' . htmlspecialchars($event['start_at'] ?? '') . ' s/d ' . htmlspecialchars($event['end_at'] ?? '') . '<br>';
    $html .= '<strong>Risiko:</strong> ' . htmlspecialchars($event['risk_level'] ?? '') . '</p>';

    $html .= '<h4>Dasar Perintah</h4><p>' . nl2br(htmlspecialchars($renops['command_basis'] ?? '')) . '</p>';
    $html .= '<h4>Intel Singkat</h4><p>' . nl2br(htmlspecialchars($renops['intel_summary'] ?? '')) . '</p>';
    $html .= '<h4>Sasaran/Keluaran</h4><p>' . nl2br(htmlspecialchars($renops['objectives'] ?? '')) . '</p>';
    $html .= '<h4>Kekuatan/Peralatan</h4><p>' . nl2br(htmlspecialchars($renops['forces'] ?? '')) . '</p>';
    $html .= '<h4>Rencana Komunikasi</h4><p>' . nl2br(htmlspecialchars($renops['comms_plan'] ?? '')) . '</p>';
    $html .= '<h4>Rencana Kontinjensi</h4><p>' . nl2br(htmlspecialchars($renops['contingency_plan'] ?? '')) . '</p>';
    $html .= '<h4>Rencana Logistik</h4><p>' . nl2br(htmlspecialchars($renops['logistics_plan'] ?? '')) . '</p>';
    $html .= '<h4>Koordinasi Eksternal</h4><p>' . nl2br(htmlspecialchars($renops['coordination'] ?? '')) . '</p>';

    if (!empty($assignments)) {
        $html .= '<h4>Penugasan Personel</h4><table border="1" cellpadding="4"><tr><th>Nama</th><th>Pangkat</th><th>Jabatan</th><th>Peran/Sektor</th></tr>';
        foreach ($assignments as $a) {
            $html .= '<tr><td>' . htmlspecialchars($a['name'] ?? '') . '</td><td>' . htmlspecialchars($a['rank'] ?? '') . '</td><td>' . htmlspecialchars($a['position'] ?? '') . '</td><td>' . htmlspecialchars($a['role'] ?? '') . '</td></tr>';
        }
        $html .= '</table>';
    }

    $pdf->writeHTML($html, true, false, true, false, '');

    $filename = 'storage/lampiran/renops_' . time() . '.pdf';
    $pdf->Output(__DIR__ . '/../../' . $filename, 'F');

    return $filename;
}
