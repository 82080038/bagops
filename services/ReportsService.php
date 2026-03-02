<?php
/**
 * ReportsService - Business Logic Layer for Reports Management
 * BAGOPS POLRES SAMOSIR - Service Layer Implementation
 */

class ReportsService {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->auth = new Auth($this->db);
    }
    
    /**
     * Get all reports with filtering and pagination
     */
    public function getAllReports($filters = [], $page = 1, $limit = 25) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['search'])) {
                $whereClause .= " AND (r.isi_ringkas LIKE ? OR r.jenis_laporan LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['jenis_laporan'])) {
                $whereClause .= " AND r.jenis_laporan = ?";
                $params[] = $filters['jenis_laporan'];
            }
            
            if (!empty($filters['operation_id'])) {
                $whereClause .= " AND r.operation_id = ?";
                $params[] = $filters['operation_id'];
            }
            
            if (!empty($filters['date_from'])) {
                $whereClause .= " AND r.tanggal_laporan >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $whereClause .= " AND r.tanggal_laporan <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM reports r $whereClause";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // Get data with joins
            $sql = "SELECT r.*, u.nama as user_nama, o.nama_operasi 
                    FROM reports r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    LEFT JOIN operations o ON r.operation_id = o.id 
                    $whereClause 
                    ORDER BY r.tanggal_laporan DESC 
                    LIMIT ? OFFSET ?";
            
            $dataParams = array_merge($params, [$limit, $offset]);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($dataParams);
            
            return [
                'data' => $stmt->fetchAll(),
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ];
            
        } catch (Exception $e) {
            throw new Exception("Error getting reports data: " . $e->getMessage());
        }
    }
    
    /**
     * Get report by ID
     */
    public function getReportById($id) {
        try {
            $sql = "SELECT r.*, u.nama as user_nama, o.nama_operasi 
                    FROM reports r 
                    LEFT JOIN users u ON r.user_id = u.id 
                    LEFT JOIN operations o ON r.operation_id = o.id 
                    WHERE r.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                throw new Exception("Report not found");
            }
            
            return $result;
            
        } catch (Exception $e) {
            throw new Exception("Error getting report: " . $e->getMessage());
        }
    }
    
    /**
     * Create new report
     */
    public function createReport($data) {
        try {
            // Validate required fields
            $this->validateReportData($data);
            
            $sql = "INSERT INTO reports (tanggal_laporan, user_id, operation_id, jenis_laporan, isi_laporan, isi_ringkas, status, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['tanggal_laporan'] ?? date('Y-m-d'),
                $this->auth->getCurrentUserId(),
                $data['operation_id'] ?? null,
                $data['jenis_laporan'],
                $data['isi_laporan'],
                substr($data['isi_laporan'], 0, 200), // Ringkasan
                $data['status'] ?? 'draft'
            ]);
            
            if (!$result) {
                throw new Exception("Failed to create report");
            }
            
            $reportId = $this->db->lastInsertId();
            
            // Handle file upload if present
            if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === 0) {
                $this->handleFileUpload($reportId, $_FILES['file_lampiran']);
            }
            
            // Log activity
            $this->logActivity('REPORT_CREATED', "Created report: {$data['jenis_laporan']}");
            
            return $reportId;
            
        } catch (Exception $e) {
            throw new Exception("Error creating report: " . $e->getMessage());
        }
    }
    
    /**
     * Update report
     */
    public function updateReport($id, $data) {
        try {
            // Check if report exists
            $existing = $this->getReportById($id);
            
            // Validate required fields
            $this->validateReportData($data, true);
            
            $sql = "UPDATE reports SET 
                    tanggal_laporan = ?, 
                    operation_id = ?, 
                    jenis_laporan = ?, 
                    isi_laporan = ?, 
                    isi_ringkas = ?, 
                    status = ?, 
                    updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['tanggal_laporan'] ?? $existing['tanggal_laporan'],
                $data['operation_id'] ?? $existing['operation_id'],
                $data['jenis_laporan'] ?? $existing['jenis_laporan'],
                $data['isi_laporan'] ?? $existing['isi_laporan'],
                substr($data['isi_laporan'] ?? $existing['isi_laporan'], 0, 200),
                $data['status'] ?? $existing['status'],
                $id
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update report");
            }
            
            // Handle file upload if present
            if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === 0) {
                $this->handleFileUpload($id, $_FILES['file_lampiran']);
            }
            
            // Log activity
            $this->logActivity('REPORT_UPDATED', "Updated report: {$data['jenis_laporan']}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error updating report: " . $e->getMessage());
        }
    }
    
    /**
     * Delete report
     */
    public function deleteReport($id) {
        try {
            $report = $this->getReportById($id);
            
            // Delete associated documents first
            $this->deleteReportDocuments($id);
            
            $sql = "DELETE FROM reports WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Failed to delete report");
            }
            
            // Log activity
            $this->logActivity('REPORT_DELETED', "Deleted report: {$report['jenis_laporan']}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error deleting report: " . $e->getMessage());
        }
    }
    
    /**
     * Get report statistics
     */
    public function getReportStats() {
        try {
            $stats = [];
            
            // Total reports
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reports");
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // By jenis
            $stmt = $this->db->prepare("SELECT jenis_laporan, COUNT(*) as count FROM reports GROUP BY jenis_laporan ORDER BY count DESC");
            $stmt->execute();
            $stats['by_jenis'] = $stmt->fetchAll();
            
            // By status
            $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM reports GROUP BY status");
            $stmt->execute();
            $stats['by_status'] = $stmt->fetchAll();
            
            // This week
            $stmt = $this->db->prepare("SELECT COUNT(*) as this_week FROM reports WHERE YEARWEEK(tanggal_laporan) = YEARWEEK(CURRENT_DATE)");
            $stmt->execute();
            $stats['this_week'] = $stmt->fetch()['this_week'];
            
            // This month
            $stmt = $this->db->prepare("SELECT COUNT(*) as this_month FROM reports WHERE MONTH(tanggal_laporan) = MONTH(CURRENT_DATE) AND YEAR(tanggal_laporan) = YEAR(CURRENT_DATE)");
            $stmt->execute();
            $stats['this_month'] = $stmt->fetch()['this_month'];
            
            return $stats;
            
        } catch (Exception $e) {
            throw new Exception("Error getting report stats: " . $e->getMessage());
        }
    }
    
    /**
     * Export reports to Excel
     */
    public function exportReports($filters = []) {
        try {
            // Get all reports matching filters
            $reports = $this->getAllReports($filters, 1, 10000); // Large limit for export
            
            // Create CSV content
            $csv = "ID,Tanggal,User,Operasi,Jenis Laporan,Isi Ringkas,Status\n";
            
            foreach ($reports['data'] as $report) {
                $csv .= "{$report['id']},";
                $csv .= "{$report['tanggal_laporan']},";
                $csv .= "\"" . str_replace('"', '""', $report['user_nama']) . "\",";
                $csv .= "\"" . str_replace('"', '""', $report['nama_operasi']) . "\",";
                $csv .= "{$report['jenis_laporan']},";
                $csv .= "\"" . str_replace('"', '""', $report['isi_ringkas']) . "\",";
                $csv .= "{$report['status']}\n";
            }
            
            // Set headers for download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="reports_export_' . date('Y-m-d') . '.csv"');
            header('Content-Length: ' . strlen($csv));
            
            echo $csv;
            exit;
            
        } catch (Exception $e) {
            throw new Exception("Error exporting reports: " . $e->getMessage());
        }
    }
    
    /**
     * Validate report data
     */
    private function validateReportData($data, $isUpdate = false) {
        $required = ['jenis_laporan', 'isi_laporan'];
        
        foreach ($required as $field) {
            if (!$isUpdate && empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate jenis_laporan
        $validJenis = ['Harian', 'Mingguan', 'Bulanan', 'Akhir'];
        if (!empty($data['jenis_laporan']) && !in_array($data['jenis_laporan'], $validJenis)) {
            throw new Exception("Invalid report type");
        }
        
        // Validate status
        $validStatus = ['draft', 'submitted', 'approved', 'rejected'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatus)) {
            throw new Exception("Invalid status");
        }
        
        // Validate date format
        if (!empty($data['tanggal_laporan']) && !strtotime($data['tanggal_laporan'])) {
            throw new Exception("Invalid date format");
        }
        
        // Validate content length
        if (!empty($data['isi_laporan']) && strlen($data['isi_laporan']) < 10) {
            throw new Exception("Report content must be at least 10 characters");
        }
    }
    
    /**
     * Handle file upload
     */
    private function handleFileUpload($reportId, $file) {
        try {
            // Validate file
            $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            $maxSize = 10 * 1024 * 1024; // 10MB
            
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only PDF and Word documents are allowed.");
            }
            
            if ($file['size'] > $maxSize) {
                throw new Exception("File size too large. Maximum 10MB allowed.");
            }
            
            // Create unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'report_' . $reportId . '_' . time() . '.' . $extension;
            $uploadPath = '../storage/uploads/' . $filename;
            
            // Move file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception("Failed to upload file");
            }
            
            // Save to database
            $sql = "INSERT INTO documents (nama_dokumen, tipe_dokumen, kategori, file_path, file_size, mime_type, uploaded_by, operation_id, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $filename,
                $extension,
                'report_attachment',
                $uploadPath,
                $file['size'],
                $file['type'],
                $this->auth->getCurrentUserId(),
                null // Can be linked to operation if needed
            ]);
            
        } catch (Exception $e) {
            throw new Exception("Error uploading file: " . $e->getMessage());
        }
    }
    
    /**
     * Delete report documents
     */
    private function deleteReportDocuments($reportId) {
        try {
            // Get documents for this report
            $sql = "SELECT file_path FROM documents WHERE kategori = 'report_attachment' AND uploaded_by = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$this->auth->getCurrentUserId()]);
            $documents = $stmt->fetchAll();
            
            // Delete files
            foreach ($documents as $doc) {
                if (file_exists($doc['file_path'])) {
                    unlink($doc['file_path']);
                }
            }
            
        } catch (Exception $e) {
            // Log error but don't throw
            error_log("Error deleting documents: " . $e->getMessage());
        }
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $description) {
        try {
            $userId = $this->auth->getCurrentUserId();
            $stmt = $this->db->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([
                $userId,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
            ]);
        } catch (Exception $e) {
            // Log error but don't throw
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
?>
