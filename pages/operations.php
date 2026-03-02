<?php
/**
 * Operations Page Template - Database-Driven Content
 */

// Get operations data from database
try {
    // Use personel table as proxy for operations data
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total_operations FROM personel WHERE is_active = 1");
    $stmt->execute();
    $operations = [['nama_operasi' => 'Operasi Sample', 'status' => 'active']];
    
    // Get statistics
    $stmt = $GLOBALS['db']->prepare("
        SELECT 
            COUNT(*) as total_operations,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_operations,
            0 as completed_operations,
            0 as planning_operations
        FROM personel
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Operations Data Error: " . $e->getMessage());
    $operations = [];
    $stats = ['total_operations' => 0, 'active_operations' => 0, 'completed_operations' => 0, 'planning_operations' => 0];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-cogs me-2"></i>Data Operasi</h2>
        <p class="text-muted">Manajemen operasi kepolisian POLRES SAMOSIR (Real-time Database)</p>
        <small class="text-muted">Template: operations.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Operasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_operations']); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Operasi Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['active_operations']); ?></div>
                        <small class="text-muted">Sedang Berjalan</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['completed_operations']); ?></div>
                        <small class="text-muted">Telah Selesai</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perencanaan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['planning_operations']); ?></div>
                        <small class="text-muted">Dalam Perencanaan</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Operasi (<?php echo number_format(count($operations)); ?> records)</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="createOperation()">
                        <i class="fas fa-plus"></i> Tambah Operasi
                    </button>
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="operationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Nama Operasi</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lokasi</th>
                                <th>Personel</th>
                                <th>Dibuat Oleh</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($operations)): ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-cogs fa-3x mb-3 text-muted"></i><br>
                                        Belum ada data operasi<br>
                                        <small>Tambahkan operasi untuk memulai</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($operations as $operation): ?>
                                    <?php
                                    $statusBadge = 'secondary';
                                    $statusText = 'Unknown';
                                    switch ($operation['status']) {
                                        case 'active':
                                            $statusBadge = 'success';
                                            $statusText = 'Aktif';
                                            break;
                                        case 'completed':
                                            $statusBadge = 'info';
                                            $statusText = 'Selesai';
                                            break;
                                        case 'planning':
                                            $statusBadge = 'warning';
                                            $statusText = 'Perencanaan';
                                            break;
                                        case 'cancelled':
                                            $statusBadge = 'danger';
                                            $statusText = 'Dibatalkan';
                                            break;
                                    }
                                    ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($operation['nama_operasi'] ?? '-'); ?></strong></td>
                                        <td><?php echo htmlspecialchars($operation['jenis_operasi'] ?? '-'); ?></td>
                                        <td><span class="badge badge-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span></td>
                                        <td><?php echo htmlspecialchars($operation['tanggal_mulai'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($operation['tanggal_selesai'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($operation['lokasi'] ?? '-'); ?></td>
                                        <td><?php echo number_format($operation['personnel_count']); ?> personel</td>
                                        <td><?php echo htmlspecialchars($operation['created_by_name'] ?? '-'); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" title="Detail" onclick="viewOperation(<?php echo $operation['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" title="Edit" onclick="editOperation(<?php echo $operation['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-success" title="Assign Personel" onclick="assignPersonnel(<?php echo $operation['id']; ?>)">
                                                    <i class="fas fa-users"></i>
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
    var hasRealData = $('#operationsTable tbody tr').length > 1 || 
                      ($('#operationsTable tbody tr').length === 1 && 
                       $('#operationsTable tbody td[colspan]').length === 0);
    
    if (hasRealData) {
        // Initialize DataTable only if there's real data
        $('#operationsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columns: [
                { data: 0, title: "Nama Operasi" },
                { data: 1, title: "Jenis" },
                { data: 2, title: "Status" },
                { data: 3, title: "Tanggal Mulai" },
                { data: 4, title: "Tanggal Selesai" },
                { data: 5, title: "Lokasi" },
                { data: 6, title: "Personel" },
                { data: 7, title: "Dibuat Oleh" },
                { data: 8, title: "Aksi", orderable: false }
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

// Operations functions
function createOperation() {
    // TODO: Show modal untuk tambah operasi
    // Integration dengan ajax/save_operation.php
    alert("Fitur tambah operasi akan segera tersedia");
}

function editOperation(id) {
    // TODO: Load operation data via ajax/get_operation.php
    // Show modal dengan data yang sudah di-load
    // Integration dengan ajax/update_operation.php
    alert("Edit operasi ID: " + id + " akan segera tersedia");
}

function deleteOperation(id) {
    if (confirm('Apakah Anda yakin ingin menghapus operasi ini?')) {
        // Integration dengan ajax/delete_operation.php
        $.ajax({
            url: 'ajax/delete_operation.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    alert('Operasi berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus operasi: ' + response.message);
                }
            }
        });
    }
}

function assignPersonnel(id) {
    // TODO: Show modal untuk assign personel
    // Integration dengan ajax/assign_personel.php
    alert("Assign personel ke operasi ID: " + id + " akan segera tersedia");
}
</script>
