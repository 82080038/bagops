<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

$pdo = (new Database())->getConnection();

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Simple routing
switch ($path) {
    case 'personel':
        handlePersonel($method, $pdo);
        break;
    case 'personel/search':
        handlePersonelSearch($method, $pdo);
        break;
    case 'personel/stats':
        handlePersonelStats($method, $pdo);
        break;
    case 'master/ranks':
        handleMasterRanks($method, $pdo);
        break;
    case 'master/jabatan':
        handleMasterJabatan($method, $pdo);
        break;
    case 'master/kantor':
        handleMasterKantor($method, $pdo);
        break;
    case 'master/status':
        handleMasterStatus($method, $pdo);
        break;
    case 'operations':
        handleOperations($method, $pdo);
        break;
    case 'renops':
        handleRenops($method, $pdo);
        break;
    case 'posko':
        handlePosko($method, $pdo);
        break;
    case 'analytics':
        handleAnalytics($method, $pdo);
        break;
    case 'mobile':
        handleMobile($method, $pdo);
        break;
    case 'dashboard':
        handleDashboard($method, $pdo);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Personel CRUD - Updated for new database structure
function handlePersonel($method, $pdo) {
    switch ($method) {
        case 'GET':
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $kantor = isset($_GET['kantor']) ? $_GET['kantor'] : '';
            $status = isset($_GET['status']) ? $_GET['status'] : '';
            $kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
            $pangkat = isset($_GET['pangkat']) ? $_GET['pangkat'] : '';
            $jabatan = isset($_GET['jabatan']) ? $_GET['jabatan'] : '';
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $offset = ($page - 1) * $limit;
            
            $query = "SELECT * FROM personel WHERE 1=1";
            $count_query = "SELECT COUNT(*) as total FROM personel WHERE 1=1";
            $params = [];
            
            if ($search) {
                $query .= " AND (nama LIKE ? OR nrp LIKE ?)";
                $count_query .= " AND (nama LIKE ? OR nrp LIKE ?)";
                $search_param = "%$search%";
                $params[] = $search_param;
                $params[] = $search_param;
            }
            if ($kantor) {
                $query .= " AND kantor = ?";
                $count_query .= " AND kantor = ?";
                $params[] = $kantor;
            }
            if ($status !== '') {
                $query .= " AND status_jabatan = ?";
                $count_query .= " AND status_jabatan = ?";
                $params[] = $status;
            }
            if ($kategori) {
                $query .= " AND kategori_personil = ?";
                $count_query .= " AND kategori_personil = ?";
                $params[] = $kategori;
            }
            if ($pangkat) {
                $query .= " AND pangkat = ?";
                $count_query .= " AND pangkat = ?";
                $params[] = $pangkat;
            }
            if ($jabatan) {
                $query .= " AND jabatan = ?";
                $count_query .= " AND jabatan = ?";
                $params[] = $jabatan;
            }
            
            $query .= " ORDER BY nama ASC LIMIT $limit OFFSET $offset";
            
            // Get total count
            $stmt = $pdo->prepare($count_query);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Get data
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $personel = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $personel,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                INSERT INTO personel (nrp, nama, pangkat, jabatan, jabatan_asli, unit, kantor, 
                                   status_jabatan, kategori_personil, keterangan, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())
            ");
            
            $success = $stmt->execute([
                $data['nrp'],
                $data['nama'],
                $data['pangkat'] ?? null,
                $data['jabatan'] ?? null,
                $data['jabatan_asli'] ?? $data['jabatan'] ?? null,
                $data['unit'] ?? null,
                $data['kantor'] ?? null,
                $data['status_jabatan'] ?? 'DEFINITIF',
                $data['kategori_personil'] ?? 'POLRI',
                $data['keterangan'] ?? null
            ]);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Personel berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan personel']);
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            
            $stmt = $pdo->prepare("
                UPDATE personel 
                SET nrp = ?, nama = ?, pangkat = ?, jabatan = ?, jabatan_asli = ?, 
                    unit = ?, kantor = ?, status_jabatan = ?, kategori_personil = ?, 
                    keterangan = ?, is_active = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            $success = $stmt->execute([
                $data['nrp'],
                $data['nama'],
                $data['pangkat'] ?? null,
                $data['jabatan'] ?? null,
                $data['jabatan_asli'] ?? $data['jabatan'] ?? null,
                $data['unit'] ?? null,
                $data['kantor'] ?? null,
                $data['status_jabatan'] ?? 'DEFINITIF',
                $data['kategori_personil'] ?? 'POLRI',
                $data['keterangan'] ?? null,
                $data['is_active'] ?? 1,
                $id
            ]);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Personel berhasil diperbarui']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui personel']);
            }
            break;
            
        case 'DELETE':
            $id = $_GET['id'];
            
            $stmt = $pdo->prepare("DELETE FROM personel WHERE id = ?");
            $success = $stmt->execute([$id]);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Personel berhasil dihapus']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus personel']);
            }
            break;
    }
}

