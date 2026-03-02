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

// Check if user can access RENOPS module
$currentUser = $auth->getCurrentUser();
$userRole = $currentUser['role'] ?? 'user';

// Simple permission check
if (!in_array($userRole, ['super_admin', 'admin', 'kabag_ops', 'kaur_ops'])) {
    echo '<div class="alert alert-danger">Anda tidak memiliki akses ke detail RENOPS</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = (new Database())->getConnection();
        
        // Get RENOPS ID
        $id = $_POST['id'] ?? '';
        
        if (empty($id)) {
            echo '<div class="alert alert-danger">ID RENOPS tidak valid</div>';
            exit();
        }
        
        // Get RENOPS data
        $stmt = $pdo->prepare("
            SELECT r.*, e.title as event_title, e.type as event_type, e.start_at, e.end_at, e.location, e.latitude, e.longitude, e.risk_level, e.notes 
            FROM renops r 
            LEFT JOIN events e ON r.event_id = e.id 
            WHERE r.id = ?
        ");
        $stmt->execute([$id]);
        $renops = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$renops) {
            echo '<div class="alert alert-danger">RENOPS tidak ditemukan</div>';
            exit();
        }
        
        ?>
        <div class="row">
            <div class="col-md-12">
                <h4 class="mb-3">RENOPS - <?php echo htmlspecialchars($renops['doc_no'] ?? 'No Document'); ?></h4>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Event</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Event:</strong></td>
                                <td><?php echo htmlspecialchars($renops['event_title'] ?? 'Event ' . $renops['event_id']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Event ID:</strong></td>
                                <td><?php echo htmlspecialchars($renops['event_id']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi:</strong></td>
                                <td><?php echo htmlspecialchars($renops['location'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Mulai:</strong></td>
                                <td>
                                    <?php if ($renops['start_at']): ?>
                                        <?php echo date('d/m/Y H:i', strtotime($renops['start_at'])); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Selesai:</strong></td>
                                <td>
                                    <?php if ($renops['end_at']): ?>
                                        <?php echo date('d/m/Y H:i', strtotime($renops['end_at'])); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($renops['event_type']) {
                                        case 'pending':
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Pending';
                                            break;
                                        case 'planned':
                                            $statusClass = 'bg-secondary';
                                            $statusText = 'Direncanakan';
                                            break;
                                        case 'active':
                                            $statusClass = 'bg-primary';
                                            $statusText = 'Aktif';
                                            break;
                                        case 'completed':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Selesai';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                            $statusText = 'Unknown';
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Risiko:</strong></td>
                                <td><?php echo htmlspecialchars($renops['risk_level'] ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Dasar Perintah</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['command_basis'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Ringkasan Intelijen</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['intel_summary'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Tujuan Operasi</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['objectives'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Kekuatan</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['forces'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Rencana Komunikasi</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['comms_plan'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Rencana Logistik</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['logistics_plan'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Rencana Kontinjensi</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['contingency_plan'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Koordinasi</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['coordination'] ?? '-')); ?></p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Dokumen</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Nomor Dokumen:</strong></td>
                                <td><?php echo htmlspecialchars($renops['doc_no'] ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($renops['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Diperbarui:</strong></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($renops['updated_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if (!empty($renops['notes'])): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Catatan</h6>
                    </div>
                    <div class="card-body">
                        <p><?php echo nl2br(htmlspecialchars($renops['notes'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($renops['latitude']) && !empty($renops['longitude'])): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Koordinat Lokasi</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Latitude:</strong></td>
                                <td><?php echo $renops['latitude']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Longitude:</strong></td>
                                <td><?php echo $renops['longitude']; ?></td>
                            </tr>
                        </table>
                        <div class="mt-3">
                            <a href="https://maps.google.com/?q=<?php echo $renops['latitude']; ?>,<?php echo $renops['longitude']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-map-marker-alt me-1"></i> Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
