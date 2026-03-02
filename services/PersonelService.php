<?php
/**
 * PersonelService - Business Logic Layer for Personel Management
 * BAGOPS POLRES SAMOSIR - Service Layer Implementation
 */

class PersonelService {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->auth = new Auth($this->db);
    }
    
    /**
     * Get all personel with filtering and pagination
     */
    public function getAllPersonel($filters = [], $page = 1, $limit = 25) {
        try {
            $offset = ($page - 1) * $limit;
            $whereClause = "WHERE 1=1";
            $params = [];
            
            // Apply filters
            if (!empty($filters['search'])) {
                $whereClause .= " AND (p.nama LIKE ? OR p.nrp LIKE ? OR p.pangkat LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (!empty($filters['pangkat'])) {
                $whereClause .= " AND p.pangkat = ?";
                $params[] = $filters['pangkat'];
            }
            
            if (!empty($filters['unit_id'])) {
                $whereClause .= " AND p.unit_id = ?";
                $params[] = $filters['unit_id'];
            }
            
            if (!empty($filters['is_active'])) {
                $whereClause .= " AND p.is_active = ?";
                $params[] = $filters['is_active'];
            }
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM personel p $whereClause";
            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($params);
            $total = $countStmt->fetch()['total'];
            
            // Get data with joins
            $sql = "SELECT p.*, j.nama_jabatan, u.nama_unit, sp.nama_status 
                    FROM personel p 
                    LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                    LEFT JOIN m_unit_organisasi u ON p.unit_id = u.id 
                    LEFT JOIN m_status_personel sp ON p.status_personel_id = sp.id 
                    $whereClause 
                    ORDER BY p.nama ASC 
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
            throw new Exception("Error getting personel data: " . $e->getMessage());
        }
    }
    
    /**
     * Get personel by ID
     */
    public function getPersonelById($id) {
        try {
            $sql = "SELECT p.*, j.nama_jabatan, u.nama_unit, sp.nama_status 
                    FROM personel p 
                    LEFT JOIN m_jabatan j ON p.jabatan_id = j.id 
                    LEFT JOIN m_unit_organisasi u ON p.unit_id = u.id 
                    LEFT JOIN m_status_personel sp ON p.status_personel_id = sp.id 
                    WHERE p.id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if (!$result) {
                throw new Exception("Personel not found");
            }
            
            return $result;
            
        } catch (Exception $e) {
            throw new Exception("Error getting personel: " . $e->getMessage());
        }
    }
    
    /**
     * Create new personel
     */
    public function createPersonel($data) {
        try {
            // Validate required fields
            $this->validatePersonelData($data);
            
            // Check if NRP already exists
            if ($this->nrpExists($data['nrp'])) {
                throw new Exception("NRP already exists");
            }
            
            $sql = "INSERT INTO personel (nrp, nama, pangkat, jabatan_id, unit_id, kantor, status_personel_id, is_active, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['nrp'],
                $data['nama'],
                $data['pangkat'],
                $data['jabatan_id'] ?? null,
                $data['unit_id'] ?? null,
                $data['kantor'] ?? '',
                $data['status_personel_id'] ?? 1,
                $data['is_active'] ?? 1
            ]);
            
            if (!$result) {
                throw new Exception("Failed to create personel");
            }
            
            $personelId = $this->db->lastInsertId();
            
            // Log activity
            $this->logActivity('PERSONEL_CREATED', "Created personel: {$data['nama']} ({$data['nrp']})");
            
            return $personelId;
            
        } catch (Exception $e) {
            throw new Exception("Error creating personel: " . $e->getMessage());
        }
    }
    
    /**
     * Update personel
     */
    public function updatePersonel($id, $data) {
        try {
            // Check if personel exists
            $existing = $this->getPersonelById($id);
            
            // Validate required fields
            $this->validatePersonelData($data, true);
            
            // Check if NRP already exists (for other personel)
            if (isset($data['nrp']) && $data['nrp'] !== $existing['nrp']) {
                if ($this->nrpExists($data['nrp'])) {
                    throw new Exception("NRP already exists");
                }
            }
            
            $sql = "UPDATE personel SET 
                    nrp = ?, 
                    nama = ?, 
                    pangkat = ?, 
                    jabatan_id = ?, 
                    unit_id = ?, 
                    kantor = ?, 
                    status_personel_id = ?, 
                    is_active = ?, 
                    updated_at = NOW() 
                    WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $data['nrp'] ?? $existing['nrp'],
                $data['nama'] ?? $existing['nama'],
                $data['pangkat'] ?? $existing['pangkat'],
                $data['jabatan_id'] ?? $existing['jabatan_id'],
                $data['unit_id'] ?? $existing['unit_id'],
                $data['kantor'] ?? $existing['kantor'],
                $data['status_personel_id'] ?? $existing['status_personel_id'],
                $data['is_active'] ?? $existing['is_active'],
                $id
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update personel");
            }
            
            // Log activity
            $this->logActivity('PERSONEL_UPDATED', "Updated personel: {$data['nama']} ({$data['nrp']})");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error updating personel: " . $e->getMessage());
        }
    }
    
    /**
     * Delete personel
     */
    public function deletePersonel($id) {
        try {
            $personel = $this->getPersonelById($id);
            
            // Check if personel has assignments
            if ($this->hasAssignments($id)) {
                throw new Exception("Cannot delete personel with active assignments");
            }
            
            $sql = "DELETE FROM personel WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if (!$result) {
                throw new Exception("Failed to delete personel");
            }
            
            // Log activity
            $this->logActivity('PERSONEL_DELETED', "Deleted personel: {$personel['nama']} ({$personel['nrp']})");
            
            return true;
            
        } catch (Exception $e) {
            throw new Exception("Error deleting personel: " . $e->getMessage());
        }
    }
    
    /**
     * Get personel statistics
     */
    public function getPersonelStats() {
        try {
            $stats = [];
            
            // Total personel
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM personel");
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // Active personel
            $stmt = $this->db->prepare("SELECT COUNT(*) as active FROM personel WHERE is_active = 1");
            $stmt->execute();
            $stats['active'] = $stmt->fetch()['active'];
            
            // By pangkat
            $stmt = $this->db->prepare("SELECT pangkat, COUNT(*) as count FROM personel GROUP BY pangkat ORDER BY count DESC");
            $stmt->execute();
            $stats['by_pangkat'] = $stmt->fetchAll();
            
            // By unit
            $stmt = $this->db->prepare("SELECT u.nama_unit, COUNT(*) as count FROM personel p LEFT JOIN m_unit_organisasi u ON p.unit_id = u.id GROUP BY p.unit_id ORDER BY count DESC LIMIT 10");
            $stmt->execute();
            $stats['by_unit'] = $stmt->fetchAll();
            
            return $stats;
            
        } catch (Exception $e) {
            throw new Exception("Error getting personel stats: " . $e->getMessage());
        }
    }
    
    /**
     * Validate personel data
     */
    private function validatePersonelData($data, $isUpdate = false) {
        $required = ['nrp', 'nama', 'pangkat'];
        
        foreach ($required as $field) {
            if (!$isUpdate && empty($data[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        // Validate NRP format
        if (!empty($data['nrp']) && !preg_match('/^[0-9]{8,10}$/', $data['nrp'])) {
            throw new Exception("Invalid NRP format");
        }
        
        // Validate name
        if (!empty($data['nama']) && strlen($data['nama']) < 3) {
            throw new Exception("Name must be at least 3 characters");
        }
    }
    
    /**
     * Check if NRP exists
     */
    private function nrpExists($nrp, $excludeId = null) {
        $sql = "SELECT id FROM personel WHERE nrp = ?";
        $params = [$nrp];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetch() !== false;
    }
    
    /**
     * Check if personel has assignments
     */
    private function hasAssignments($personelId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM assignments WHERE personel_id = ?");
        $stmt->execute([$personelId]);
        
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
