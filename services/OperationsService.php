<?php
/**
 * OperationsService - Business Logic Layer for Operations Management
 * BAGOPS POLRES SAMOSIR - Service Layer Implementation
 */

class OperationsService {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->auth = new Auth($this->db);
    }
    
    /**
     * Get all operations with filtering and pagination
     */
    public function getAllOperations($filters = [], $page = 1, $limit = 25) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['search'])) {
                $whereClause .= " AND (o.nama_operasi LIKE ? OR o.lokasi LIKE ? OR o.deskripsi LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['jenis_operasi'])) {
                $whereClause .= " AND o.jenis_operasi = ?";
                $params[] = $filters['jenis_operasi'];
            }
            
            if (!empty($filters['status'])) {
                $whereClause .= " AND o.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $whereClause .= " AND o.tanggal_mulai >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $whereClause .= " AND o.tanggal_selesai <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM operations o $whereClause";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // Get data with joins
            $sql = "SELECT o.*, u.nama as created_by_name 
                    FROM operations o 
                    LEFT JOIN users u ON o.created_by = u.id 
                    $whereClause 
                    ORDER BY o.created_at DESC 
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
            throw new Exception("Error getting operations data: " . $e->getMessage());
        }
    }
    
    /**
     * Get operation by ID
     */
    public function getOperationById($id) {
        try {
            $sql = "SELECT o.*, u.nama as created_by_name 
                    FROM operations o 
                    LEFT JOIN users u ON o.created_by = u.id 
                    WHERE o.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                throw new Exception("Operation not found");
            }
            
            // Get assignments for this operation
            $assignmentsSql = "SELECT a.*, p.nama as personel_nama, p.nrp, p.pangkat 
                              FROM assignments a 
                              LEFT JOIN personel p ON a.personel_id = p.id 
                              WHERE a.operation_id = ?";
            $assignmentsStmt = $this->db->prepare($assignmentsSql);
            $assignmentsStmt->execute([$id]);
            $result['assignments'] = $assignmentsStmt->fetchAll();
            
            return $result;
            
        } catch (Exception $e) {
            throw new Exception("Error getting operation: " . $e->getMessage());
        }
    }
    
    /**
     * Create new operation
     */
    public function createOperation($data) {
        try {
            // Validate required fields
            $this->validateOperationData($data);
            
            // Validate dates
            if (isset($data['tanggal_selesai']) && $data['tanggal_selesai'] < $data['tanggal_mulai']) {
                throw new Exception("End date cannot be before start date");
            }
            
            $sql = "INSERT INTO operations (nama_operasi, jenis_operasi, status, tanggal_mulai, tanggal_selesai, lokasi, deskripsi, created_by, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['nama_operasi'],
                $data['jenis_operasi'],
                $data['status'] ?? 'planning',
                $data['tanggal_mulai'],
                $data['tanggal_selesai'] ?? null,
                $data['lokasi'],
                $data['deskripsi'] ?? '',
                $this->auth->getCurrentUserId()
            ]);
            
            if (!$result) {
                throw new Exception("Failed to create operation");
            }
            
            $operationId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('OPERATION_CREATED', "Created operation: {$data['nama_operasi']}");
            
            return $operationId;
            
        } catch (Exception $e) {
            throw new Exception("Error creating operation: " . $e->getMessage());
        }
    }
    
    /**
     * Update operation
     */
    public function updateOperation($id, $data) {
        try {
            // Check if operation exists
            $existing = $this->getOperationById($id);
            
            // Validate required fields
            $this->validateOperationData($data, true);
            
            // Validate dates
            $startDate = $data['tanggal_mulai'] ?? $existing['tanggal_mulai'];
            $endDate = $data['tanggal_selesai'] ?? $existing['tanggal_selesai'];
            
            if ($endDate && $endDate < $startDate) {
                throw new Exception("End date cannot be before start date");
            }
            
            $sql = "UPDATE operations SET 
                    nama_operasi = ?, 
                    jenis_operasi = ?, 
                    status = ?, 
                    tanggal_mulai = ?, 
                    tanggal_selesai = ?, 
                    lokasi = ?, 
                    deskripsi = ?, 
                    updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['nama_operasi'] ?? $existing['nama_operasi'],
                $data['jenis_operasi'] ?? $existing['jenis_operasi'],
                $data['status'] ?? $existing['status'],
                $startDate,
                $endDate,
                $data['lokasi'] ?? $existing['lokasi'],
                $data['deskripsi'] ?? $existing['deskripsi'],
                $id
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update operation");
            }
            
            // Log activity
            $this->logActivity('OPERATION_UPDATED', "Updated operation: {$data['nama_operasi']}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error updating operation: " . $e->getMessage());
        }
    }
    
    /**
     * Delete operation
     */
    public function deleteOperation($id) {
        try {
            $operation = $this->getOperationById($id);
            
            // Check if operation has assignments
            if ($this->hasAssignments($id)) {
                throw new Exception("Cannot delete operation with active assignments");
            }
            
            $sql = "DELETE FROM operations WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Failed to delete operation");
            }
            
            // Log activity
            $this->logActivity('OPERATION_DELETED', "Deleted operation: {$operation['nama_operasi']}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error deleting operation: " . $e->getMessage());
        }
    }
    
    /**
     * Assign personel to operation
     */
    public function assignPersonel($operationId, $personelId, $roleAssignment) {
        try {
            // Check if operation exists
            $this->getOperationById($operationId);
            
            // Check if personel exists
            $personelSql = "SELECT id, nama FROM personel WHERE id = ?";
            $personelStmt = $this->db->prepare($personelSql);
            $personelStmt->execute([$personelId]);
            $personel = $personelStmt->fetch();
            
            if (!$personel) {
                throw new Exception("Personel not found");
            }
            
            // Check if already assigned
            $checkSql = "SELECT id FROM assignments WHERE operation_id = ? AND personel_id = ?";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$operationId, $personelId]);
            
            if ($checkStmt->fetch()) {
                throw new Exception("Personel already assigned to this operation");
            }
            
            $sql = "INSERT INTO assignments (operation_id, personel_id, role_assignment, status, assigned_at) 
                    VALUES (?, ?, ?, 'assigned', NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$operationId, $personelId, $roleAssignment]);
            
            if (!$result) {
                throw new Exception("Failed to assign personel");
            }
            
            // Log activity
            $this->logActivity('PERSONEL_ASSIGNED', "Assigned {$personel['nama']} to operation ID: {$operationId}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error assigning personel: " . $e->getMessage());
        }
    }
    
    /**
     * Get operation statistics
     */
    public function getOperationStats() {
        try {
            $stats = [];
            
            // Total operations
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM operations");
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // By status
            $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM operations GROUP BY status");
            $stmt->execute();
            $stats['by_status'] = $stmt->fetchAll();
            
            // By jenis
            $stmt = $this->db->prepare("SELECT jenis_operasi, COUNT(*) as count FROM operations GROUP BY jenis_operasi ORDER BY count DESC");
            $stmt->execute();
            $stats['by_jenis'] = $stmt->fetchAll();
            
            // Active operations (not completed/cancelled)
            $stmt = $this->db->prepare("SELECT COUNT(*) as active FROM operations WHERE status NOT IN ('completed', 'cancelled')");
            $stmt->execute();
            $stats['active'] = $stmt->fetch()['active'];
            
            // This month
            $stmt = $this->db->prepare("SELECT COUNT(*) as this_month FROM operations WHERE MONTH(created_at) = MONTH(CURRENT_DATE) AND YEAR(created_at) = YEAR(CURRENT_DATE)");
            $stmt->execute();
            $stats['this_month'] = $stmt->fetch()['this_month'];
            
            return $stats;
            
        } catch (Exception $e) {
            throw new Exception("Error getting operation stats: " . $e->getMessage());
        }
    }
    
    /**
     * Validate operation data
     */
    private function validateOperationData($data, $isUpdate = false) {
        $required = ['nama_operasi', 'jenis_operasi', 'tanggal_mulai', 'lokasi'];
        
        foreach ($required as $field) {
            if (!$isUpdate && empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate jenis_operasi
        $validJenis = ['RENOPS', 'SPRIN', 'LAPHAR', 'AAR'];
        if (!empty($data['jenis_operasi']) && !in_array($data['jenis_operasi'], $validJenis)) {
            throw new Exception("Invalid operation type");
        }
        
        // Validate status
        $validStatus = ['planning', 'active', 'completed', 'cancelled'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatus)) {
            throw new Exception("Invalid status");
        }
        
        // Validate date format
        if (!empty($data['tanggal_mulai']) && !strtotime($data['tanggal_mulai'])) {
            throw new Exception("Invalid start date format");
        }
        
        if (!empty($data['tanggal_selesai']) && !strtotime($data['tanggal_selesai'])) {
            throw new Exception("Invalid end date format");
        }
    }
    
    /**
     * Check if operation has assignments
     */
    private function hasAssignments($operationId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM assignments WHERE operation_id = ?");
        $stmt->execute([$operationId]);
        
        return $stmt->fetch()['count'] > 0;
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
