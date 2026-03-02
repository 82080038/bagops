<?php
session_start();
require_once '../config/database.php';
require_once '../classes/Auth.php';

header('Content-Type: application/json');

try {
    $auth = new Auth((new Database())->getConnection());
    
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    }
    
    // Check permissions
    if (!$auth->hasPermission('manage_users')) {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit();
    }
    
    $action = $_POST['action'] ?? '';
    $db = (new Database())->getConnection();
    
    switch ($action) {
        case 'get_jabatan_list':
            getJabatanList($db);
            break;
            
        case 'add_jabatan':
            addJabatan($db);
            break;
            
        case 'update_jabatan':
            updateJabatan($db);
            break;
            
        case 'delete_jabatan':
            deleteJabatan($db);
            break;
            
        case 'search_jabatan':
            searchJabatan($db);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    error_log("Jabatan Management Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

function getJabatanList($db) {
    try {
        // Get from dynamic jabatan table first, then fallback to existing data
        $query = "
            SELECT 
                id,
                nama_jabatan as nama,
                kode_jabatan as kode,
                level_jabatan as level,
                parent_id,
                is_active,
                created_at,
                updated_at,
                'dynamic' as source
            FROM dynamic_jabatan 
            WHERE is_active = 1
            ORDER BY level_jabatan, nama_jabatan
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        $dynamicJabatans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get unique jabatans from personel table as fallback
        $personelQuery = "
            SELECT DISTINCT 
                jabatan as nama,
                jabatan as kode,
                0 as level,
                NULL as parent_id,
                1 as is_active,
                NOW() as created_at,
                NOW() as updated_at,
                'personel' as source,
                jabatan as id
            FROM personel 
            WHERE jabatan IS NOT NULL 
            AND jabatan != ''
            AND jabatan NOT IN (SELECT nama_jabatan FROM dynamic_jabatan WHERE is_active = 1)
            ORDER BY jabatan
        ";
        
        $stmt = $db->prepare($personelQuery);
        $stmt->execute();
        $personelJabatans = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combine results
        $allJabatans = array_merge($dynamicJabatans, $personelJabatans);
        
        echo json_encode([
            'success' => true,
            'data' => $allJabatans,
            'total' => count($allJabatans)
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Failed to get jabatan list: " . $e->getMessage());
    }
}

function addJabatan($db) {
    try {
        $namaJabatan = trim($_POST['nama_jabatan'] ?? '');
        $kodeJabatan = trim($_POST['kode_jabatan'] ?? '');
        $levelJabatan = intval($_POST['level_jabatan'] ?? 0);
        $parentId = intval($_POST['parent_id'] ?? 0);
        
        // Validation
        if (empty($namaJabatan)) {
            throw new Exception("Nama jabatan harus diisi");
        }
        
        // Check if jabatan already exists
        $checkQuery = "SELECT id FROM dynamic_jabatan WHERE nama_jabatan = :nama AND is_active = 1";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':nama', $namaJabatan);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            throw new Exception("Jabatan '$namaJabatan' sudah ada");
        }
        
        // Generate kode if not provided
        if (empty($kodeJabatan)) {
            $kodeJabatan = generateKodeJabatan($namaJabatan);
        }
        
        // Insert new jabatan
        $insertQuery = "
            INSERT INTO dynamic_jabatan 
            (nama_jabatan, kode_jabatan, level_jabatan, parent_id, created_by, created_at, updated_at)
            VALUES (:nama, :kode, :level, :parent, :created_by, NOW(), NOW())
        ";
        
        $stmt = $db->prepare($insertQuery);
        $stmt->bindParam(':nama', $namaJabatan);
        $stmt->bindParam(':kode', $kodeJabatan);
        $stmt->bindParam(':level', $levelJabatan);
        $stmt->bindParam(':parent', $parentId);
        $stmt->bindParam(':created_by', $_SESSION['user_id']);
        
        if ($stmt->execute()) {
            $id = $db->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'message' => 'Jabatan berhasil ditambahkan',
                'id' => $id,
                'data' => [
                    'id' => $id,
                    'nama' => $namaJabatan,
                    'kode' => $kodeJabatan,
                    'level' => $levelJabatan,
                    'parent_id' => $parentId
                ]
            ]);
        } else {
            throw new Exception("Gagal menambah jabatan");
        }
        
    } catch (Exception $e) {
        throw new Exception("Add jabatan failed: " . $e->getMessage());
    }
}

