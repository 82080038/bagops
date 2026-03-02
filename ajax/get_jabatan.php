<?php
// Get jabatan content for kantor

require_once '../config/database.php';

try {
    $db = (new Database())->getConnection();

    // Get current user
    session_start();
    $currentUser = isset($_SESSION['user']) ? $_SESSION['user'] : null;
    $userRole = $currentUser['role'] ?? 'user';

    $kantorId = $_POST['kantor_id'] ?? 0;

    if (!$kantorId) {
        echo json_encode(['success' => false, 'message' => 'Kantor ID tidak valid']);
        exit;
    }

    // Get kantor info from units table
    $kantorStmt = $db->prepare("SELECT * FROM units WHERE id = ?");
    $kantorStmt->execute([$kantorId]);
    $kantor = $kantorStmt->fetch(PDO::FETCH_ASSOC);

    if (!$kantor) {
        echo json_encode(['success' => false, 'message' => 'Kantor tidak ditemukan']);
        exit;
    }

    // Map tipe to type
    $jenis = $kantor['tipe'];
    $typeMap = [
        'POLRES' => 'polres',
        'POLSEK' => 'polsek',
        'POSLIS' => 'polsek'
    ];
    $kantorType = $typeMap[$jenis] ?? 'polres';

    // Get required positions
    $reqStmt = $db->prepare("SELECT jabatan FROM required_positions WHERE kantor_type = ? ORDER BY jabatan");
    $reqStmt->execute([$kantorType]);
    $required = $reqStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get positions for kantor using master_jabatan
    $posStmt = $db->prepare("
        SELECT kp.*, mj.jabatan, mj.unsur, p.nama as personel_nama 
        FROM kantor_positions kp 
        LEFT JOIN master_jabatan mj ON kp.master_jabatan_id = mj.id 
        LEFT JOIN personel p ON kp.personel_id = p.id 
        WHERE kp.kantor_id = ? 
        ORDER BY mj.unsur, mj.jabatan
    ");
    $posStmt->execute([$kantorId]);
    $positions = $posStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get personel for assign dropdown
    $persStmt = $db->prepare("SELECT id, nama FROM personel WHERE unit_id = ? ORDER BY nama");
    $persStmt->execute([$kantorId]);
    $personel = $persStmt->fetchAll(PDO::FETCH_KEY_PAIR);

    ob_start();
    ?>
    <div class="row mb-3">
        <div class="col-12">
            <h5>Jabatan untuk Kantor: <?php echo htmlspecialchars($kantor['nama']); ?></h5>
            <p>Tipe: <?php echo htmlspecialchars($jenis); ?> | Jumlah Jabatan: <?php echo count($positions); ?></p>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Unsur</th>
                            <th>Jabatan</th>
                            <th>Personel Assigned</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $currentUnsur = '';
                        foreach ($positions as $pos): 
                            if ($currentUnsur != $pos['unsur']) {
                                $currentUnsur = $pos['unsur'];
                                echo '<tr><td colspan="5" class="table-info"><strong>' . htmlspecialchars($pos['unsur']) . '</strong></td></tr>';
                            }
                        ?>
                            <tr>
                                <td></td>
                                <td><?php echo htmlspecialchars($pos['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($pos['personel_nama'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $pos['is_active'] ? 'success' : 'danger'; ?>">
                                        <?php echo $pos['is_active'] ? 'Aktif' : 'Non-Aktif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Assign dropdown -->
                                    <select class="form-select form-select-sm d-inline-block w-auto me-2" onchange="assignPersonel(<?php echo $pos['id']; ?>, this.value)">
                                        <option value="">Pilih Personel</option>
                                        <?php foreach ($personel as $pid => $nama): ?>
                                            <option value="<?php echo $pid; ?>" <?php echo $pos['personel_id'] == $pid ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($nama); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <!-- Toggle active if super admin -->
                                    <?php if ($userRole == 'super_admin'): ?>
                                        <button class="btn btn-sm btn-outline-<?php echo $pos['is_active'] ? 'danger' : 'success'; ?>"
                                                onclick="toggleJabatan(<?php echo $pos['id']; ?>, <?php echo $pos['is_active'] ? 0 : 1; ?>)">
                                            <i class="fas fa-<?php echo $pos['is_active'] ? 'times' : 'check'; ?>"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    function assignPersonel(positionId, personelId) {
        $.ajax({
            url: 'ajax/assign_personel.php',
            method: 'POST',
            data: { position_id: positionId, personel_id: personelId },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Gagal assign personel: ' + response.message);
                }
            },
            error: function() {
                alert('Gagal assign personel');
            }
        });
    }

    function toggleJabatan(positionId, isActive) {
        $.ajax({
            url: 'ajax/toggle_jabatan.php',
            method: 'POST',
            data: { position_id: positionId, is_active: isActive },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Gagal toggle jabatan: ' + response.message);
                }
            },
            error: function() {
                alert('Gagal toggle jabatan');
            }
        });
    }
    </script>
    <?php
    $content = ob_get_clean();

    echo json_encode(['success' => true, 'content' => $content]);

} catch (Exception $e) {
    error_log("Error in get_jabatan.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