// Personel Search - Advanced search functionality
function handlePersonelSearch($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    
    $stmt = $pdo->prepare("
        SELECT id, nrp, nama, pangkat, jabatan, jabatan_asli, kantor, status_jabatan, kategori_personil
        FROM personel 
        WHERE (nama LIKE ? OR nrp LIKE ? OR jabatan LIKE ? OR kantor LIKE ?)
        AND is_active = 1
        ORDER BY 
            CASE 
                WHEN nama LIKE ? THEN 1
                WHEN nrp LIKE ? THEN 2
                ELSE 3
            END,
            nama ASC
        LIMIT ?
    ");
    
    $search_param = "%$q%";
    $exact_param = "$q%";
    
    $stmt->execute([
        $search_param, $search_param, $search_param, $search_param,
        $exact_param, $exact_param,
        $limit
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $results,
        'query' => $q
    ]);
}

// Personel Statistics
function handlePersonelStats($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $stats = [];
    
    // Total by kategori
    $stmt = $pdo->query("SELECT kategori_personil, COUNT(*) as count FROM personel WHERE is_active = 1 GROUP BY kategori_personil");
    $stats['by_kategori'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total by status jabatan
    $stmt = $pdo->query("SELECT status_jabatan, COUNT(*) as count FROM personel WHERE is_active = 1 GROUP BY status_jabatan");
    $stats['by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total by pangkat
    $stmt = $pdo->query("SELECT pangkat, COUNT(*) as count FROM personel WHERE is_active = 1 AND pangkat IS NOT NULL GROUP BY pangkat ORDER BY count DESC");
    $stats['by_pangkat'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total by jabatan
    $stmt = $pdo->query("SELECT jabatan, COUNT(*) as count FROM personel WHERE is_active = 1 AND jabatan IS NOT NULL GROUP BY jabatan ORDER BY count DESC LIMIT 10");
    $stats['by_jabatan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total by kantor
    $stmt = $pdo->query("SELECT kantor, COUNT(*) as count FROM personel WHERE is_active = 1 AND kantor IS NOT NULL GROUP BY kantor ORDER BY count DESC");
    $stats['by_kantor'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Overall totals
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
    $stats['total_active'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personel WHERE is_active = 0");
    $stats['total_inactive'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
}

// Master Data - Ranks
function handleMasterRanks($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $stmt = $pdo->query("SELECT * FROM ranks ORDER BY level ASC");
    $ranks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $ranks
    ]);
}

// Master Data - Jabatan
function handleMasterJabatan($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $stmt = $pdo->query("SELECT * FROM m_jabatan ORDER BY level_jabatan ASC");
    $jabatan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $jabatan
    ]);
}

// Master Data - Kantor
function handleMasterKantor($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $stmt = $pdo->query("SELECT * FROM kantor ORDER BY nama_kantor ASC");
    $kantor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $kantor
    ]);
}

// Master Data - Status Jabatan
function handleMasterStatus($method, $pdo) {
    if ($method !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }
    
    $stmt = $pdo->query("SELECT * FROM master_status_jabatan ORDER BY status_jabatan ASC");
    $status = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $status
    ]);
}

