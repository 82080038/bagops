<?php
/**
 * Assignments Page - Task Management System
 * Full CRUD for task assignment and tracking
 */

// Load assignment manager
require_once 'classes/AssignmentManager.php';

try {
    $assignmentManager = new AssignmentManager($GLOBALS['db'], $GLOBALS['auth']);
    $assignments = $assignmentManager->getAssignments();
    $stats = $assignmentManager->getAssignmentStats();
    $workload = $assignmentManager->getPersonnelWorkload();
    $templates = $assignmentManager->getTemplates();
    $overdue = $assignmentManager->getOverdueAssignments();
    $upcoming = $assignmentManager->getUpcomingAssignments();
    
} catch (Exception $e) {
    error_log("Assignments Data Error: " . $e->getMessage());
    $assignments = [];
    $stats = [
        'total_assignments' => 0,
        'assigned' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'overdue' => 0,
        'overdue_count' => 0
    ];
    $workload = [];
    $templates = [];
    $overdue = [];
    $upcoming = [];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-tasks me-2"></i>Manajemen Tugas</h2>
        <p class="text-muted">Sistem penugasan dan tracking tugas personel POLRES SAMOSIR</p>
        <small class="text-muted">Template: assignments.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Tugas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_assignments']); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diproses</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['in_progress']); ?></div>
                        <small class="text-muted">In Progress</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-spinner fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['completed']); ?></div>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Terlambat</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['overdue_count']); ?></div>
                        <small class="text-muted">Overdue</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Cards -->
