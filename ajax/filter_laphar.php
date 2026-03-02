<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../classes/Auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
$auth = new Auth((new Database())->getConnection());
$auth->requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get filter parameters
    $search = trim($_POST['search'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $shift = trim($_POST['shift'] ?? '');

    // Build query
    $whereConditions = [];
    $params = [];

    if (!empty($search)) {
        $whereConditions[] = "(l.lokasi LIKE ? OR l.kanit_nama LIKE ? OR l.kejadian_kasus LIKE ?)";
        $searchParam = '%' . $search . '%';
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
    }

    if (!empty($date)) {
        $whereConditions[] = "l.tanggal_laporan = ?";
        $params[] = $date;
    }

    if (!empty($shift)) {
        $whereConditions[] = "l.shift = ?";
        $params[] = $shift;
    }

    // Add user access control (only show own records unless admin)
    if ($currentUser['role'] !== 'super_admin' && $currentUser['role'] !== 'admin') {
        $whereConditions[] = "l.created_by = ?";
        $params[] = $currentUser['id'];
    }

    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // Get filtered Laphar records
    $stmt = $pdo->prepare("
        SELECT l.*, u.name as created_by_name
        FROM laphar l
        LEFT JOIN users u ON l.created_by = u.id
        {$whereClause}
        ORDER BY l.tanggal_laporan DESC, l.created_at DESC
        LIMIT 100
    ");

    $stmt->execute($params);
    $laphar_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format data for response
    $formatted_data = array_map(function($laphar) {
        return [
            'id' => $laphar['id'],
            'tanggal_laporan' => $laphar['tanggal_laporan'],
            'tanggal_laporan_formatted' => date('d/m/Y', strtotime($laphar['tanggal_laporan'])),
            'shift' => $laphar['shift'],
            'shift_text' => match($laphar['shift']) {
                'pagi' => 'Pagi (06:00-14:00)',
                'siang' => 'Siang (14:00-22:00)',
                'malam' => 'Malam (22:00-06:00)',
                default => ucfirst($laphar['shift'])
            },
            'lokasi' => $laphar['lokasi'],
            'kanit_nama' => $laphar['kanit_nama'],
            'jumlah_personel' => $laphar['jumlah_personel'],
            'jumlah_mobil' => $laphar['jumlah_mobil'],
            'kejadian_kasus' => $laphar['kejadian_kasus'],
            'situasi_umum' => $laphar['situasi_umum'],
            'cuaca' => $laphar['cuaca'],
            'catatan_rekomendasi' => $laphar['catatan_rekomendasi'],
            'created_by_name' => $laphar['created_by_name'],
            'created_at' => $laphar['created_at'],
            'kegiatan' => $laphar['kegiatan'] ? json_decode($laphar['kegiatan'], true) : [],
            'kegiatan_detail' => $laphar['kegiatan_detail'] ? json_decode($laphar['kegiatan_detail'], true) : []
        ];
    }, $laphar_list);

    echo json_encode([
        'success' => true,
        'laphar' => $formatted_data,
        'total' => count($formatted_data),
        'filters_applied' => [
            'search' => $search,
            'date' => $date,
            'shift' => $shift
        ]
    ]);

} catch (Exception $e) {
    error_log("Filter Laphar error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memfilter data Laphar: ' . $e->getMessage()
    ]);
}
?>
