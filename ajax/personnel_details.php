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

// Check if user can access personnel module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    echo '<div class="alert alert-danger">Anda tidak memiliki akses ke detail personel</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get personnel ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo '<div class="alert alert-danger">ID personel tidak valid</div>';
            exit();
        }
        
        // Get personnel data
        $stmt = $pdo->prepare("
            SELECT p.*, r.nama as rank_nama, pos.nama as position_nama, u.nama as unit_nama 
            FROM personel p 
            LEFT JOIN ranks r ON p.rank_id = r.id 
            LEFT JOIN positions pos ON p.position_id = pos.id 
            LEFT JOIN unit u ON p.unit_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $personnel = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$personnel) {
            echo '<div class="alert alert-danger">Personel tidak ditemukan</div>';
            exit();
        }
        
        ?>
        <div class="row">
            <div class="col-md-4">
                <img src="https://picsum.photos/seed/<?php echo $personnel['nrp']; ?>/150/150" alt="Avatar" class="img-fluid rounded-circle mb-3">
                <h5 class="text-center"><?php echo htmlspecialchars($personnel['nama']); ?></h5>
                <p class="text-center text-muted"><?php echo htmlspecialchars($personnel['nrp']); ?></p>
            </div>
            <div class="col-md-8">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>NRP:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['nrp']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['nama']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Pangkat:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['rank_nama'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Jabatan:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['position_nama'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Unit:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['unit_nama'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Telepon:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['phone'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td><?php echo htmlspecialchars($personnel['email'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <?php if ($personnel['is_active']): ?>
                                <span class="badge bg-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Tidak Aktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Dibuat:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($personnel['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Diperbarui:</strong></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($personnel['updated_at'])); ?></td>
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