<div class="row mb-4">
    <?php if (!empty($overdue)): ?>
    <div class="col-md-6">
        <div class="card border-left-danger shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Tugas Terlambat (<?php echo count($overdue); ?>)
                </h6>
            </div>
            <div class="card-body">
                <?php foreach (array_slice($overdue, 0, 3) as $task): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong><?php echo htmlspecialchars($task['judul_assignment']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($task['personel_nama']); ?> | Terlambat <?php echo $task['days_overdue']; ?> hari</small>
                    </div>
                    <span class="badge bg-danger"><?php echo $task['days_overdue']; ?> hari</span>
                </div>
                <?php endforeach; ?>
                <?php if (count($overdue) > 3): ?>
                <small class="text-muted">Dan <?php echo count($overdue) - 3; ?> tugas lainnya...</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($upcoming)): ?>
    <div class="col-md-6">
        <div class="card border-left-info shadow">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-calendar-alt me-2"></i>Tugas Akan Datang (<?php echo count($upcoming); ?>)
                </h6>
            </div>
            <div class="card-body">
                <?php foreach (array_slice($upcoming, 0, 3) as $task): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong><?php echo htmlspecialchars($task['judul_assignment']); ?></strong>
                        <br><small class="text-muted"><?php echo htmlspecialchars($task['personel_nama']); ?> | <?php echo $task['days_until']; ?> hari lagi</small>
                    </div>
                    <span class="badge bg-info"><?php echo $task['days_until']; ?> hari</span>
                </div>
                <?php endforeach; ?>
                <?php if (count($upcoming) > 3): ?>
                <small class="text-muted">Dan <?php echo count($upcoming) - 3; ?> tugas lainnya...</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Tugas</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="showCreateAssignmentModal()">
                        <i class="fas fa-plus me-2"></i>Tambah Tugas
                    </button>
                    <button class="btn btn-success btn-sm" onclick="showTemplateModal()">
                        <i class="fas fa-clone me-2"></i>Gunakan Template
                    </button>
                    <button class="btn btn-info btn-sm" onclick="refreshAssignments()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="ditugaskan">Ditugaskan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterPrioritas">
                            <option value="">Semua Prioritas</option>
                            <option value="kritikal">Kritikal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="sedang">Sedang</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterPersonel">
                            <option value="">Semua Personel</option>
                            <?php foreach ($workload as $person): ?>
                            <option value="<?php echo $person['personel_id']; ?>"><?php echo htmlspecialchars($person['nama']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="filterAssignments()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Assignments Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="assignmentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Judul Tugas</th>
                                <th>Personel</th>
                                <th>Operasi</th>
                                <th>Tanggal</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assignments)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada tugas</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($assignment['judul_assignment']); ?></strong>
                                            <?php if (!empty($assignment['deskripsi'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($assignment['deskripsi'], 0, 50)) . '...'; ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($assignment['personel_nama']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($assignment['nrp']); ?> | <?php echo htmlspecialchars($assignment['pangkat']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($assignment['operation_id']): ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($assignment['kode_operasi']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($assignment['nama_operasi']); ?></small>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-muted">Tidak terkait operasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y', strtotime($assignment['tanggal_mulai'])); ?>
                                            <?php if ($assignment['tanggal_selesai']): ?>
                                            - <?php echo date('d/m/Y', strtotime($assignment['tanggal_selesai'])); ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($assignment['prioritas']) {
                                                'kritikal' => 'danger',
                                                'tinggi' => 'warning',
                                                'sedang' => 'info',
                                                'rendah' => 'success',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($assignment['prioritas']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo match($assignment['status_assignment']) {
                                                'ditugaskan' => 'primary',
                                                'diproses' => 'info',
                                                'selesai' => 'success',
                                                'terlambat' => 'danger',
                                                'dibatalkan' => 'secondary',
                                                default => 'light'
                                            };
                                        ?>">
                                            <?php echo ucfirst($assignment['status_assignment']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?php echo $assignment['progress_percent']; ?>%"
                                                 aria-valuenow="<?php echo $assignment['progress_percent']; ?>" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                <?php echo $assignment['progress_percent']; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" title="Detail" onclick="viewAssignment(<?php echo $assignment['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" title="Edit" onclick="editAssignment(<?php echo $assignment['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-success" title="Update Status" onclick="updateStatus(<?php echo $assignment['id']; ?>)">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            <button class="btn btn-danger" title="Hapus" onclick="deleteAssignment(<?php echo $assignment['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalTitle">Tambah Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignmentForm">
                    <input type="hidden" id="assignmentId" name="id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="judul_assignment" class="form-label">Judul Tugas *</label>
                                <input type="text" class="form-control" id="judul_assignment" name="judul_assignment" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="prioritas" class="form-label">Prioritas *</label>
                                <select class="form-select" id="prioritas" name="prioritas" required>
                                    <option value="">Pilih Prioritas</option>
                                    <option value="kritikal">Kritikal</option>
                                    <option value="tinggi">Tinggi</option>
                                    <option value="sedang">Sedang</option>
                                    <option value="rendah">Rendah</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Tugas</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="personel_id" class="form-label">Personel *</label>
                                <select class="form-select" id="personel_id" name="personel_id" required>
                                    <option value="">Pilih Personel</option>
                                    <?php foreach ($workload as $person): ?>
                                    <option value="<?php echo $person['personel_id']; ?>">
                                        <?php echo htmlspecialchars($person['nama'] . ' - ' . $person['pangkat']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="operation_id" class="form-label">Operasi (Opsional)</label>
                                <select class="form-select" id="operation_id" name="operation_id">
                                    <option value="">Tidak terkait operasi</option>
                                    <?php
                                    $stmt = $GLOBALS['db']->prepare("SELECT id, kode_operasi, nama_operasi FROM operations ORDER BY kode_operasi");
                                    $stmt->execute();
                                    $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($operations as $op):
                                    ?>
                                    <option value="<?php echo $op['id']; ?>">
                                        <?php echo htmlspecialchars($op['kode_operasi'] . ' - ' . $op['nama_operasi']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai *</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status_assignment" class="form-label">Status</label>
                                <select class="form-select" id="status_assignment" name="status_assignment">
                                    <option value="ditugaskan">Ditugaskan</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="terlambat">Terlambat</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="progress_percent" class="form-label">Progress (%)</label>
                                <input type="number" class="form-control" id="progress_percent" name="progress_percent" min="0" max="100" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="catatan" name="catatan" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveAssignment()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Template Modal -->
<div class="modal fade" id="templateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gunakan Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="templateForm">
                    <div class="mb-3">
                        <label for="template_id" class="form-label">Pilih Template *</label>
                        <select class="form-select" id="template_id" name="template_id" required>
                            <option value="">Pilih Template</option>
                            <?php foreach ($templates as $template): ?>
                            <option value="<?php echo $template['id']; ?>">
                                <?php echo htmlspecialchars($template['nama_template']); ?>
                                <?php if (!empty($template['deskripsi_template'])): ?>
                                - <?php echo htmlspecialchars(substr($template['deskripsi_template'], 0, 30)) . '...'; ?>
                                <?php endif; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="template_personel_id" class="form-label">Personel *</label>
                                <select class="form-select" id="template_personel_id" name="personel_id" required>
                                    <option value="">Pilih Personel</option>
                                    <?php foreach ($workload as $person): ?>
                                    <option value="<?php echo $person['personel_id']; ?>">
                                        <?php echo htmlspecialchars($person['nama'] . ' - ' . $person['pangkat']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="template_tanggal_mulai" class="form-label">Tanggal Mulai *</label>
                                <input type="date" class="form-control" id="template_tanggal_mulai" name="tanggal_mulai" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="template_tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="template_tanggal_selesai" name="tanggal_selesai">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="createFromTemplate()">Buat Tugas</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    <input type="hidden" id="statusAssignmentId" name="assignment_id">
                    
                    <div class="mb-3">
                        <label for="new_status" class="form-label">Status Baru *</label>
                        <select class="form-select" id="new_status" name="status_assignment" required>
                            <option value="ditugaskan">Ditugaskan</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="new_progress" class="form-label">Progress (%)</label>
                        <input type="number" class="form-control" id="new_progress" name="progress_percent" min="0" max="100" value="0">
                    </div>

                    <div class="mb-3">
                        <label for="new_catatan" class="form-label">Catatan</label>
                        <textarea class="form-control" id="new_catatan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveStatusUpdate()">Update</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Assignments page loaded with full task management functionality');
    
    // Initialize DataTable
    $('#assignmentsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']]
    });
    
    // Filter handlers
    $('#filterStatus, #filterPrioritas, #filterPersonel').on('change', function() {
        filterAssignments();
    });
});

function showCreateAssignmentModal() {
    $('#assignmentModalTitle').text('Tambah Tugas');
    $('#assignmentForm')[0].reset();
    $('#assignmentId').val('');
    $('#status_assignment').val('ditugaskan');
    $('#progress_percent').val(0);
    var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
    modal.show();
}

function editAssignment(id) {
    $.ajax({
        url: 'ajax/assignments.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var assignment = response.data;
                $('#assignmentModalTitle').text('Edit Tugas');
                $('#assignmentId').val(assignment.id);
                $('#judul_assignment').val(assignment.judul_assignment);
                $('#deskripsi').val(assignment.deskripsi);
                $('#personel_id').val(assignment.personel_id);
                $('#operation_id').val(assignment.operation_id);
                $('#tanggal_mulai').val(assignment.tanggal_mulai);
                $('#tanggal_selesai').val(assignment.tanggal_selesai);
                $('#prioritas').val(assignment.prioritas);
                $('#status_assignment').val(assignment.status_assignment);
                $('#progress_percent').val(assignment.progress_percent);
                $('#catatan').val(assignment.catatan);
                
                var modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
                modal.show();
            } else {
                alert('Gagal memuat data tugas: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function saveAssignment() {
    var form = $('#assignmentForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', $('#assignmentId').val() ? 'update' : 'create');
    
    $.ajax({
        url: 'ajax/assignments.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('assignmentModal')).hide();
                location.reload();
            } else {
                alert('Gagal menyimpan tugas: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function deleteAssignment(id) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        $.ajax({
            url: 'ajax/assignments.php',
            method: 'POST',
            data: {action: 'delete', id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Gagal menghapus tugas: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    }
}

function viewAssignment(id) {
    // For now, just edit. In future, show detailed view
    editAssignment(id);
}

function updateStatus(id) {
    $('#statusAssignmentId').val(id);
    $('#statusForm')[0].reset();
    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function saveStatusUpdate() {
    var form = $('#statusForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    $.ajax({
        url: 'ajax/assignments.php',
        method: 'POST',
        data: {
            action: 'update_status',
            id: $('#statusAssignmentId').val(),
            status_assignment: $('#new_status').val(),
            progress_percent: $('#new_progress').val(),
            catatan: $('#new_catatan').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                location.reload();
            } else {
                alert('Gagal update status: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function showTemplateModal() {
    $('#templateForm')[0].reset();
    var modal = new bootstrap.Modal(document.getElementById('templateModal'));
    modal.show();
}

function createFromTemplate() {
    var form = $('#templateForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    $.ajax({
        url: 'ajax/assignments.php',
        method: 'POST',
        data: {
            action: 'create_from_template',
            template_id: $('#template_id').val(),
            personel_id: $('#template_personel_id').val(),
            tanggal_mulai: $('#template_tanggal_mulai').val(),
            tanggal_selesai: $('#template_tanggal_selesai').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('templateModal')).hide();
                location.reload();
            } else {
                alert('Gagal membuat tugas dari template: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function filterAssignments() {
    var filters = {
        status_assignment: $('#filterStatus').val(),
        prioritas: $('#filterPrioritas').val(),
        personel_id: $('#filterPersonel').val()
    };
    
    $.ajax({
        url: 'ajax/assignments.php',
        method: 'POST',
        data: {action: 'list', ...filters},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateAssignmentsTable(response.data);
            }
        },
        error: function() {
            alert('Error: Tidak dapat filter data');
        }
    });
}

function updateAssignmentsTable(data) {
    var tbody = $('#assignmentsTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">Tidak ada tugas</td></tr>');
        return;
    }
    
    data.forEach(function(assignment) {
        var prioritasBadge = getPrioritasBadge(assignment.prioritas);
        var statusBadge = getStatusBadge(assignment.status_assignment);
        
        var row = `
            <tr>
                <td>
                    <div>
                        <strong>${assignment.judul_assignment}</strong>
                        ${assignment.deskripsi ? '<br><small class="text-muted">' + assignment.deskripsi.substring(0, 50) + '...</small>' : ''}
                    </div>
                </td>
                <td>
                    <div>
                        <strong>${assignment.personel_nama}</strong>
                        <br><small class="text-muted">${assignment.nrp} | ${assignment.pangkat}</small>
                    </div>
                </td>
                <td>
                    ${assignment.operation_id ? 
                        `<div><strong>${assignment.kode_operasi}</strong><br><small class="text-muted">${assignment.nama_operasi}</small></div>` : 
                        '<span class="text-muted">Tidak terkait operasi</span>'}
                </td>
                <td>
                    <small>
                        ${new Date(assignment.tanggal_mulai).toLocaleDateString('id-ID')}
                        ${assignment.tanggal_selesai ? ' - ' + new Date(assignment.tanggal_selesai).toLocaleDateString('id-ID') : ''}
                    </small>
                </td>
                <td>${prioritasBadge}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: ${assignment.progress_percent}%"
                             aria-valuenow="${assignment.progress_percent}" 
                             aria-valuemin="0" aria-valuemax="100">
                            ${assignment.progress_percent}%
                        </div>
                    </div>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewAssignment(${assignment.id})"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editAssignment(${assignment.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-success" onclick="updateStatus(${assignment.id})"><i class="fas fa-sync"></i></button>
                        <button class="btn btn-danger" onclick="deleteAssignment(${assignment.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getPrioritasBadge(prioritas) {
    var badgeMap = {
        'kritikal': 'bg-danger',
        'tinggi': 'bg-warning',
        'sedang': 'bg-info',
        'rendah': 'bg-success'
    };
    return '<span class="badge ' + (badgeMap[prioritas] || 'bg-secondary') + '">' + prioritas.toUpperCase() + '</span>';
}

function getStatusBadge(status) {
    var badgeMap = {
        'ditugaskan': 'badge-primary',
        'diproses': 'badge-info',
        'selesai': 'badge-success',
        'terlambat': 'badge-danger',
        'dibatalkan': 'badge-secondary'
    };
    return '<span class="badge ' + (badgeMap[status] || 'badge-light') + '">' + status.replace('_', ' ').toUpperCase() + '</span>';
}

function refreshAssignments() {
    location.reload();
}
</script>