function updateJabatan($db) {
    try {
        $id = intval($_POST['id'] ?? 0);
        $namaJabatan = trim($_POST['nama_jabatan'] ?? '');
        $kodeJabatan = trim($_POST['kode_jabatan'] ?? '');
        $levelJabatan = intval($_POST['level_jabatan'] ?? 0);
        $parentId = intval($_POST['parent_id'] ?? 0);
        
        if ($id <= 0 || empty($namaJabatan)) {
            throw new Exception("Data tidak valid");
        }
        
        // Check if jabatan exists
        $checkQuery = "SELECT id FROM dynamic_jabatan WHERE id = :id AND is_active = 1";
        $stmt = $db->prepare($checkQuery);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            throw new Exception("Jabatan tidak ditemukan");
        }
        
        // Update jabatan
        $updateQuery = "
            UPDATE dynamic_jabatan 
            SET nama_jabatan = :nama, 
                kode_jabatan = :kode, 
                level_jabatan = :level, 
                parent_id = :parent,
                updated_at = NOW()
            WHERE id = :id
        ";
        
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':nama', $namaJabatan);
        $stmt->bindParam(':kode', $kodeJabatan);
        $stmt->bindParam(':level', $levelJabatan);
        $stmt->bindParam(':parent', $parentId);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Jabatan berhasil diperbarui'
            ]);
        } else {
            throw new Exception("Gagal memperbarui jabatan");
        }
        
    } catch (Exception $e) {
        throw new Exception("Update jabatan failed: " . $e->getMessage());
    }
}

function deleteJabatan($db) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception("ID tidak valid");
        }
        
        // Soft delete
        $updateQuery = "UPDATE dynamic_jabatan SET is_active = 0, updated_at = NOW() WHERE id = :id";
        $stmt = $db->prepare($updateQuery);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Jabatan berhasil dihapus'
            ]);
        } else {
            throw new Exception("Gagal menghapus jabatan");
        }
        
    } catch (Exception $e) {
        throw new Exception("Delete jabatan failed: " . $e->getMessage());
    }
}

function searchJabatan($db) {
    try {
        $keyword = trim($_POST['keyword'] ?? '');
        
        if (empty($keyword)) {
            echo json_encode(['success' => true, 'data' => []]);
            return;
        }
        
        $query = "
            SELECT 
                id,
                nama_jabatan as nama,
                kode_jabatan as kode,
                level_jabatan as level,
                parent_id,
                is_active
            FROM dynamic_jabatan 
            WHERE is_active = 1 
            AND (nama_jabatan LIKE :keyword OR kode_jabatan LIKE :keyword)
            ORDER BY nama_jabatan
            LIMIT 20
        ";
        
        $stmt = $db->prepare($query);
        $keywordParam = "%$keyword%";
        $stmt->bindParam(':keyword', $keywordParam);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $results,
            'keyword' => $keyword
        ]);
        
    } catch (Exception $e) {
        throw new Exception("Search jabatan failed: " . $e->getMessage());
    }
}

function generateKodeJabatan($namaJabatan) {
    // Generate kode from nama jabatan
    $words = explode(' ', strtoupper($namaJabatan));
    $kode = '';
    
    foreach ($words as $word) {
        if (strlen($word) >= 3) {
            $kode .= substr($word, 0, 3);
        } else {
            $kode .= $word;
        }
        
        if (strlen($kode) >= 10) break;
    }
    
    return $kode ?: 'JBT';
}
?>
