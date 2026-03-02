<?php
/**
 * Document Management Class for BAGOPS
 * File upload, download, and management system
 */

class DocumentManager {
    private $db;
    private $auth;
    private $uploadPath;
    private $allowedTypes;
    private $maxFileSize;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
        $this->uploadPath = __DIR__ . '/../storage/documents/';
        $this->allowedTypes = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'jpg', 'jpeg', 'png', 'gif', 'bmp',
            'zip', 'rar', '7z', 'tar', 'gz'
        ];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        
        // Ensure upload directory exists
        if (!file_exists($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Upload document
     */
    public function uploadDocument($fileData, $metadata) {
        try {
            // Validate file
            $validation = $this->validateFile($fileData);
            if (!$validation['valid']) {
                return ['success' => false, 'message' => $validation['message']];
            }
            
            // Generate unique filename
            $filename = $this->generateUniqueFilename($fileData['name']);
            $filepath = $this->uploadPath . $filename;
            
            // Move uploaded file
            if (!move_uploaded_file($fileData['tmp_name'], $filepath)) {
                return ['success' => false, 'message' => 'Gagal mengupload file'];
            }
            
            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO documents (
                    judul_document, nama_file_asli, nama_file, tipe_file, ukuran_file,
                    path_file, kategori, deskripsi, access_level, uploaded_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $metadata['judul_document'],
                $fileData['name'],
                $filename,
                $fileData['type'],
                $fileData['size'],
                $filepath,
                $metadata['kategori'] ?? 'umum',
                $metadata['deskripsi'] ?? '',
                $metadata['access_level'] ?? 'internal',
                $this->auth->getCurrentUser()['id']
            ]);
            
            $document_id = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'document_id' => $document_id,
                'filename' => $filename,
                'message' => 'Document berhasil diupload'
            ];
            
        } catch (Exception $e) {
            error_log("Upload Document Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal upload: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get documents list
     */
    public function getDocuments($filters = []) {
        try {
            $where_conditions = ["1=1"];
            $params = [];
            
            // Build WHERE clause
            if (!empty($filters['kategori'])) {
                $where_conditions[] = "d.kategori = ?";
                $params[] = $filters['kategori'];
            }
            
            if (!empty($filters['access_level'])) {
                $where_conditions[] = "d.access_level = ?";
                $params[] = $filters['access_level'];
            }
            
            if (!empty($filters['search'])) {
                $where_conditions[] = "(d.judul_document LIKE ? OR d.deskripsi LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $where_clause = implode(" AND ", $where_conditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    d.*,
                    u.username as uploaded_by_name,
                    DATE_FORMAT(d.uploaded_at, '%d %b %Y %H:%i') as formatted_date
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE {$where_clause}
                ORDER BY d.uploaded_at DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Documents Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single document
     */
    public function getDocument($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    d.*,
                    u.username as uploaded_by_name
                FROM documents d
                LEFT JOIN users u ON d.uploaded_by = u.id
                WHERE d.id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Document Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Download document
     */
    public function downloadDocument($id) {
        try {
            $document = $this->getDocument($id);
            
            if (!$document) {
                return ['success' => false, 'message' => 'Document tidak ditemukan'];
            }
            
            if (!file_exists($document['path_file'])) {
                return ['success' => false, 'message' => 'File tidak ditemukan'];
            }
            
            // Check access permissions
            if (!$this->hasAccess($document)) {
                return ['success' => false, 'message' => 'Access denied'];
            }
            
            return [
                'success' => true,
                'document' => $document,
                'filepath' => $document['path_file']
            ];
            
        } catch (Exception $e) {
            error_log("Download Document Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal download: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete document
     */
    public function deleteDocument($id) {
        try {
            $document = $this->getDocument($id);
            
            if (!$document) {
                return ['success' => false, 'message' => 'Document tidak ditemukan'];
            }
            
            // Check permissions
            $currentUser = $this->auth->getCurrentUser();
            if ($document['uploaded_by'] != $currentUser['id'] && $currentUser['role'] !== 'super_admin') {
                return ['success' => false, 'message' => 'Tidak memiliki izin menghapus dokumen ini'];
            }
            
            // Delete file
            if (file_exists($document['path_file'])) {
                unlink($document['path_file']);
            }
            
            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM documents WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Document berhasil dihapus'];
            
        } catch (Exception $e) {
            error_log("Delete Document Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal hapus: ' . $e->getMessage()];
        }
    }
    
    /**
     * Update document metadata
     */
    public function updateDocument($id, $metadata) {
        try {
            $fields = [];
            $params = [];
            
            $updatable_fields = ['judul_document', 'kategori', 'deskripsi', 'access_level'];
            
            foreach ($updatable_fields as $field) {
                if (isset($metadata[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = $metadata[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'Tidak ada data yang diupdate'];
            }
            
            $params[] = $id;
            
            $sql = "UPDATE documents SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Document berhasil diupdate'];
            
        } catch (Exception $e) {
            error_log("Update Document Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile($file) {
        // Check if file was uploaded
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'message' => 'Invalid file upload'];
        }
        
        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['valid' => false, 'message' => 'File terlalu besar (max 10MB)'];
        }
        
        // Check file type
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExt, $this->allowedTypes)) {
            return ['valid' => false, 'message' => 'Tipe file tidak diizinkan'];
        }
        
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File terlalu besar (upload_max_filesize)',
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (MAX_FILE_SIZE)',
                UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                UPLOAD_ERR_EXTENSION => 'File upload dihentikan oleh extension',
            ];
            
            $errorMsg = $errorMessages[$file['error']] ?? 'Unknown upload error';
            return ['valid' => false, 'message' => $errorMsg];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Generate unique filename
     */
    private function generateUniqueFilename($originalName) {
        $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        
        // Clean filename
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $baseName = substr($baseName, 0, 50); // Limit length
        
        $timestamp = date('YmdHis');
        $random = substr(md5(uniqid()), 0, 8);
        
        return "{$timestamp}_{$random}_{$baseName}.{$fileExt}";
    }
    
    /**
     * Check access permissions
     */
    private function hasAccess($document) {
        $currentUser = $this->auth->getCurrentUser();
        $userRole = $currentUser['role'];
        
        // Super admin has access to everything
        if ($userRole === 'super_admin') {
            return true;
        }
        
        // User can access their own documents
        if ($document['uploaded_by'] == $currentUser['id']) {
            return true;
        }
        
        // Check access level
        $accessLevels = [
            'user' => ['public'],
            'kaur_ops' => ['public', 'internal'],
            'kabag_ops' => ['public', 'internal', 'confidential'],
            'admin' => ['public', 'internal', 'confidential', 'secret'],
            'super_admin' => ['public', 'internal', 'confidential', 'secret', 'top_secret']
        ];
        
        return in_array($document['access_level'], $accessLevels[$userRole] ?? []);
    }
    
    /**
     * Get document statistics
     */
    public function getDocumentStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_documents,
                    SUM(ukuran_file) as total_size,
                    COUNT(CASE WHEN kategori = 'surat_perintah' THEN 1 END) as surat_perintah,
                    COUNT(CASE WHEN kategori = 'laporan' THEN 1 END) as laporan,
                    COUNT(CASE WHEN kategori = 'dokumentasi' THEN 1 END) as dokumentasi,
                    COUNT(CASE WHEN uploaded_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as this_week
                FROM documents
            ");
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Document Stats Error: " . $e->getMessage());
            return [
                'total_documents' => 0,
                'total_size' => 0,
                'surat_perintah' => 0,
                'laporan' => 0,
                'dokumentasi' => 0,
                'this_week' => 0
            ];
        }
    }
    
    /**
     * Format file size
     */
    public function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    /**
     * Get file icon based on type
     */
    public function getFileIcon($filename, $mimeType) {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $iconMap = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'ppt' => 'fas fa-file-powerpoint text-warning',
            'pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg' => 'fas fa-file-image text-info',
            'jpeg' => 'fas fa-file-image text-info',
            'png' => 'fas fa-file-image text-info',
            'gif' => 'fas fa-file-image text-info',
            'zip' => 'fas fa-file-archive text-secondary',
            'rar' => 'fas fa-file-archive text-secondary',
            '7z' => 'fas fa-file-archive text-secondary',
            'tar' => 'fas fa-file-archive text-secondary',
            'gz' => 'fas fa-file-archive text-secondary',
        ];
        
        return $iconMap[$ext] ?? 'fas fa-file text-muted';
    }
}
?>
