<?php
// Start session and include required files
session_start();
require_once '../config/config.php';
require_once '../config/database.php';

// Check authentication
require_once '../classes/Auth.php';
$auth = new Auth((new Database())->getConnection());
if (!$auth->isLoggedIn()) {
    echo '<div class="alert alert-danger">Unauthorized</div>';
    exit();
}

// Check if user can access reports module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    echo '<div class="alert alert-danger">Anda tidak memiliki akses ke detail laporan</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get report ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo '<div class="alert alert-danger">ID laporan tidak valid</div>';
            exit();
        }
        
        // Get report data
        $stmt = $pdo->prepare("
            SELECT r.*, u.nama as created_by_name 
            FROM reports r 
            LEFT JOIN users u ON r.created_by = u.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$report) {
            echo '<div class="alert alert-danger">Laporan tidak ditemukan</div>';
            exit();
        }
        
        ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3"><?php echo htmlspecialchars($report['title']); ?></h4>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Laporan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Tipe Laporan:</strong></td>
                                <td>
                                    <?php
                                    $typeClass = '';
                                    $typeText = '';
                                    switch ($report['type']) {
                                        case 'bulanan':
                                            $typeClass = 'bg-info';
                                            $typeText = 'Bulanan';
                                            break;
                                        case 'mingguan':
                                            $typeClass = 'bg-success';
                                            $typeText = 'Mingguan';
                                            break;
                                        case 'harian':
                                            $typeClass = 'bg-primary';
                                            $typeText = 'Harian';
                                            break;
                                        case 'ops':
                                            $typeClass = 'bg-secondary';
                                            $typeText = 'Operasional';
                                            break;
                                        default:
                                            $typeClass = 'bg-secondary';
                                            $typeText = $report['type'] ?? 'Unknown';
                                    }
                                    ?>
                                    <span class="badge <?php echo $typeClass; ?>"><?php echo $typeText; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Periode:</strong></td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($report['period_start'])); ?>
                                    <?php if (!empty($report['period_end'])): ?>
                                        - <?php echo date('d/m/Y', strtotime($report['period_end'])); ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat Oleh:</strong></td>
                                <td><?php echo htmlspecialchars($report['created_by_name'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Dibuat:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Diperbarui:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($report['updated_at'])); ?></td>
                            </tr>
                            <?php if (!empty($report['file_path'])): ?>
                            <tr>
                                <td><strong>File:</strong></td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($report['file_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>Download File
                                    </a>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Deskripsi</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($report['description'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Isi Laporan</h6>
                    </div>
                    <div class="card-body">
                        <div style="white-space: pre-wrap;"><?php echo htmlspecialchars($report['content']); ?></div>
                    </div>
                </div>
                
                <?php if (!empty($report['file_path'])): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Lampiran</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt fa-2x me-3 text-primary"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-0"><?php echo basename($report['file_path']); ?></h6>
                                <p class="text-muted mb-0">File laporan</p>
                            </div>
                            <a href="<?php echo htmlspecialchars($report['file_path']); ?>" target="_blank" class="btn btn-primary">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Aksi</h6>
                    </div>
                    <div class="card-body">
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="downloadReport(<?php echo $report['id']; ?>)">
                                <i class="fas fa-download me-1"></i>Download Laporan
                            </button>
                            <?php if (!empty($report['file_path'])): ?>
                            <a href="<?php echo htmlspecialchars($report['file_path']); ?>" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-file-alt me-1"></i>Buka File
                            </a>
                            <?php endif; ?>
                            <?php if (in_array($userRole, ['super_admin', 'admin'])): ?>
                            <button class="btn btn-outline-danger" onclick="deleteReport(<?php echo $report['id']; ?>)">
                                <i class="fas fa-trash me-1"></i>Hapus
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request method</div>';
}
?>
