<?php
/**
 * Reports Page Template - Database-Driven Content
 */

// Get reports data from database
try {
    $stmt = $GLOBALS['db']->prepare("
        SELECT dr.*, u.nama as user_nama, o.nama_operasi
        FROM daily_reports dr
        LEFT JOIN users u ON dr.user_id = u.id
        LEFT JOIN operations o ON dr.operation_id = o.id
        WHERE DATE(dr.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ORDER BY dr.created_at DESC
        LIMIT 50
    ");
    $stmt->execute();
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stmt = $GLOBALS['db']->prepare("
        SELECT 
            COUNT(*) as total_reports,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_reports,
            SUM(CASE WHEN DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) THEN 1 ELSE 0 END) as yesterday_reports,
            SUM(CASE WHEN DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week_reports
        FROM daily_reports
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Reports Data Error: " . $e->getMessage());
    $reports = [];
    $stats = ['total_reports' => 0, 'today_reports' => 0, 'yesterday_reports' => 0, 'week_reports' => 0];
}
?>


<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_reports']); ?></div>
                        <small class="text-muted">30 Hari Terakhir</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Hari Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['today_reports']); ?></div>
                        <small class="text-muted">Laporan Hari Ini</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kemarin</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['yesterday_reports']); ?></div>
                        <small class="text-muted">Laporan Kemarin</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-history fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Minggu Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['week_reports']); ?></div>
                        <small class="text-muted">7 Hari Terakhir</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Laporan (<?php echo number_format(count($reports)); ?> records)</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="addReport()">
                        <i class="fas fa-plus"></i> Tambah Laporan
                    </button>
                    <button class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel"></i> Export
                    </button>
                    <button class="btn btn-info btn-sm">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Operasi</th>
                                <th>Jenis Laporan</th>
                                <th>Isi Ringkas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($reports)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-file-alt fa-3x mb-3 text-muted"></i><br>
                                        Belum ada data laporan<br>
                                        <small>Buat laporan untuk memulai</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($reports as $report): ?>
                                    <?php
                                    $statusBadge = 'secondary';
                                    $statusText = 'Unknown';
                                    // Assuming status field exists in daily_reports table
                                    if (isset($report['status'])) {
                                        switch ($report['status']) {
                                            case 'submitted':
                                                $statusBadge = 'info';
                                                $statusText = 'Diajukan';
                                                break;
                                            case 'approved':
                                                $statusBadge = 'success';
                                                $statusText = 'Disetujui';
                                                break;
                                            case 'rejected':
                                                $statusBadge = 'danger';
                                                $statusText = 'Ditolak';
                                                break;
                                            case 'draft':
                                                $statusBadge = 'warning';
                                                $statusText = 'Draft';
                                                break;
                                            default:
                                                $statusBadge = 'secondary';
                                                $statusText = 'Unknown';
                                                break;
                                        }
                                    } else {
                                        $statusBadge = 'info';
                                        $statusText = 'Diajukan';
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($report['created_at'])); ?></td>
                                        <td><?php echo htmlspecialchars($report['user_nama'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($report['nama_operasi'] ?? '-'); ?></td>
                                        <td>Laporan Harian</td>
                                        <td><?php echo htmlspecialchars(substr($report['content'] ?? '-', 0, 50)) . '...'; ?></td>
                                        <td><span class="badge badge-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info" title="Detail" onclick="viewReport(<?php echo $report['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-warning" title="Edit" onclick="editReport(<?php echo $report['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-success" title="Export" onclick="exportReport(<?php echo $report['id']; ?>)">
                                                    <i class="fas fa-download"></i>
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
    var hasRealData = $('#reportsTable tbody tr').length > 1 || 
                      ($('#reportsTable tbody tr').length === 1 && 
                       $('#reportsTable tbody td[colspan]').length === 0);
    
    if (hasRealData) {
        // Initialize DataTable only if there's real data
        $('#reportsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [[0, 'desc']],
            columns: [
                { data: 0, title: "Tanggal" },
                { data: 1, title: "User" },
                { data: 2, title: "Operasi" },
                { data: 3, title: "Jenis Laporan" },
                { data: 4, title: "Isi Ringkas" },
                { data: 5, title: "Status" },
                { data: 6, title: "Aksi", orderable: false }
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

// Reports functions
function addReport() {
    // TODO: Show modal untuk tambah laporan
    // Integration dengan ajax/save_report.php
    alert("Fitur tambah laporan akan segera tersedia");
}

function editReport(id) {
    // TODO: Load report data via ajax/get_report.php
    // Show modal dengan data yang sudah di-load
    // Integration dengan ajax/update_report.php
    alert("Edit laporan ID: " + id + " akan segera tersedia");
}

function deleteReport(id) {
    if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
        // Integration dengan ajax/delete_report.php
        $.ajax({
            url: 'ajax/delete_report.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    alert('Laporan berhasil dihapus');
                    location.reload();
                } else {
                    alert('Gagal menghapus laporan: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    }
}

function downloadReport(id) {
    // Integration dengan ajax/download_report.php
    window.open('ajax/download_report.php?id=' + id, '_blank');
}

function exportReports() {
    // Integration dengan ajax/export_reports.php
    window.open('ajax/export_reports.php', '_blank');
}
</script>
