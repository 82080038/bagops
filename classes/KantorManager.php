<?php
/**
 * Kantor Management Class for BAGOPS
 * Office management system with full CRUD operations
 */

class KantorManager {
    private $db;
    private $auth;
    
    public function __construct($database, $auth) {
        $this->db = $database;
        $this->auth = $auth;
    }
    
    /**
     * Create new kantor
     */
    public function createKantor($data) {
        try {
            // Check for duplicate nama_kantor
            $stmt = $this->db->prepare("SELECT id FROM kantor WHERE nama_kantor = ?");
            $stmt->execute([$data['nama_kantor']]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Nama kantor sudah ada'];
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO kantor (
                    nama_kantor, tipe_kantor_polisi, klasifikasi, level_kompleksitas,
                    pimpinan_default_pangkat, alamat, latitude, longitude, telepon,
                    email, jam_operasional, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['nama_kantor'],
                $data['tipe_kantor_polisi'] ?? null,
                $data['klasifikasi'] ?? null,
                $data['level_kompleksitas'] ?? null,
                $data['pimpinan_default_pangkat'] ?? null,
                $data['alamat'] ?? null,
                $data['latitude'] ?? null,
                $data['longitude'] ?? null,
                $data['telepon'] ?? null,
                $data['email'] ?? null,
                $data['jam_operasional'] ?? null,
                $data['status'] ?? 'aktif'
            ]);
            
            $kantor_id = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'kantor_id' => $kantor_id,
                'message' => 'Kantor berhasil dibuat'
            ];
            
        } catch (Exception $e) {
            error_log("Create Kantor Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal membuat kantor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all kantor with filtering
     */
    public function getKantor($filters = []) {
        try {
            $where_conditions = ["1=1"];
            $params = [];
            
            // Build WHERE clause
            if (!empty($filters['tipe_kantor_polisi'])) {
                $where_conditions[] = "k.tipe_kantor_polisi = ?";
                $params[] = $filters['tipe_kantor_polisi'];
            }
            
            if (!empty($filters['klasifikasi'])) {
                $where_conditions[] = "k.klasifikasi = ?";
                $params[] = $filters['klasifikasi'];
            }
            
            if (!empty($filters['level_kompleksitas'])) {
                $where_conditions[] = "k.level_kompleksitas = ?";
                $params[] = $filters['level_kompleksitas'];
            }
            
            if (!empty($filters['status'])) {
                $where_conditions[] = "k.status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $where_conditions[] = "(k.nama_kantor LIKE ? OR k.telepon LIKE ? OR k.email LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $where_clause = implode(" AND ", $where_conditions);
            
            $stmt = $this->db->prepare("
                SELECT 
                    k.*,
                    COUNT(p.id) as jumlah_personel,
                    (
                        SELECT p1.nama 
                        FROM personel p1 
                        WHERE p1.unit = k.nama_kantor 
                        AND p1.is_active = 1
                        AND (
                            p1.pangkat = 'AKBP' OR 
                            p1.pangkat LIKE 'KOMPOL%' OR 
                            p1.pangkat LIKE 'AKP%'
                        )
                        ORDER BY 
                            CASE 
                                WHEN p1.pangkat = 'AKBP' THEN 1
                                WHEN p1.pangkat LIKE 'KOMPOL%' THEN 2
                                WHEN p1.pangkat LIKE 'AKP%' THEN 2
                                ELSE 3
                            END ASC
                        LIMIT 1
                    ) as pimpinan_nama,
                    (
                        SELECT p1.pangkat 
                        FROM personel p1 
                        WHERE p1.unit = k.nama_kantor 
                        AND p1.is_active = 1
                        AND (
                            p1.pangkat = 'AKBP' OR 
                            p1.pangkat LIKE 'KOMPOL%' OR 
                            p1.pangkat LIKE 'AKP%'
                        )
                        ORDER BY 
                            CASE 
                                WHEN p1.pangkat = 'AKBP' THEN 1
                                WHEN p1.pangkat LIKE 'KOMPOL%' THEN 2
                                WHEN p1.pangkat LIKE 'AKP%' THEN 2
                                ELSE 3
                            END ASC
                        LIMIT 1
                    ) as pimpinan_pangkat_asli,
                    (
                        SELECT p1.nrp 
                        FROM personel p1 
                        WHERE p1.unit = k.nama_kantor 
                        AND p1.is_active = 1
                        AND (
                            p1.pangkat = 'AKBP' OR 
                            p1.pangkat LIKE 'KOMPOL%' OR 
                            p1.pangkat LIKE 'AKP%'
                        )
                        ORDER BY 
                            CASE 
                                WHEN p1.pangkat = 'AKBP' THEN 1
                                WHEN p1.pangkat LIKE 'KOMPOL%' THEN 2
                                WHEN p1.pangkat LIKE 'AKP%' THEN 2
                                ELSE 3
                            END ASC
                        LIMIT 1
                    ) as pimpinan_nrp
                FROM kantor k
                LEFT JOIN personel p ON k.nama_kantor = p.unit
                WHERE {$where_clause}
                GROUP BY k.id
                ORDER BY k.tipe_kantor_polisi DESC, k.nama_kantor ASC
            ");
            
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Kantor Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get single kantor by ID
     */
    public function getKantorDetail($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    k.*,
                    COUNT(p.id) as jumlah_personel
                FROM kantor k
                LEFT JOIN personel p ON k.nama_kantor = p.unit
                WHERE k.id = ?
                GROUP BY k.id
            ");
            
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Get Kantor Detail Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update kantor
     */
    public function updateKantor($id, $data) {
        try {
            // Check for duplicate nama_kantor (excluding current record)
            $stmt = $this->db->prepare("SELECT id FROM kantor WHERE nama_kantor = ? AND id != ?");
            $stmt->execute([$data['nama_kantor'], $id]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Nama kantor sudah ada'];
            }
            
            $fields = [];
            $params = [];
            
            $updatable_fields = [
                'nama_kantor', 'tipe_kantor_polisi', 'klasifikasi', 'level_kompleksitas',
                'pimpinan_default_pangkat', 'alamat', 'latitude', 'longitude',
                'telepon', 'email', 'jam_operasional', 'status'
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
            
            $params[] = $id;
            
            $sql = "UPDATE kantor SET " . implode(", ", $fields) . " WHERE id = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return ['success' => true, 'message' => 'Kantor berhasil diupdate'];
            
        } catch (Exception $e) {
            error_log("Update Kantor Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update kantor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Delete kantor
     */
    public function deleteKantor($id) {
        try {
            // Check if kantor has personnel
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM personel WHERE unit = (
                    SELECT nama_kantor FROM kantor WHERE id = ?
                )
            ");
            $stmt->execute([$id]);
            $personel_count = $stmt->fetch()['count'];
            
            if ($personel_count > 0) {
                return ['success' => false, 'message' => 'Tidak dapat menghapus kantor yang memiliki personel'];
            }
            
            $stmt = $this->db->prepare("DELETE FROM kantor WHERE id = ?");
            $stmt->execute([$id]);
            
            return ['success' => true, 'message' => 'Kantor berhasil dihapus'];
            
        } catch (Exception $e) {
            error_log("Delete Kantor Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal hapus kantor: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get comprehensive statistics
     */
    public function getKantorStats() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_kantor,
                    SUM(CASE WHEN tipe_kantor_polisi = 'POLRES' THEN 1 ELSE 0 END) as total_polres,
                    SUM(CASE WHEN tipe_kantor_polisi = 'POLSEK' THEN 1 ELSE 0 END) as total_polsek,
                    SUM(CASE WHEN tipe_kantor_polisi = 'POS' THEN 1 ELSE 0 END) as total_pos,
                    SUM(CASE WHEN status = 'aktif' THEN 1 ELSE 0 END) as aktif,
                    SUM(CASE WHEN status = 'non_aktif' THEN 1 ELSE 0 END) as non_aktif,
                    SUM(CASE WHEN klasifikasi = 'Kabupaten/Kota' THEN 1 ELSE 0 END) as kabupaten,
                    SUM(CASE WHEN klasifikasi = 'Kecamatan' THEN 1 ELSE 0 END) as kecamatan,
                    SUM(CASE WHEN level_kompleksitas = 'Tinggi' THEN 1 ELSE 0 END) as kompleksitas_tinggi,
                    SUM(CASE WHEN level_kompleksitas = 'Menengah' THEN 1 ELSE 0 END) as kompleksitas_menengah,
                    SUM(CASE WHEN level_kompleksitas = 'Rendah' THEN 1 ELSE 0 END) as kompleksitas_rendah
                FROM kantor
            ");
            
            $stmt->execute();
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get personnel distribution
            $stmt = $this->db->prepare("
                SELECT 
                    k.nama_kantor,
                    k.tipe_kantor_polisi,
                    COUNT(p.id) as jumlah_personel
                FROM kantor k
                LEFT JOIN personel p ON k.nama_kantor = p.unit
                GROUP BY k.id
                ORDER BY jumlah_personel DESC
            ");
            
            $stmt->execute();
            $personel_distribution = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stats['personel_distribution'] = $personel_distribution;
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Get Kantor Stats Error: " . $e->getMessage());
            return [
                'total_kantor' => 0,
                'total_polres' => 0,
                'total_polsek' => 0,
                'total_pos' => 0,
                'aktif' => 0,
                'non_aktif' => 0,
                'kabupaten' => 0,
                'kecamatan' => 0,
                'kompleksitas_tinggi' => 0,
                'kompleksitas_menengah' => 0,
                'kompleksitas_rendah' => 0,
                'personel_distribution' => []
            ];
        }
    }
    
    /**
     * Toggle kantor status
     */
    public function toggleStatus($id) {
        try {
            $stmt = $this->db->prepare("SELECT status FROM kantor WHERE id = ?");
            $stmt->execute([$id]);
            $kantor = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$kantor) {
                return ['success' => false, 'message' => 'Kantor tidak ditemukan'];
            }
            
            $new_status = $kantor['status'] === 'aktif' ? 'non_aktif' : 'aktif';
            
            $stmt = $this->db->prepare("UPDATE kantor SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            
            return [
                'success' => true,
                'message' => 'Status kantor berhasil diupdate',
                'new_status' => $new_status
            ];
            
        } catch (Exception $e) {
            error_log("Toggle Status Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal update status: ' . $e->getMessage()];
        }
    }
    
    /**
     * Export kantor data
     */
    public function exportKantor($format = 'excel') {
        try {
            $kantor = $this->getKantor();
            
            if ($format === 'excel') {
                return $this->exportToExcel($kantor);
            } elseif ($format === 'pdf') {
                return $this->exportToPDF($kantor);
            } else {
                return ['success' => false, 'message' => 'Format tidak didukung'];
            }
            
        } catch (Exception $e) {
            error_log("Export Kantor Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal export: ' . $e->getMessage()];
        }
    }
    
    /**
     * Export to Excel (CSV format for simplicity)
     */
    private function exportToExcel($kantor) {
        $filename = 'kantor_export_' . date('YmdHis') . '.csv';
        $filepath = __DIR__ . '/../storage/exports/' . $filename;
        
        // Ensure exports directory exists
        if (!file_exists(__DIR__ . '/../storage/exports/')) {
            mkdir(__DIR__ . '/../storage/exports/', 0755, true);
        }
        
        $handle = fopen($filepath, 'w');
        
        // Header
        fputcsv($handle, [
            'ID', 'Nama Kantor', 'Tipe', 'Klasifikasi', 'Level Kompleksitas',
            'Pimpinan Default', 'Alamat', 'Telepon', 'Email', 'Status', 'Jumlah Personel'
        ]);
        
        // Data
        foreach ($kantor as $k) {
            fputcsv($handle, [
                $k['id'],
                $k['nama_kantor'],
                $k['tipe_kantor_polisi'] ?? '-',
                $k['klasifikasi'] ?? '-',
                $k['level_kompleksitas'] ?? '-',
                $k['pimpinan_default_pangkat'] ?? '-',
                $k['alamat'] ?? '-',
                $k['telepon'] ?? '-',
                $k['email'] ?? '-',
                $k['status'] ?? '-',
                $k['jumlah_personel'] ?? 0
            ]);
        }
        
        fclose($handle);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Export berhasil'
        ];
    }
    
    /**
     * Export to PDF (HTML format for simplicity)
     */
    private function exportToPDF($kantor) {
        $filename = 'kantor_export_' . date('YmdHis') . '.html';
        $filepath = __DIR__ . '/../storage/exports/' . $filename;
        
        // Ensure exports directory exists
        if (!file_exists(__DIR__ . '/../storage/exports/')) {
            mkdir(__DIR__ . '/../storage/exports/', 0755, true);
        }
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Daftar Kantor - POLRES SAMOSIR</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .status-aktif { color: green; font-weight: bold; }
                .status-non-aktif { color: red; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>DAFTAR KANTOR</h1>
                <h2>POLRES SAMOSIR</h2>
                <p><strong>Dibuat:</strong> ' . date('d/m/Y H:i') . '</p>
            </div>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nama Kantor</th>
                    <th>Tipe</th>
                    <th>Klasifikasi</th>
                    <th>Level Kompleksitas</th>
                    <th>Pimpinan Default</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Status</th>
                    <th>Jumlah Personel</th>
                </tr>';
        
        foreach ($kantor as $k) {
            $status_class = $k['status'] === 'aktif' ? 'status-aktif' : 'status-non-aktif';
            
            $html .= '
                <tr>
                    <td>' . $k['id'] . '</td>
                    <td>' . htmlspecialchars($k['nama_kantor']) . '</td>
                    <td>' . htmlspecialchars($k['tipe_kantor_polisi'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($k['klasifikasi'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($k['level_kompleksitas'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($k['pimpinan_default_pangkat'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($k['alamat'] ?? '-') . '</td>
                    <td>' . htmlspecialchars($k['telepon'] ?? '-') . '</td>
                    <td class="' . $status_class . '">' . ucfirst($k['status'] ?? '-') . '</td>
                    <td>' . number_format($k['jumlah_personel'] ?? 0) . '</td>
                </tr>';
        }
        
        $html .= '
            </table>
            
            <div class="footer">
                <p><em>Laporan ini dibuat secara otomatis oleh sistem BAGOPS POLRES SAMOSIR</em></p>
            </div>
        </body>
        </html>';
        
        file_put_contents($filepath, $html);
        
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'Export PDF berhasil'
        ];
    }
}
?>
