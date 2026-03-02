<?php
/**
 * Operations Management Class for BAGOPS
 * Full CRUD implementation for police operations
 */

class OperationsManager {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    /**
     * Create new operation
     */
    public function createOperation($data) {
        try {
            // Generate operation code
            $kode_operasi = $this->generateOperationCode();
            
            $stmt = $this->db->prepare("
                INSERT INTO operations (
                    kode_operasi, nama_operasi, deskripsi, jenis_operasi, tingkat_operasi,
                    tanggal_mulai, tanggal_selesai, lokasi_utama, lokasi_detail,
                    wilayah_hukum, status, prioritas, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $kode_operasi,
                $data['nama_operasi'],
                $data['deskripsi'] ?? '',
                $data['jenis_operasi'],
                $data['tingkat_operasi'],
                $data['tanggal_mulai'],
                $data['tanggal_selesai'] ?? null,
                $data['lokasi_utama'],
                $data['lokasi_detail'] ?? '',
                $data['wilayah_hukum'] ?? '',
                $data['status'] ?? 'perencanaan',
                $data['prioritas'] ?? 'sedang',
                $this->auth->getCurrentUser()['id']
            ]);
            
            $operation_id = $this->db->lastInsertId();
            
            // Log activity
            $this->logOperation($operation_id, 'dibuat', 'Operasi baru dibuat: ' . $data['nama_operasi']);
            
            return [
                'success' => true,
                'operation_id' => $operation_id,
                'kode_operasi' => $kode_operasi,
                'message' => 'Operasi berhasil dibuat'
            ];
            
        } catch (Exception $e) {
            error_log("Create Operation Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal membuat operasi: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Get all operations with filtering
     */
    public function getOperations($filters = []) {
        try {
            $where_conditions = ["1=1"];
            $params = [];
            
            // Build WHERE clause
            if (!empty($filters['status'])) {
                $where_conditions[] = "o.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['jenis_operasi'])) {
                $where_conditions[] = "o.jenis_operasi = ?";
                $params[] = $filters['jenis_operasi'];
            }
            
            if (!empty($filters['tanggal_mulai'])) {
                $where_conditions[] = "o.tanggal_mulai >= ?";
                $params[] = $filters['tanggal_mulai'];
            }
            
            if (!empty($filters['tanggal_selesai'])) {
                $where_conditions[] = "o.tanggal_selesai <= ?";
                $params[] = $filters['tanggal_selesai'];
            }
            
            $where_clause = implode(" AND ", $where_conditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    o.*,
                    creator.username as created_by_name,
                    approver.username as approved_by_name,
                    COUNT(DISTINCT oa.personel_id) as personnel_count,
                    COUNT(DISTINCT or_.id) as resource_count,
                    COUNT(DISTINCT od.id) as document_count
                FROM operations o
                LEFT JOIN users creator ON o.created_by = creator.id
                LEFT JOIN users approver ON o.approved_by = approver.id
                LEFT JOIN operation_assignments oa ON o.id = oa.operation_id
                LEFT JOIN operation_resources or_ ON o.id = or_.operation_id
                LEFT JOIN operation_documents od ON o.id = od.operation_id
                WHERE {$where_clause}
                GROUP BY o.id
                ORDER BY o.created_at DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Operations Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single operation by ID
     */
    public function getOperation($id) {
        try {
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
            
            $stmt->execute([$id]);
            $operation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($operation) {
                // Get related data
                $operation['assignments'] = $this->getOperationAssignments($id);
                $operation['resources'] = $this->getOperationResources($id);
                $operation['documents'] = $this->getOperationDocuments($id);
                $operation['objectives'] = $this->getOperationObjectives($id);
                $operation['logs'] = $this->getOperationLogs($id);
            }
            
            return $operation;
            
        } catch (Exception $e) {
            error_log("Get Operation Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update operation
     */
    public function updateOperation($id, $data) {
        try {
            $fields = [];
            $params = [];
            
            // Build update fields
            $updatable_fields = [
                'nama_operasi', 'deskripsi', 'jenis_operasi', 'tingkat_operasi',
                'tanggal_mulai', 'tanggal_selesai', 'lokasi_utama', 'lokasi_detail',
                'wilayah_hukum', 'status', 'prioritas'
            ];
            
            foreach ($updatable_fields as $field) {
                if (isset($data[$field])) {
                    $fields[] = "{$field} = ?";
                    $params[] = $data[$field];
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'Tidak ada data yang diupdate'];
            }
            
            $fields[] = "updated_at = CURRENT_TIMESTAMP";
            $params[] = $id;
            
            $sql = "UPDATE operations SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Log activity
            $this->logOperation($id, 'diupdate', 'Operasi diupdate');
            
            return ['success' => true, 'message' => 'Operasi berhasil diupdate'];
            
        } catch (Exception $e) {
            error_log("Update Operation Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update operasi: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete operation
     */
    public function deleteOperation($id) {
        try {
            // Check if operation can be deleted (not active)
            $stmt = $this->db->prepare("SELECT status FROM operations WHERE id = ?");
            $stmt->execute([$id]);
            $operation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$operation) {
                return ['success' => false, 'message' => 'Operasi tidak ditemukan'];
            }
            
            if ($operation['status'] === 'aktif') {
                return ['success' => false, 'message' => 'Operasi aktif tidak dapat dihapus'];
            }
            
            $stmt = $this->db->prepare("DELETE FROM operations WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Operasi berhasil dihapus'];
            
        } catch (Exception $e) {
            error_log("Delete Operation Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal hapus operasi: ' . $e->getMessage()];
        }
    }
    
    /**
     * Assign personnel to operation
     */
    public function assignPersonnel($operation_id, $personnel_data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO operation_assignments (
                    operation_id, personel_id, role_assignment, tugas_khusus,
                    tanggal_assign, jam_mulai, jam_selesai, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                role_assignment = VALUES(role_assignment),
                tugas_khusus = VALUES(tugas_khusus),
                jam_mulai = VALUES(jam_mulai),
                jam_selesai = VALUES(jam_selesai),
                updated_at = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([
                $operation_id,
                $personnel_data['personel_id'],
                $personnel_data['role_assignment'],
                $personnel_data['tugas_khusus'] ?? '',
                $personnel_data['tanggal_assign'] ?? date('Y-m-d'),
                $personnel_data['jam_mulai'] ?? '08:00',
                $personnel_data['jam_selesai'] ?? '16:00',
                $this->auth->getCurrentUser()['id']
            ]);
            
            // Log activity
            $this->logOperation($operation_id, 'personel_ditambah', 
                'Personel ditambahkan: ' . $personnel_data['role_assignment']);
            
            return ['success' => true, 'message' => 'Personel berhasil ditugaskan'];
            
        } catch (Exception $e) {
            error_log("Assign Personnel Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal menugaskan personel: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get operation assignments
     */
    private function getOperationAssignments($operation_id) {
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get operation resources
     */
    private function getOperationResources($operation_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM operation_resources 
            WHERE operation_id = ? 
            ORDER BY jenis_resource, nama_resource
        ");
        
        $stmt->execute([$operation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get operation documents
     */
    private function getOperationDocuments($operation_id) {
        $stmt = $this->db->prepare("
            SELECT 
                od.*,
                u.username as uploaded_by_name
            FROM operation_documents od
            LEFT JOIN users u ON od.uploaded_by = u.id
            WHERE od.operation_id = ?
            ORDER BY od.uploaded_at DESC
        ");
        
        $stmt->execute([$operation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get operation objectives
     */
    private function getOperationObjectives($operation_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM operation_objectives 
            WHERE operation_id = ? 
            ORDER BY created_at
        ");
        
        $stmt->execute([$operation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get operation logs
     */
    private function getOperationLogs($operation_id) {
        $stmt = $this->db->prepare("
            SELECT 
                ol.*,
                u.username as created_by_name
            FROM operation_logs ol
            LEFT JOIN users u ON ol.created_by = u.id
            WHERE ol.operation_id = ?
            ORDER BY ol.created_at DESC
            LIMIT 50
        ");
        
        $stmt->execute([$operation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Log operation activity
     */
    private function logOperation($operation_id, $jenis_log, $deskripsi) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO operation_logs (operation_id, jenis_log, deskripsi, created_by)
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $operation_id,
                $jenis_log,
                $deskripsi,
                $this->auth->getCurrentUser()['id']
            ]);
        } catch (Exception $e) {
            error_log("Log Operation Error: " . $e->getMessage());
        }
    }
    
    /**
     * Generate operation code
     */
    private function generateOperationCode() {
        $year = date('Y');
        $prefix = "OPS-{$year}-";
        
        // Get last operation code for this year
        $stmt = $this->db->prepare("
            SELECT MAX(CAST(SUBSTRING(kode_operasi, 12) AS UNSIGNED)) as last_num
            FROM operations 
            WHERE kode_operasi LIKE ?
        ");
        
        $stmt->execute(["{$prefix}%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $next_num = ($result['last_num'] ?? 0) + 1;
        return $prefix . str_pad($next_num, 3, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get operation statistics
     */
    public function getOperationStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_operations,
                    SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as active_operations,
                    SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as completed_operations,
                    SUM(CASE WHEN status = 'perencanaan' THEN 1 ELSE 0 END) as planning_operations,
                    SUM(CASE WHEN tanggal_mulai <= CURDATE() AND (tanggal_selesai >= CURDATE() OR tanggal_selesai IS NULL) THEN 1 ELSE 0 END) as ongoing_operations
                FROM operations
            ");
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Stats Error: " . $e->getMessage());
            return [
                'total_operations' => 0,
                'active_operations' => 0,
                'completed_operations' => 0,
                'planning_operations' => 0,
                'ongoing_operations' => 0
            ];
        }
    }
}
?>