// Operations CRUD - Keep existing
function handleOperations($method, $pdo) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM api_operations ORDER BY created_at DESC");
            $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $operations,
                'total' => count($operations)
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                INSERT INTO api_operations (kode_operasi, nama_operasi, jenis_operasi, status, waktu_mulai, deskripsi, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $data['kode_operasi'],
                $data['nama_operasi'],
                $data['jenis_operasi'],
                $data['status'],
                $data['waktu_mulai'],
                $data['deskripsi'],
                $data['created_by'] ?? 1
            ]);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Operasi berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan operasi']);
            }
            break;
    }
}

// RENOPS CRUD
function handleRenops($method, $pdo) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM api_renops ORDER BY created_at DESC");
            $renops = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $renops,
                'total' => count($renops)
            ]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare("
                INSERT INTO api_renops (kode_renops, nomor_renops, tanggal_renops, dasar_hukum, intel_singkat, status, created_by, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $success = $stmt->execute([
                $data['kode_renops'],
                $data['nomor_renops'],
                $data['tanggal_renops'],
                $data['dasar_hukum'],
                $data['intel_singkat'],
                $data['status'],
                $data['created_by'] ?? 1
            ]);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'RENOPS berhasil ditambahkan']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Gagal menambahkan RENOPS']);
            }
            break;
    }
}

// POSKO CRUD
function handlePosko($method, $pdo) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM api_posko ORDER BY created_at DESC");
            $posko = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $posko,
                'total' => count($posko)
            ]);
            break;
    }
}

// Analytics CRUD
function handleAnalytics($method, $pdo) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM api_analytics ORDER BY recorded_date DESC");
            $analytics = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $analytics,
                'total' => count($analytics)
            ]);
            break;
    }
}

// Mobile CRUD
function handleMobile($method, $pdo) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT * FROM api_mobile_devices ORDER BY created_at DESC");
            $mobile = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $mobile,
                'total' => count($mobile)
            ]);
            break;
    }
}

// Dashboard Data - Updated for new database structure
function handleDashboard($method, $pdo) {
    switch ($method) {
        case 'GET':
            // Get dashboard statistics
            $stats = [];
            
            // Total personel
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_personel'] = $result['total'];
            
            // Total by kategori
            $stmt = $pdo->query("SELECT kategori_personil, COUNT(*) as count FROM personel WHERE is_active = 1 GROUP BY kategori_personil");
            $stats['by_kategori'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Total by status jabatan
            $stmt = $pdo->query("SELECT status_jabatan, COUNT(*) as count FROM personel WHERE is_active = 1 GROUP BY status_jabatan");
            $stats['by_status_jabatan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top pangkat
            $stmt = $pdo->query("SELECT pangkat, COUNT(*) as count FROM personel WHERE is_active = 1 AND pangkat IS NOT NULL GROUP BY pangkat ORDER BY count DESC LIMIT 5");
            $stats['top_pangkat'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top kantor
            $stmt = $pdo->query("SELECT kantor, COUNT(*) as count FROM personel WHERE is_active = 1 AND kantor IS NOT NULL GROUP BY kantor ORDER BY count DESC LIMIT 5");
            $stats['top_kantor'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Top jabatan
            $stmt = $pdo->query("SELECT jabatan, COUNT(*) as count FROM personel WHERE is_active = 1 AND jabatan IS NOT NULL GROUP BY jabatan ORDER BY count DESC LIMIT 5");
            $stats['top_jabatan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Set default values for missing tables
            $stats['total_operations'] = 0;
            $stats['total_renops'] = 0;
            $stats['total_posko'] = 0;
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
    }
}
?>
