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

$id = $_POST['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid assignment ID']);
    exit;
}

try {
    $pdo = (new Database())->getConnection();
    $currentUser = $auth->getCurrentUser();

    // Get assignment details with security check (only allow user to see their own assignments)
    $stmt = $pdo->prepare("
        SELECT a.*, e.title as event_title, e.description as event_description,
               e.start_at, e.end_at, e.location, e.status as event_status,
               r.nama as role_name, r.deskripsi as role_description,
               p.nama as commander_name, p.nrp as commander_nrp,
               u.nama as unit_name
        FROM assignments a
        JOIN events e ON a.event_id = e.id
        LEFT JOIN assignment_roles r ON a.role_id = r.id
        LEFT JOIN personel p ON e.commander_id = p.id
        LEFT JOIN units u ON a.unit_id = u.id
        WHERE a.id = ? AND a.user_id = ?
    ");
    $stmt->execute([$id, $currentUser['id']]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$assignment) {
        echo json_encode(['success' => false, 'message' => 'Assignment not found or access denied']);
        exit;
    }

    // Generate HTML content for the modal
    ob_start();
    ?>
    <div class="row">
        <div class="col-md-8">
            <h6 class="text-primary mb-3"><?php echo htmlspecialchars($assignment['event_title']); ?></h6>

            <div class="mb-3">
                <strong>Deskripsi Operasi:</strong><br>
                <p class="mt-1"><?php echo nl2br(htmlspecialchars($assignment['event_description'] ?? 'Tidak ada deskripsi')); ?></p>
            </div>

            <div class="row mb-3">
                <div class="col-sm-6">
                    <strong>Waktu Mulai:</strong><br>
                    <span class="text-primary"><?php echo date('d F Y, H:i', strtotime($assignment['start_at'])); ?> WIB</span>
                </div>
                <div class="col-sm-6">
                    <strong>Waktu Selesai:</strong><br>
                    <span class="text-primary"><?php echo date('d F Y, H:i', strtotime($assignment['end_at'])); ?> WIB</span>
                </div>
            </div>

            <div class="mb-3">
                <strong>Lokasi:</strong><br>
                <span class="badge bg-info"><?php echo htmlspecialchars($assignment['location'] ?? 'Tidak ditentukan'); ?></span>
            </div>

            <div class="mb-3">
                <strong>Status Operasi:</strong><br>
                <?php
                $statusClass = match($assignment['event_status']) {
                    'ACTIVE' => 'bg-success',
                    'COMPLETED' => 'bg-primary',
                    'CANCELLED' => 'bg-danger',
                    default => 'bg-secondary'
                };
                ?>
                <span class="badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($assignment['event_status'] ?? 'UNKNOWN'); ?></span>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Penugasan Anda</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Peran:</strong><br>
                        <span class="text-primary"><?php echo htmlspecialchars($assignment['role_name'] ?? 'Tidak ditentukan'); ?></span>
                    </div>

                    <?php if ($assignment['role_description']): ?>
                    <div class="mb-2">
                        <strong>Deskripsi Peran:</strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($assignment['role_description']); ?></small>
                    </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <strong>Unit:</strong><br>
                        <span class="badge bg-light text-dark"><?php echo htmlspecialchars($assignment['unit_name'] ?? 'Tidak ditentukan'); ?></span>
                    </div>

                    <div class="mb-0">
                        <strong>Komandan:</strong><br>
                        <span><?php echo htmlspecialchars($assignment['commander_name'] ?? 'Tidak ditentukan'); ?></span>
                        <?php if ($assignment['commander_nrp']): ?>
                        <br><small class="text-muted">NRP: <?php echo htmlspecialchars($assignment['commander_nrp']); ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <div class="d-grid">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="printAssignment(<?php echo $assignment['id']; ?>)">
                        <i class="fas fa-print me-1"></i>
                        Cetak Penugasan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printAssignment(assignmentId) {
            // Open print window with assignment details
            var printWindow = window.open('print_assignment.php?id=' + assignmentId, '_blank');
            if (printWindow) {
                printWindow.focus();
                // Wait for content to load then print
                setTimeout(function() {
                    printWindow.print();
                }, 1000);
            } else {
                alert('Popup blocker mungkin aktif. Silakan izinkan popup untuk situs ini.');
            }
        }
    </script>
    <?php

    $content = ob_get_clean();

    echo json_encode([
        'success' => true,
        'content' => $content
    ]);

} catch (Exception $e) {
    error_log("Get assignment error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Terjadi kesalahan saat memuat detail penugasan'
    ]);
}
?>
