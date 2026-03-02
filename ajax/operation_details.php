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

// Check if user can access operations module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    echo '<div class="alert alert-danger">Anda tidak memiliki akses ke detail operasi</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get operation ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo '<div class="alert alert-danger">ID operasi tidak valid</div>';
            exit();
        }
        
        // Get operation data
        $stmt = $pdo->prepare("
            SELECT o.*, u.nama as commander_name, u.nrp as commander_nrp 
            FROM operations o 
            LEFT JOIN users u ON o.commander_id = u.id 
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $operation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$operation) {
            echo '<div class="alert alert-danger">Operasi tidak ditemukan</div>';
            exit();
        }
        
        ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3"><?php echo htmlspecialchars($operation['title']); ?></h4>
                
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Komandan:</strong></td>
                        <td><?php echo htmlspecialchars($operation['commander_name'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>NRP Komandan:</strong></td>
                        <td><?php echo htmlspecialchars($operation['commander_nrp'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Lokasi:</strong></td>
                        <td><?php echo htmlspecialchars($operation['location']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <?php
                            $statusClass = '';
                            $statusText = '';
                            switch ($operation['status']) {
                                case 'planned':
                                    $statusClass = 'bg-warning';
                                    $statusText = 'Direncanakan';
                                    break;
                                case 'ongoing':
                                    $statusClass = 'bg-primary';
                                    $statusText = 'Sedang Berlangsung';
                                    break;
                                case 'completed':
                                    $statusClass = 'bg-success';
                                    $statusText = 'Selesai';
                                    break;
                                default:
                                    $statusClass = 'bg-secondary';
                                    $statusText = $operation['status'];
                            }
                            ?>
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Mulai:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($operation['start_date'] . ' ' . $operation['start_time'])); ?></td>
                    </tr>
                    <?php if ($operation['end_date']): ?>
                    <tr>
                        <td><strong>Tanggal Selesai:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($operation['end_date'] . ' ' . $operation['end_time'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>Deskripsi:</strong></td>
                        <td><?php echo nl2br(htmlspecialchars($operation['description'] ?? '-')); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat Oleh:</strong></td>
                        <td><?php echo htmlspecialchars($operation['created_by']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Dibuat:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($operation['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Diperbarui:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($operation['updated_at'])); ?></td>
                    </tr>
                </table>
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
