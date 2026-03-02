<?php
/**
 * AssignmentsService - Business Logic Layer for Assignments Management
 * BAGOPS POLRES SAMOSIR - Service Layer Implementation
 */

class AssignmentsService {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->auth = new Auth($this->db);
    }
    
    /**
     * Get all assignments with filtering and pagination
     */
    public function getAllAssignments($filters = [], $page = 1, $limit = 25) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['search'])) {
                $whereClause .= " AND (p.nama LIKE ? OR p.nrp LIKE ? OR o.nama_operasi LIKE ? OR a.role_assignment LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['operation_id'])) {
                $whereClause .= " AND a.operation_id = ?";
                $params[] = $filters['operation_id'];
            }
            
            if (!empty($filters['personel_id'])) {
                $whereClause .= " AND a.personel_id = ?";
                $params[] = $filters['personel_id'];
            }
            
            if (!empty($filters['status'])) {
                $whereClause .= " AND a.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $whereClause .= " AND a.assigned_at >= ?";
                $params[] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $whereClause .= " AND a.assigned_at <= ?";
                $params[] = $filters['date_to'];
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM assignments a 
                        LEFT JOIN personel p ON a.personel_id = p.id 
                        LEFT JOIN operations o ON a.operation_id = o.id 
                        $whereClause";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // Get data with joins
            $sql = "SELECT a.*, p.nama as personel_nama, p.nrp, p.pangkat, o.nama_operasi, o.status as operation_status 
                    FROM assignments a 
                    LEFT JOIN personel p ON a.personel_id = p.id 
                    LEFT JOIN operations o ON a.operation_id = o.id 
                    $whereClause 
                    ORDER BY a.assigned_at DESC 
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
            throw new Exception("Error getting assignments data: " . $e->getMessage());
        }
    }
    
    /**
     * Get assignment by ID
     */
    public function getAssignmentById($id) {
        try {
            $sql = "SELECT a.*, p.nama as personel_nama, p.nrp, p.pangkat, p.jabatan_id, p.unit_id, 
                           o.nama_operasi, o.jenis_operasi, o.lokasi 
                    FROM assignments a 
                    LEFT JOIN personel p ON a.personel_id = p.id 
                    LEFT JOIN operations o ON a.operation_id = o.id 
                    WHERE a.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                throw new Exception("Assignment not found");
            }
            
            return $result;
            
        } catch (Exception $e) {
            throw new Exception("Error getting assignment: " . $e->getMessage());
        }
    }
    
    /**
     * Create new assignment
     */
    public function createAssignment($data) {
        try {
            // Validate required fields
            $this->validateAssignmentData($data);
            
            // Check if personel exists
            $personelSql = "SELECT id, nama, is_active FROM personel WHERE id = ?";
            $personelStmt = $this->db->prepare($personelSql);
            $personelStmt->execute([$data['personel_id']]);
            $personel = $personelStmt->fetch();
            
            if (!$personel) {
                throw new Exception("Personel not found");
            }
            
            if (!$personel['is_active']) {
                throw new Exception("Cannot assign inactive personel");
            }
            
            // Check if operation exists
            $operationSql = "SELECT id, nama_operasi, status FROM operations WHERE id = ?";
            $operationStmt = $this->db->prepare($operationSql);
            $operationStmt->execute([$data['operation_id']]);
            $operation = $operationStmt->fetch();
            
            if (!$operation) {
                throw new Exception("Operation not found");
            }
            
            if ($operation['status'] === 'completed' || $operation['status'] === 'cancelled') {
                throw new Exception("Cannot assign to completed or cancelled operation");
            }
            
            // Check if already assigned
            $checkSql = "SELECT id FROM assignments WHERE personel_id = ? AND operation_id = ? AND status != 'cancelled'";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([$data['personel_id'], $data['operation_id']]);
            
            if ($checkStmt->fetch()) {
                throw new Exception("Personel already assigned to this operation");
            }
            
            $sql = "INSERT INTO assignments (personel_id, operation_id, role_assignment, status, assigned_at, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['personel_id'],
                $data['operation_id'],
                $data['role_assignment'],
                $data['status'] ?? 'assigned'
            ]);
            
            if (!$result) {
                throw new Exception("Failed to create assignment");
            }
            
            $assignmentId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('ASSIGNMENT_CREATED', "Assigned {$personel['nama']} to {$operation['nama_operasi']} as {$data['role_assignment']}");
            
            return $assignmentId;
            
        } catch (Exception $e) {
            throw new Exception("Error creating assignment: " . $e->getMessage());
        }
    }
    
    /**
     * Update assignment
     */
    public function updateAssignment($id, $data) {
        try {
            // Check if assignment exists
            $existing = $this->getAssignmentById($id);
            
            // Validate required fields
            $this->validateAssignmentData($data, true);
            
            // If changing status to completed, validate
            if (isset($data['status']) && $data['status'] === 'completed') {
                if ($existing['status'] !== 'assigned' && $existing['status'] !== 'in_progress') {
                    throw new Exception("Can only complete assigned or in-progress assignments");
                }
            }
            
            $sql = "UPDATE assignments SET 
                    role_assignment = ?, 
                    status = ?, 
                    updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['role_assignment'] ?? $existing['role_assignment'],
                $data['status'] ?? $existing['status'],
                $id
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update assignment");
            }
            
            // Log activity
            $this->logActivity('ASSIGNMENT_UPDATED', "Updated assignment ID: {$id}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error updating assignment: " . $e->getMessage());
        }
    }
    
    /**
     * Delete assignment
     */
    public function deleteAssignment($id) {
        try {
            $assignment = $this->getAssignmentById($id);
            
            // Check if assignment can be deleted
            if ($assignment['status'] === 'completed') {
                throw new Exception("Cannot delete completed assignment");
            }
            
            $sql = "DELETE FROM assignments WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Failed to delete assignment");
            }
            
            // Log activity
            $this->logActivity('ASSIGNMENT_DELETED', "Deleted assignment ID: {$id}");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error deleting assignment: " . $e->getMessage());
        }
    }
    
    /**
     * Complete assignment
     */
    public function completeAssignment($id) {
        try {
            return $this->updateAssignment($id, ['status' => 'completed']);
        } catch (Exception $e) {
            throw new Exception("Error completing assignment: " . $e->getMessage());
        }
    }
    
    /**
     * Get assignments by personel
     */
    public function getAssignmentsByPersonel($personelId, $status = null) {
        try {
            $whereClause = "WHERE a.personel_id = ?";
            $params = [$personelId];
            
            if ($status) {
                $whereClause .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT a.*, o.nama_operasi, o.status as operation_status 
                    FROM assignments a 
                    LEFT JOIN operations o ON a.operation_id = o.id 
                    $whereClause 
                    ORDER BY a.assigned_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            throw new Exception("Error getting personel assignments: " . $e->getMessage());
        }
    }
    
    /**
     * Get assignments by operation
     */
    public function getAssignmentsByOperation($operationId, $status = null) {
        try {
            $whereClause = "WHERE a.operation_id = ?";
            $params = [$operationId];
            
            if ($status) {
                $whereClause .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $sql = "SELECT a.*, p.nama as personel_nama, p.nrp, p.pangkat, p.jabatan_id 
                    FROM assignments a 
                    LEFT JOIN personel p ON a.personel_id = p.id 
                    $whereClause 
                    ORDER BY a.assigned_at DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (Exception $e) {
            throw new Exception("Error getting operation assignments: " . $e->getMessage());
        }
    }
    
    /**
     * Get assignment statistics
     */
    public function getAssignmentStats() {
        try {
            $stats = [];
            
            // Total assignments
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM assignments");
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // By status
            $stmt = $this->db->prepare("SELECT status, COUNT(*) as count FROM assignments GROUP BY status");
            $stmt->execute();
            $stats['by_status'] = $stmt->fetchAll();
            
            // Pending assignments
            $stmt = $this->db->prepare("SELECT COUNT(*) as pending FROM assignments WHERE status = 'assigned'");
            $stmt->execute();
            $stats['pending'] = $stmt->fetch()['pending'];
            
            // In progress
            $stmt = $this->db->prepare("SELECT COUNT(*) as in_progress FROM assignments WHERE status = 'in_progress'");
            $stmt->execute();
            $stats['in_progress'] = $stmt->fetch()['in_progress'];
            
            // Completed
            $stmt = $this->db->prepare("SELECT COUNT(*) as completed FROM assignments WHERE status = 'completed'");
            $stmt->execute();
            $stats['completed'] = $stmt->fetch()['completed'];
            
            // This week
            $stmt = $this->db->prepare("SELECT COUNT(*) as this_week FROM assignments WHERE YEARWEEK(assigned_at) = YEARWEEK(CURRENT_DATE)");
            $stmt->execute();
            $stats['this_week'] = $stmt->fetch()['this_week'];
            
            return $stats;
            
        } catch (Exception $e) {
            throw new Exception("Error getting assignment stats: " . $e->getMessage());
        }
    }
    
    /**
     * Validate assignment data
     */
    private function validateAssignmentData($data, $isUpdate = false) {
        $required = ['personel_id', 'operation_id', 'role_assignment'];
        
        foreach ($required as $field) {
            if (!$isUpdate && empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate status
        $validStatus = ['assigned', 'in_progress', 'completed', 'cancelled'];
        if (!empty($data['status']) && !in_array($data['status'], $validStatus)) {
            throw new Exception("Invalid status");
        }
        
        // Validate role assignment length
        if (!empty($data['role_assignment']) && strlen($data['role_assignment']) < 2) {
            throw new Exception("Role assignment must be at least 2 characters");
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
