<?php
/**
 * Assignment Management Class for BAGOPS
 * Task assignment and tracking system
 */

class AssignmentManager {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    /**
     * Create new assignment
     */
    public function createAssignment($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO assignments (
                    judul_assignment, deskripsi, personel_id, operation_id,
                    tanggal_mulai, tanggal_selesai, prioritas, status_assignment,
                    created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['judul_assignment'],
                $data['deskripsi'] ?? '',
                $data['personel_id'],
                $data['operation_id'] ?? null,
                $data['tanggal_mulai'],
                $data['tanggal_selesai'] ?? null,
                $data['prioritas'] ?? 'sedang',
                $data['status_assignment'] ?? 'ditugaskan',
                $this->auth->getCurrentUser()['id']
            ]);
            
            $assignment_id = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'assignment_id' => $assignment_id,
                'message' => 'Assignment berhasil dibuat'
            ];
            
        } catch (Exception $e) {
            error_log("Create Assignment Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal membuat assignment: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get assignments with filtering
     */
    public function getAssignments($filters = []) {
        try {
            $where_conditions = ["1=1"];
            $params = [];
            
            // Build WHERE clause
            if (!empty($filters['status_assignment'])) {
                $where_conditions[] = "a.status_assignment = ?";
                $params[] = $filters['status_assignment'];
            }
            
            if (!empty($filters['personel_id'])) {
                $where_conditions[] = "a.personel_id = ?";
                $params[] = $filters['personel_id'];
            }
            
            if (!empty($filters['operation_id'])) {
                $where_conditions[] = "a.operation_id = ?";
                $params[] = $filters['operation_id'];
            }
            
            if (!empty($filters['prioritas'])) {
                $where_conditions[] = "a.prioritas = ?";
                $params[] = $filters['prioritas'];
            }
            
            $where_clause = implode(" AND ", $where_conditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    p.nama as personel_nama, p.nrp, p.pangkat, p.jabatan,
                    o.kode_operasi, o.nama_operasi,
                    creator.username as created_by_name
                FROM assignments a
                LEFT JOIN personel p ON a.personel_id = p.id
                LEFT JOIN operations o ON a.operation_id = o.id
                LEFT JOIN users creator ON a.created_by = creator.id
                WHERE {$where_clause}
                ORDER BY a.created_at DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Assignments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single assignment
     */
    public function getAssignment($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    p.nama as personel_nama, p.nrp, p.pangkat, p.jabatan,
                    o.kode_operasi, o.nama_operasi,
                    creator.username as created_by_name
                FROM assignments a
                LEFT JOIN personel p ON a.personel_id = p.id
                LEFT JOIN operations o ON a.operation_id = o.id
                LEFT JOIN users creator ON a.created_by = creator.id
                WHERE a.id = ?
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Assignment Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update assignment
     */
    public function updateAssignment($id, $data) {
        try {
            $fields = [];
            $params = [];
            
            $updatable_fields = [
                'judul_assignment', 'deskripsi', 'personel_id', 'operation_id',
                'tanggal_mulai', 'tanggal_selesai', 'prioritas', 'status_assignment'
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
            
            $sql = "UPDATE assignments SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Assignment berhasil diupdate'];
            
        } catch (Exception $e) {
            error_log("Update Assignment Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update assignment: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete assignment
     */
    public function deleteAssignment($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM assignments WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Assignment berhasil dihapus'];
            
        } catch (Exception $e) {
            error_log("Delete Assignment Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal hapus assignment: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get assignment statistics
     */
    public function getAssignmentStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_assignments,
                    SUM(CASE WHEN status_assignment = 'ditugaskan' THEN 1 ELSE 0 END) as assigned,
                    SUM(CASE WHEN status_assignment = 'diproses' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status_assignment = 'selesai' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status_assignment = 'terlambat' THEN 1 ELSE 0 END) as overdue,
                    SUM(CASE WHEN tanggal_selesai < CURDATE() AND status_assignment != 'selesai' THEN 1 ELSE 0 END) as overdue_count
                FROM assignments
            ");
            
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Assignment Stats Error: " . $e->getMessage());
            return [
                'total_assignments' => 0,
                'assigned' => 0,
                'in_progress' => 0,
                'completed' => 0,
                'overdue' => 0,
                'overdue_count' => 0
            ];
        }
    }
    
    /**
     * Get personnel workload
     */
    public function getPersonnelWorkload($personel_id = null) {
        try {
            $where_clause = $personel_id ? "WHERE a.personel_id = ?" : "";
            $params = $personel_id ? [$personel_id] : [];
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.id as personel_id,
                    p.nama,
                    p.nrp,
                    p.pangkat,
                    COUNT(a.id) as total_assignments,
                    SUM(CASE WHEN a.status_assignment = 'ditugaskan' THEN 1 ELSE 0 END) as assigned_count,
                    SUM(CASE WHEN a.status_assignment = 'diproses' THEN 1 ELSE 0 END) as in_progress_count,
                    SUM(CASE WHEN a.status_assignment = 'selesai' THEN 1 ELSE 0 END) as completed_count
                FROM personel p
                LEFT JOIN assignments a ON p.id = a.personel_id
                {$where_clause}
                GROUP BY p.id
                ORDER BY total_assignments DESC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Personnel Workload Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update assignment status
     */
    public function updateAssignmentStatus($id, $status, $catatan = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE assignments 
                SET status_assignment = ?, catatan = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([$status, $catatan, $id]);
            
            return ['success' => true, 'message' => 'Status assignment berhasil diupdate'];
            
        } catch (Exception $e) {
            error_log("Update Assignment Status Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get overdue assignments
     */
    public function getOverdueAssignments() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    p.nama as personel_nama, p.nrp,
                    o.kode_operasi,
                    DATEDIFF(CURDATE(), a.tanggal_selesai) as days_overdue
                FROM assignments a
                LEFT JOIN personel p ON a.personel_id = p.id
                LEFT JOIN operations o ON a.operation_id = o.id
                WHERE a.tanggal_selesai < CURDATE() 
                AND a.status_assignment NOT IN ('selesai', 'dibatalkan')
                ORDER BY a.tanggal_selesai ASC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Overdue Assignments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get upcoming assignments
     */
    public function getUpcomingAssignments($days = 7) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.*,
                    p.nama as personel_nama, p.nrp,
                    o.kode_operasi,
                    DATEDIFF(a.tanggal_mulai, CURDATE()) as days_until
                FROM assignments a
                LEFT JOIN personel p ON a.personel_id = p.id
                LEFT JOIN operations o ON a.operation_id = o.id
                WHERE a.tanggal_mulai BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND a.status_assignment NOT IN ('selesai', 'dibatalkan')
                ORDER BY a.tanggal_mulai ASC
            ");
            
            $stmt->execute([$days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Upcoming Assignments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create assignment template
     */
    public function createTemplate($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO assignment_templates (
                    nama_template, deskripsi_template, default_prioritas,
                    default_status, created_by
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['nama_template'],
                $data['deskripsi_template'] ?? '',
                $data['default_prioritas'] ?? 'sedang',
                $data['default_status'] ?? 'ditugaskan',
                $this->auth->getCurrentUser()['id']
            ]);
            
            return [
                'success' => true,
                'template_id' => $this->db->lastInsertId(),
                'message' => 'Template berhasil dibuat'
            ];
            
        } catch (Exception $e) {
            error_log("Create Template Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal membuat template: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get assignment templates
     */
    public function getTemplates() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    at.*,
                    u.username as created_by_name
                FROM assignment_templates at
                LEFT JOIN users u ON at.created_by = u.id
                ORDER BY at.created_at DESC
            ");
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Templates Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create assignment from template
     */
    public function createFromTemplate($template_id, $personel_id, $tanggal_mulai, $tanggal_selesai = null) {
        try {
            // Get template
            $stmt = $this->db->prepare("SELECT * FROM assignment_templates WHERE id = ?");
            $stmt->execute([$template_id]);
            $template = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$template) {
                return ['success' => false, 'message' => 'Template tidak ditemukan'];
            }
            
            // Create assignment from template
            $assignment_data = [
                'judul_assignment' => $template['nama_template'],
                'deskripsi' => $template['deskripsi_template'],
                'personel_id' => $personel_id,
                'prioritas' => $template['default_prioritas'],
                'status_assignment' => $template['default_status'],
                'tanggal_mulai' => $tanggal_mulai,
                'tanggal_selesai' => $tanggal_selesai
            ];
            
            return $this->createAssignment($assignment_data);
            
        } catch (Exception $e) {
            error_log("Create From Template Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal membuat assignment dari template: ' . $e->getMessage()];
        }
    }
}
?>
