<?php
/**
 * Reports Page - PDF Generation and Management
 * Advanced reporting system with multiple formats
 */

// Load report generator
require_once 'classes/ReportGenerator.php';

try {
    $reportGenerator = new ReportGenerator($GLOBALS['db'], $GLOBALS['auth']);
    $availableReports = $reportGenerator->getAvailableReports();
    
    // Get operations for report generation
    $stmt = $GLOBALS['db']->prepare("SELECT id, kode_operasi, nama_operasi FROM operations ORDER BY kode_operasi");
    $stmt->execute();
    $operations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stats = [
        'total_reports' => count($availableReports),
        'today_reports' => 0,
        'week_reports' => 0,
        'operations_count' => count($operations)
    ];
    
    // Calculate today and week reports
    $today = date('Y-m-d');
    $week_ago = date('Y-m-d', strtotime('-7 days'));
    
    foreach ($availableReports as $report) {
        $report_date = date('Y-m-d', strtotime($report['created']));
        if ($report_date === $today) {
            $stats['today_reports']++;
        }
        if ($report_date >= $week_ago) {
            $stats['week_reports']++;
        }
    }
    
} catch (Exception $e) {
    error_log("Reports Data Error: " . $e->getMessage());
    $availableReports = [];
    $operations = [];
    $stats = [
        'total_reports' => 0,
        'today_reports' => 0,
        'week_reports' => 0,
        'operations_count' => 0
    ];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-file-alt me-2"></i>Generasi Laporan</h2>
        <p class="text-muted">Sistem pembuatan laporan operasional POLRES SAMOSIR (PDF, Excel)</p>
        <small class="text-muted">Template: reports.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_reports']); ?></div>
                        <small class="text-muted">Tersimpan</small>
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
                        <small class="text-muted">Dibuat hari ini</small>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Minggu Ini</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['week_reports']); ?></div>
                        <small class="text-muted">7 hari terakhir</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Operasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['operations_count']); ?></div>
                        <small class="text-muted">Tersedia</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-cogs fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Generation Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Generasi Laporan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Operation Report -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-primary h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-cogs me-2"></i>Laporan Operasi
                                </h6>
                                <p class="card-text text-muted">Generate laporan detail untuk operasi tertentu</p>
                                
                                <form id="operationReportForm">
                                    <div class="mb-2">
                                        <label class="form-label">Pilih Operasi</label>
                                        <select class="form-select form-select-sm" name="operation_id" required>
                                            <option value="">Pilih Operasi</option>
                                            <?php foreach ($operations as $op): ?>
                                            <option value="<?php echo $op['id']; ?>">
                                                <?php echo htmlspecialchars($op['kode_operasi'] . ' - ' . $op['nama_operasi']); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Format</label>
                                        <select class="form-select form-select-sm" name="format">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary btn-sm w-100" onclick="generateOperationReport()">
                                        <i class="fas fa-file-pdf me-2"></i>Generate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personnel Report -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-success h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-users me-2"></i>Laporan Personel
                                </h6>
                                <p class="card-text text-muted">Generate laporan data personel dengan filter</p>
                                
                                <form id="personnelReportForm">
                                    <div class="mb-2">
                                        <label class="form-label">Filter Pangkat</label>
                                        <select class="form-select form-select-sm" name="pangkat">
                                            <option value="">Semua Pangkat</option>
                                            <?php
                                            $stmt = $GLOBALS['db']->prepare("SELECT DISTINCT pangkat FROM personel WHERE pangkat IS NOT NULL ORDER BY pangkat");
                                            $stmt->execute();
                                            $pangkats = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                            foreach ($pangkats as $pangkat):
                                            ?>
                                            <option value="<?php echo $pangkat; ?>"><?php echo htmlspecialchars($pangkat); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Filter Jabatan</label>
                                        <input type="text" class="form-control form-control-sm" name="jabatan" placeholder="Kosongkan untuk semua">
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Format</label>
                                        <select class="form-select form-select-sm" name="format">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
                                    
                                    <button type="button" class="btn btn-success btn-sm w-100" onclick="generatePersonnelReport()">
                                        <i class="fas fa-file-pdf me-2"></i>Generate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monthly Report -->
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-info h-100">
                            <div class="card-body">
                                <h6 class="card-title">
                                    <i class="fas fa-calendar-alt me-2"></i>Laporan Bulanan
                                </h6>
                                <p class="card-text text-muted">Generate laporan bulanan operasional</p>
                                
                                <form id="monthlyReportForm">
                                    <div class="mb-2">
                                        <label class="form-label">Bulan</label>
                                        <select class="form-select form-select-sm" name="month">
                                            <?php
                                            for ($m = 1; $m <= 12; $m++) {
                                                $selected = ($m == date('n')) ? 'selected' : '';
                                                echo "<option value='{$m}' {$selected}>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Tahun</label>
                                        <select class="form-select form-select-sm" name="year">
                                            <?php
                                            for ($y = date('Y'); $y >= date('Y') - 5; $y--) {
                                                $selected = ($y == date('Y')) ? 'selected' : '';
                                                echo "<option value='{$y}' {$selected}>{$y}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <label class="form-label">Format</label>
                                        <select class="form-select form-select-sm" name="format">
                                            <option value="pdf">PDF</option>
                                            <option value="excel">Excel</option>
                                        </select>
                                    </div>
                                    
                                    <button type="button" class="btn btn-info btn-sm w-100" onclick="generateMonthlyReport()">
                                        <i class="fas fa-file-pdf me-2"></i>Generate
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Available Reports Section -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Laporan Tersedia</h6>
                <div>
                    <button class="btn btn-success btn-sm" onclick="refreshReports()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="reportsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Ukuran</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($availableReports)): ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada laporan yang dibuat</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($availableReports as $report): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <?php echo htmlspecialchars($report['filename']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small><?php echo $reportGenerator->formatFileSize($report['size']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo $report['created']; ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" title="Download" onclick="downloadReport('<?php echo $report['filename']; ?>')">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-danger" title="Hapus" onclick="deleteReport('<?php echo $report['filename']; ?>')">
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

<script>
$(document).ready(function() {
    console.log('Reports page loaded with PDF generation functionality');
    
    // Initialize DataTable
    $('#reportsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[2, 'desc']]
    });
});

function generateOperationReport() {
    var form = document.getElementById('operationReportForm');
    var formData = new FormData(form);
    
    if (!formData.get('operation_id')) {
        alert('Pilih operasi terlebih dahulu');
        return;
    }
    
    $.ajax({
        url: 'ajax/reports.php',
        method: 'POST',
        data: {
            action: 'generate_operation',
            operation_id: formData.get('operation_id'),
            format: formData.get('format')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                refreshReports();
            } else {
                alert('Gagal generate laporan: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function generatePersonnelReport() {
    var form = document.getElementById('personnelReportForm');
    var formData = new FormData(form);
    
    $.ajax({
        url: 'ajax/reports.php',
        method: 'POST',
        data: {
            action: 'generate_personnel',
            pangkat: formData.get('pangkat'),
            jabatan: formData.get('jabatan'),
            format: formData.get('format')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                refreshReports();
            } else {
                alert('Gagal generate laporan: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function generateMonthlyReport() {
    var form = document.getElementById('monthlyReportForm');
    var formData = new FormData(form);
    
    $.ajax({
        url: 'ajax/reports.php',
        method: 'POST',
        data: {
            action: 'generate_monthly',
            month: formData.get('month'),
            year: formData.get('year'),
            format: formData.get('format')
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                refreshReports();
            } else {
                alert('Gagal generate laporan: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function downloadReport(filename) {
    window.open('ajax/reports.php?action=download_report&filename=' + encodeURIComponent(filename), '_blank');
}

function deleteReport(filename) {
    if (confirm('Apakah Anda yakin ingin menghapus laporan ini?')) {
        $.ajax({
            url: 'ajax/reports.php',
            method: 'POST',
            data: {action: 'delete_report', filename: filename},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    refreshReports();
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

function refreshReports() {
    location.reload();
}

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}
</script>
