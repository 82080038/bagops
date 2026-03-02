<?php
/**
 * Report Generator Class for BAGOPS
 * PDF generation and reporting system
 */

class ReportGenerator {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    /**
     * Generate operation report
     */
    public function generateOperationReport($operation_id, $format = 'pdf') {
        try {
            $operation = $this->getOperationDetails($operation_id);
            
            if (!$operation) {
                return ['success' => false, 'message' => 'Operation not found'];
            }
            
            switch ($format) {
                case 'pdf':
                    return $this->generateOperationPDF($operation);
                case 'excel':
                    return $this->generateOperationExcel($operation);
                default:
                    return ['success' => false, 'message' => 'Unsupported format'];
            }
            
        } catch (Exception $e) {
            error_log("Generate Report Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate personnel report
     */
    public function generatePersonnelReport($filters = [], $format = 'pdf') {
        try {
            $personnel = $this->getPersonnelData($filters);
            
            switch ($format) {
                case 'pdf':
                    return $this->generatePersonnelPDF($personnel, $filters);
                case 'excel':
                    return $this->generatePersonnelExcel($personnel, $filters);
                default:
                    return ['success' => false, 'message' => 'Unsupported format'];
            }
            
        } catch (Exception $e) {
            error_log("Generate Personnel Report Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()];
        }
    }
    
    /**
     * Generate monthly report
     */
    public function generateMonthlyReport($month, $year, $format = 'pdf') {
        try {
            $data = $this->getMonthlyData($month, $year);
            
            switch ($format) {
                case 'pdf':
                    return $this->generateMonthlyPDF($data, $month, $year);
                case 'excel':
                    return $this->generateMonthlyExcel($data, $month, $year);
                default:
                    return ['success' => false, 'message' => 'Unsupported format'];
            }
            
        } catch (Exception $e) {
            error_log("Generate Monthly Report Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get operation details for reporting
     */
    private function getOperationDetails($operation_id) {
        $stmt = $this->db->prepare("
            SELECT 
                o.*,
                creator.username as created_by_name,
                approver.username as approved_by_name
            FROM operations o
            LEFT JOIN users creator ON o.created_by = creator.id
            LEFT JOIN users approver ON o.approved_by = approver.id
            WHERE o.id = ?
        ");
        
        $stmt->execute([$operation_id]);
        $operation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($operation) {
            // Get assignments
            $stmt = $this->db->prepare("
                SELECT 
                    oa.*,
                    p.nrp, p.nama, p.pangkat, p.jabatan
                FROM operation_assignments oa
                JOIN personel p ON oa.personel_id = p.id
                WHERE oa.operation_id = ?
                ORDER BY oa.role_assignment, p.nama
            ");
            $stmt->execute([$operation_id]);
            $operation['assignments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get resources
            $stmt = $this->db->prepare("
                SELECT * FROM operation_resources 
                WHERE operation_id = ? 
                ORDER BY jenis_resource, nama_resource
            ");
            $stmt->execute([$operation_id]);
            $operation['resources'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get objectives
            $stmt = $this->db->prepare("
                SELECT * FROM operation_objectives 
                WHERE operation_id = ? 
                ORDER BY created_at
            ");
            $stmt->execute([$operation_id]);
            $operation['objectives'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $operation;
    }
    
    /**
     * Get personnel data for reporting
     */
    private function getPersonnelData($filters) {
        $where_conditions = ["1=1"];
        $params = [];
        
        if (!empty($filters['pangkat'])) {
            $where_conditions[] = "p.pangkat = ?";
            $params[] = $filters['pangkat'];
        }
        
        if (!empty($filters['jabatan'])) {
            $where_conditions[] = "p.jabatan LIKE ?";
            $params[] = '%' . $filters['jabatan'] . '%';
        }
        
        if (!empty($filters['unit'])) {
            $where_conditions[] = "p.unit LIKE ?";
            $params[] = '%' . $filters['unit'] . '%';
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        $stmt = $this->db->prepare("
            SELECT 
                p.*,
                COUNT(oa.id) as operation_count
            FROM personel p
            LEFT JOIN operation_assignments oa ON p.id = oa.personel_id
            WHERE {$where_clause}
            GROUP BY p.id
            ORDER BY p.nama
        ");
        
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get monthly data for reporting
     */
    private function getMonthlyData($month, $year) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_operations,
                SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as completed_operations,
                SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as active_operations,
                COUNT(DISTINCT oa.personel_id) as personnel_involved,
                COUNT(DISTINCT o.id) as unique_operations
            FROM operations o
            LEFT JOIN operation_assignments oa ON o.id = oa.operation_id
            WHERE MONTH(o.tanggal_mulai) = ? AND YEAR(o.tanggal_mulai) = ?
        ");
        
        $stmt->execute([$month, $year]);
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get operation details
        $stmt = $this->db->prepare("
            SELECT 
                o.kode_operasi,
                o.nama_operasi,
                o.jenis_operasi,
                o.status,
                o.tanggal_mulai,
                o.tanggal_selesai,
                COUNT(oa.personel_id) as personnel_count
            FROM operations o
            LEFT JOIN operation_assignments oa ON o.id = oa.operation_id
            WHERE MONTH(o.tanggal_mulai) = ? AND YEAR(o.tanggal_mulai) = ?
            GROUP BY o.id
            ORDER BY o.tanggal_mulai
        ");
        
        $stmt->execute([$month, $year]);
        $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'summary' => $summary,
            'operations' => $operations,
            'month' => $month,
            'year' => $year
        ];
    }
    
    /**
     * Generate operation PDF report
     */
    private function generateOperationPDF($operation) {
        // Create HTML content for PDF
        $html = $this->createOperationPDFHTML($operation);
        
        // Save to temporary file
        $filename = 'operation_report_' . $operation['kode_operasi'] . '_' . date('YmdHis') . '.pdf';
        $filepath = __DIR__ . '/../storage/reports/' . $filename;
        
        // Ensure reports directory exists
        if (!file_exists(__DIR__ . '/../storage/reports/')) {
            mkdir(__DIR__ . '/../storage/reports/', 0755, true);
        }
        
        // For now, save as HTML (would use TCPDF in production)
        file_put_contents($filepath . '.html', $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath . '.html',
            'message' => 'Report generated successfully (HTML format)'
        ];
    }
    
    /**
     * Create operation PDF HTML content
     */
    private function createOperationPDFHTML($operation) {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Operasi - ' . htmlspecialchars($operation['kode_operasi']) . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                .section { margin-bottom: 20px; }
                .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .status-aktif { color: green; font-weight: bold; }
                .status-selesai { color: blue; font-weight: bold; }
                .status-perencanaan { color: orange; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN OPERASI</h1>
                <h2>' . htmlspecialchars($operation['kode_operasi']) . '</h2>
                <h3>' . htmlspecialchars($operation['nama_operasi']) . '</h3>
                <p><strong>Dibuat:</strong> ' . date('d/m/Y H:i') . '</p>
            </div>
            
            <div class="section">
                <h3>Informasi Umum</h3>
                <table>
                    <tr><th>Kode Operasi</th><td>' . htmlspecialchars($operation['kode_operasi']) . '</td></tr>
                    <tr><th>Nama Operasi</th><td>' . htmlspecialchars($operation['nama_operasi']) . '</td></tr>
                    <tr><th>Jenis Operasi</th><td>' . ucfirst($operation['jenis_operasi']) . '</td></tr>
                    <tr><th>Tingkat</th><td>Tingkat ' . $operation['tingkat_operasi'] . '</td></tr>
                    <tr><th>Status</th><td class="status-' . $operation['status'] . '">' . ucfirst($operation['status']) . '</td></tr>
                    <tr><th>Tanggal Mulai</th><td>' . $operation['tanggal_mulai'] . '</td></tr>
                    <tr><th>Tanggal Selesai</th><td>' . ($operation['tanggal_selesai'] ?? 'Belum') . '</td></tr>
                    <tr><th>Lokasi Utama</th><td>' . htmlspecialchars($operation['lokasi_utama']) . '</td></tr>
                    <tr><th>Dibuat Oleh</th><td>' . htmlspecialchars($operation['created_by_name']) . '</td></tr>
                </table>
            </div>';
        
        if (!empty($operation['deskripsi'])) {
            $html .= '
            <div class="section">
                <h3>Deskripsi Operasi</h3>
                <p>' . htmlspecialchars($operation['deskripsi']) . '</p>
            </div>';
        }
        
        if (!empty($operation['assignments'])) {
            $html .= '
            <div class="section">
                <h3>Personel Ditugaskan (' . count($operation['assignments']) . ')</h3>
                <table>
                    <tr><th>NRP</th><th>Nama</th><th>Pangkat</th><th>Jabatan</th><th>Role Assignment</th></tr>';
            
            foreach ($operation['assignments'] as $assignment) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($assignment['nrp']) . '</td>
                    <td>' . htmlspecialchars($assignment['nama']) . '</td>
                    <td>' . htmlspecialchars($assignment['pangkat']) . '</td>
                    <td>' . htmlspecialchars($assignment['jabatan']) . '</td>
                    <td>' . htmlspecialchars($assignment['role_assignment']) . '</td>
                </tr>';
            }
            
            $html .= '
                </table>
            </div>';
        }
        
        if (!empty($operation['resources'])) {
            $html .= '
            <div class="section">
                <h3>Resources (' . count($operation['resources']) . ')</h3>
                <table>
                    <tr><th>Nama Resource</th><th>Jenis</th><th>Jumlah</th><th>Kondisi</th></tr>';
            
            foreach ($operation['resources'] as $resource) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($resource['nama_resource']) . '</td>
                    <td>' . htmlspecialchars($resource['jenis_resource']) . '</td>
                    <td>' . $resource['jumlah'] . ' ' . htmlspecialchars($resource['satuan']) . '</td>
                    <td>' . ucfirst($resource['kondisi']) . '</td>
                </tr>';
            }
            
            $html .= '
                </table>
            </div>';
        }
        
        if (!empty($operation['objectives'])) {
            $html .= '
            <div class="section">
                <h3>Target Operasi</h3>
                <table>
                    <tr><th>Objective</th><th>Target</th><th>Status</th></tr>';
            
            foreach ($operation['objectives'] as $objective) {
                $html .= '
                <tr>
                    <td>' . htmlspecialchars($objective['judul_objective']) . '</td>
                    <td>' . htmlspecialchars($objective['target_kuantitatif'] ?? '-') . '</td>
                    <td>' . ucfirst($objective['status_achievement']) . '</td>
                </tr>';
            }
            
            $html .= '
                </table>
            </div>';
        }
        
        $html .= '
            <div class="section">
                <p><em>Laporan ini dibuat secara otomatis oleh sistem BAGOPS POLRES SAMOSIR</em></p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Generate personnel PDF report
     */
    private function generatePersonnelPDF($personnel, $filters) {
        $html = $this->createPersonnelPDFHTML($personnel, $filters);
        
        $filename = 'personnel_report_' . date('YmdHis') . '.pdf';
        $filepath = __DIR__ . '/../storage/reports/' . $filename;
        
        if (!file_exists(__DIR__ . '/../storage/reports/')) {
            mkdir(__DIR__ . '/../storage/reports/', 0755, true);
        }
        
        file_put_contents($filepath . '.html', $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath . '.html',
            'message' => 'Personnel report generated successfully (HTML format)'
        ];
    }
    
    /**
     * Create personnel PDF HTML content
     */
    private function createPersonnelPDFHTML($personnel, $filters) {
        $filter_text = '';
        if (!empty($filters)) {
            $filter_parts = [];
            foreach ($filters as $key => $value) {
                if (!empty($value)) {
                    $filter_parts[] = ucfirst($key) . ': ' . $value;
                }
            }
            if (!empty($filter_parts)) {
                $filter_text = ' (Filter: ' . implode(', ', $filter_parts) . ')';
            }
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Personel' . $filter_text . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                .summary { background-color: #f2f2f2; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .active { color: green; font-weight: bold; }
                .inactive { color: red; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN DATA PERSONEL</h1>
                <h2>Polres Samosir' . $filter_text . '</h2>
                <p><strong>Dibuat:</strong> ' . date('d/m/Y H:i') . '</p>
            </div>
            
            <div class="summary">
                <h3>Ringkasan</h3>
                <p><strong>Total Personel:</strong> ' . count($personnel) . '</p>
                <p><strong>Aktif:</strong> ' . count(array_filter($personnel, fn($p) => $p['is_active'])) . '</p>
                <p><strong>Tidak Aktif:</strong> ' . count(array_filter($personnel, fn($p) => !$p['is_active'])) . '</p>
            </div>
            
            <div class="section">
                <h3>Data Personel</h3>
                <table>
                    <tr>
                        <th>No</th>
                        <th>NRP</th>
                        <th>Nama</th>
                        <th>Pangkat</th>
                        <th>Jabatan</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Operasi</th>
                    </tr>';
        
        $no = 1;
        foreach ($personnel as $person) {
            $status_class = $person['is_active'] ? 'active' : 'inactive';
            $status_text = $person['is_active'] ? 'Aktif' : 'Tidak Aktif';
            
            $html .= '
                <tr>
                    <td>' . $no++ . '</td>
                    <td>' . htmlspecialchars($person['nrp']) . '</td>
                    <td>' . htmlspecialchars($person['nama']) . '</td>
                    <td>' . htmlspecialchars($person['pangkat']) . '</td>
                    <td>' . htmlspecialchars($person['jabatan']) . '</td>
                    <td>' . htmlspecialchars($person['unit'] ?? '-') . '</td>
                    <td class="' . $status_class . '">' . $status_text . '</td>
                    <td>' . $person['operation_count'] . '</td>
                </tr>';
        }
        
        $html .= '
                </table>
            </div>
            
            <div class="section">
                <p><em>Laporan ini dibuat secara otomatis oleh sistem BAGOPS POLRES SAMOSIR</em></p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Generate monthly PDF report
     */
    private function generateMonthlyPDF($data, $month, $year) {
        $html = $this->createMonthlyPDFHTML($data, $month, $year);
        
        $filename = 'monthly_report_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.pdf';
        $filepath = __DIR__ . '/../storage/reports/' . $filename;
        
        if (!file_exists(__DIR__ . '/../storage/reports/')) {
            mkdir(__DIR__ . '/../storage/reports/', 0755, true);
        }
        
        file_put_contents($filepath . '.html', $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath . '.html',
            'message' => 'Monthly report generated successfully (HTML format)'
        ];
    }
    
    /**
     * Create monthly PDF HTML content
     */
    private function createMonthlyPDFHTML($data, $month, $year) {
        $month_names = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $month_name = $month_names[$month];
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Laporan Bulanan ' . $month_name . ' ' . $year . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                .summary { background-color: #f2f2f2; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .status-aktif { color: green; font-weight: bold; }
                .status-selesai { color: blue; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>LAPORAN BULANAN</h1>
                <h2>' . $month_name . ' ' . $year . '</h2>
                <h3>Polres Samosir</h3>
                <p><strong>Dibuat:</strong> ' . date('d/m/Y H:i') . '</p>
            </div>
            
            <div class="summary">
                <h3>Ringkasan Bulanan</h3>
                <table>
                    <tr><th>Total Operasi</th><td>' . $data['summary']['total_operations'] . '</td></tr>
                    <tr><th>Operasi Selesai</th><td>' . $data['summary']['completed_operations'] . '</td></tr>
                    <tr><th>Operasi Aktif</th><td>' . $data['summary']['active_operations'] . '</td></tr>
                    <tr><th>Personel Terlibat</th><td>' . $data['summary']['personnel_involved'] . '</td></tr>
                </table>
            </div>
            
            <div class="section">
                <h3>Detail Operasi</h3>
                <table>
                    <tr>
                        <th>Kode Operasi</th>
                        <th>Nama Operasi</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal Mulai</th>
                        <th>Personel</th>
                    </tr>';
        
        foreach ($data['operations'] as $operation) {
            $html .= '
                <tr>
                    <td>' . htmlspecialchars($operation['kode_operasi']) . '</td>
                    <td>' . htmlspecialchars($operation['nama_operasi']) . '</td>
                    <td>' . ucfirst($operation['jenis_operasi']) . '</td>
                    <td class="status-' . $operation['status'] . '">' . ucfirst($operation['status']) . '</td>
                    <td>' . $operation['tanggal_mulai'] . '</td>
                    <td>' . $operation['personnel_count'] . '</td>
                </tr>';
        }
        
        $html .= '
                </table>
            </div>
            
            <div class="section">
                <p><em>Laporan ini dibuat secara otomatis oleh sistem BAGOPS POLRES SAMOSIR</em></p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Get available reports
     */
    public function getAvailableReports() {
        try {
            $reports_dir = __DIR__ . '/../storage/reports/';
            $reports = [];
            
            if (file_exists($reports_dir)) {
                $files = scandir($reports_dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $filepath = $reports_dir . $file;
                        $reports[] = [
                            'filename' => $file,
                            'filepath' => $filepath,
                            'size' => filesize($filepath),
                            'created' => date('Y-m-d H:i:s', filemtime($filepath))
                        ];
                    }
                }
            }
            
            // Sort by creation date (newest first)
            usort($reports, function($a, $b) {
                return strtotime($b['created']) - strtotime($a['created']);
            });
            
            return $reports;
            
        } catch (Exception $e) {
            error_log("Get Available Reports Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete report
     */
    public function deleteReport($filename) {
        try {
            $filepath = __DIR__ . '/../storage/reports/' . $filename;
            
            if (file_exists($filepath)) {
                unlink($filepath);
                return ['success' => true, 'message' => 'Report deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Report not found'];
            }
            
        } catch (Exception $e) {
            error_log("Delete Report Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error deleting report: ' . $e->getMessage()];
        }
    }
}
?>
