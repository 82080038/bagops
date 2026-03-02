<?php
/**
 * Assignments Page Template
 */

// Get assignments data from database
try {
    $stmt = $GLOBALS['db']->prepare("
        SELECT a.*, p.nama as personel_nama, o.nama_operasi
        FROM assignments a
        LEFT JOIN personel p ON a.personel_id = p.id
        LEFT JOIN operations o ON a.operation_id = o.id
        WHERE a.is_active = 1
        ORDER BY a.created_at DESC
        LIMIT 50
    ");
    $stmt->execute();
    $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stmt = $GLOBALS['db']->prepare("
        SELECT 
            COUNT(*) as total_assignments,
            SUM(CASE WHEN status = 'assigned' THEN 1 ELSE 0 END) as assigned_count,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_count,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count
        FROM assignments
        WHERE is_active = 1
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Assignments Data Error: " . $e->getMessage());
    $assignments = [];
    $stats = ['total_assignments' => 0, 'assigned_count' => 0, 'completed_count' => 0, 'pending_count' => 0];
}
?>


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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ditugaskan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['assigned_count']); ?></div>
                        <small class="text-muted">Sedang Berjalan</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['completed_count']); ?></div>
                        <small class="text-muted">Telah Selesai</small>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['pending_count']); ?></div>
                        <small class="text-muted">Menunggu Penugasan</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Tugas (<?php echo number_format(count($assignments)); ?> records)</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="createAssignment()">
                        <i class="fas fa-plus"></i> Buat Tugas
                    </button>
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="assignmentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Personel</th>
                                <th>Operasi</th>
                                <th>Role Assignment</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($assignments)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-tasks fa-3x mb-3 text-muted"></i><br>
                                        Belum ada data tugas<br>
                                        <small>Buat tugas untuk memulai</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($assignments as $assignment): ?>
                                    <?php
                                    $statusBadge = 'secondary';
                                    $statusText = 'Unknown';
                                    switch ($assignment['status']) {
                                        case 'assigned':
                                            $statusBadge = 'primary';
                                            $statusText = 'Ditugaskan';
                                            break;
                                        case 'completed':
                                            $statusBadge = 'success';
                                            $statusText = 'Selesai';
                                            break;
                                        case 'pending':
                                            $statusBadge = 'warning';
                                            $statusText = 'Pending';
                                            break;
                                        case 'absent':
                                            $statusBadge = 'danger';
                                            $statusText = 'Tidak Hadir';
                                            break;
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($assignment['personel_nama'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['nama_operasi'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['role_assignment'] ?? '-'); ?></td>
                                        <td><span class="badge badge-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($assignment['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" title="Detail" onclick="viewAssignment(<?php echo $assignment['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" title="Edit" onclick="editAssignment(<?php echo $assignment['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-success" title="Complete" onclick="completeAssignment(<?php echo $assignment['id']; ?>)">
                                                    <i class="fas fa-check"></i>
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

<script>
$(document).ready(function() {
    // Check if table has real data (not just colspan row)
    var hasRealData = $('#assignmentsTable tbody tr').length > 1 || 
                      ($('#assignmentsTable tbody tr').length === 1 && 
                       $('#assignmentsTable tbody td[colspan]').length === 0);
    
    if (hasRealData) {
        // Initialize DataTable only if there's real data
        $('#assignmentsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columns: [
                { data: 0, title: "Personel" },
                { data: 1, title: "Operasi" },
                { data: 2, title: "Role Assignment" },
                { data: 3, title: "Status" },
                { data: 4, title: "Tanggal Dibuat" },
                { data: 5, title: "Aksi", orderable: false }
            ],
            language: {
                "lengthMenu": "Tampilkan _MENU_ data per halaman",
                "zeroRecords": "Tidak ada data yang ditemukan",
                "info": "Halaman _PAGE_ dari _PAGES_",
                "infoEmpty": "Tidak ada data tersedia",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "search": "Cari:",
                "paginate": {
                    "first": "Pertama",
                    "last": "Terakhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            }
        });
    }
});

// Assignment functions
function createAssignment() {
    // TODO: Show modal untuk buat tugas
    // Integration dengan ajax/save_assignment.php (perlu dibuat)
    alert("Fitur buat tugas akan segera tersedia");
}

function editAssignment(id) {
    // TODO: Load assignment data via ajax/get_assignment.php
    // Show modal dengan data yang sudah di-load
    // Integration dengan ajax/update_assignment.php (perlu dibuat)
    alert("Edit tugas ID: " + id + " akan segera tersedia");
}

function deleteAssignment(id) {
    if (confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
        // TODO: Integration dengan ajax/delete_assignment.php (perlu dibuat)
        $.ajax({
            url: 'ajax/delete_assignment.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    alert('Tugas berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus tugas: ' + response.message);
                }
            },
            error: function() {
                alert('Error: API endpoint belum tersedia');
            }
        });
    }
}

function completeAssignment(id) {
    if (confirm("Apakah Anda yakin ingin menyelesaikan tugas ini?")) {
        // TODO: Integration dengan ajax/update_assignment.php
        $.ajax({
            url: 'ajax/update_assignment.php',
            method: 'POST',
            data: { id: id, status: 'completed' },
            success: function(response) {
                if (response.success) {
                    alert('Tugas berhasil diselesaikan');
                    location.reload();
                } else {
                    alert('Gagal menyelesaikan tugas: ' + response.message);
                }
            },
            error: function() {
                alert('Error: API endpoint belum tersedia');
            }
        });
    }
}
</script>
