<?php
/**
 * Operations Page - Full CRUD Implementation
 * Real-time operations management system
 */

// Load operations manager
require_once 'classes/OperationsManager.php';

try {
    $operationsManager = new OperationsManager($GLOBALS['db'], $GLOBALS['auth']);
    $operations = $operationsManager->getOperations();
    $stats = $operationsManager->getOperationStats();
    
} catch (Exception $e) {
    error_log("Operations Data Error: " . $e->getMessage());
    $operations = [];
    $stats = [
        'total_operations' => 0,
        'active_operations' => 0,
        'completed_operations' => 0,
        'planning_operations' => 0,
        'ongoing_operations' => 0
    ];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-cogs me-2"></i>Data Operasi</h2>
        <p class="text-muted">Manajemen operasi kepolisian POLRES SAMOSIR (Real-time Database)</p>
        <small class="text-muted">Template: operations.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- Statistics Cards -->
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
                        <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perencanaan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['planning_operations']); ?></div>
                        <small class="text-muted">Planning Phase</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Operasi</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="showCreateOperationModal()">
                        <i class="fas fa-plus me-2"></i>Tambah Operasi
                    </button>
                    <button class="btn btn-success btn-sm" onclick="refreshOperations()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                    <button class="btn btn-info btn-sm" onclick="exportOperations()">
                        <i class="fas fa-download me-2"></i>Export
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="perencanaan">Perencanaan</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterJenis">
                            <option value="">Semua Jenis</option>
                            <option value="rutin">Rutin</option>
                            <option value="khusus">Khusus</option>
                            <option value="darurat">Darurat</option>
                            <option value="pengamanan">Pengamanan</option>
                            <option value="penyelidikan">Penyelidikan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="filterTanggalMulai" placeholder="Tanggal Mulai">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control form-control-sm" id="filterTanggalSelesai" placeholder="Tanggal Selesai">
                    </div>
                </div>

                <!-- Operations Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="operationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Kode Operasi</th>
                                <th>Nama Operasi</th>
                                <th>Jenis</th>
                                <th>Tingkat</th>
                                <th>Status</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Lokasi</th>
                                <th>Personel</th>
                                <th>Created By</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($operations)): ?>
                            <tr>
                                <td colspan="11" class="text-center">Tidak ada data operasi</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($operations as $operation): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($operation['kode_operasi']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($operation['nama_operasi']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo match($operation['jenis_operasi']) {
                                                'rutin' => 'primary',
                                                'khusus' => 'warning',
                                                'darurat' => 'danger',
                                                'pengamanan' => 'info',
                                                'penyelidikan' => 'secondary',
                                                default => 'light'
                                            };
                                        ?>">
                                            <?php echo ucfirst($operation['jenis_operasi']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($operation['tingkat_operasi']) {
                                                'A' => 'danger',
                                                'B' => 'warning', 
                                                'C' => 'info',
                                                'D' => 'secondary',
                                                default => 'light'
                                            };
                                        ?>">
                                            Tingkat <?php echo $operation['tingkat_operasi']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo match($operation['status']) {
                                                'perencanaan' => 'secondary',
                                                'disetujui' => 'info',
                                                'aktif' => 'success',
                                                'ditangguhkan' => 'warning',
                                                'selesai' => 'primary',
                                                'dibatalkan' => 'danger',
                                                default => 'light'
                                            };
                                        ?>">
                                            <?php echo ucfirst($operation['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($operation['tanggal_mulai'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($operation['tanggal_selesai'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($operation['lokasi_utama'] ?? '-'); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo number_format($operation['personnel_count'] ?? 0); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($operation['created_by_name'] ?? '-'); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" title="Detail" onclick="viewOperation(<?php echo $operation['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" title="Edit" onclick="editOperation(<?php echo $operation['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger" title="Hapus" onclick="deleteOperation(<?php echo $operation['id']; ?>)">
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

<!-- Create/Edit Operation Modal -->
<div class="modal fade" id="operationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="operationModalTitle">Tambah Operasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="operationForm">
                    <input type="hidden" id="operationId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_operasi" class="form-label">Nama Operasi *</label>
                                <input type="text" class="form-control" id="nama_operasi" name="nama_operasi" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_operasi" class="form-label">Jenis Operasi *</label>
                                <select class="form-select" id="jenis_operasi" name="jenis_operasi" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="rutin">Rutin</option>
                                    <option value="khusus">Khusus</option>
                                    <option value="darurat">Darurat</option>
                                    <option value="pengamanan">Pengamanan</option>
                                    <option value="penyelidikan">Penyelidikan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tingkat_operasi" class="form-label">Tingkat Operasi *</label>
                                <select class="form-select" id="tingkat_operasi" name="tingkat_operasi" required>
                                    <option value="">Pilih Tingkat</option>
                                    <option value="A">Tingkat A (Kritikal)</option>
                                    <option value="B">Tingkat B (Tinggi)</option>
                                    <option value="C">Tingkat C (Sedang)</option>
                                    <option value="D">Tingkat D (Rendah)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="lokasi_utama" class="form-label">Lokasi Utama *</label>
                                <input type="text" class="form-control" id="lokasi_utama" name="lokasi_utama" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="wilayah_hukum" class="form-label">Wilayah Hukum</label>
                                <input type="text" class="form-control" id="wilayah_hukum" name="wilayah_hukum" placeholder="Polres Samosir">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi_detail" class="form-label">Detail Lokasi</label>
                        <textarea class="form-control" id="lokasi_detail" name="lokasi_detail" rows="2"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Operasi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="perencanaan">Perencanaan</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="aktif">Aktif</option>
                                    <option value="ditangguhkan">Ditangguhkan</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dibatalkan">Dibatalkan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveOperation()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Operation Modal -->
<div class="modal fade" id="detailOperationModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Operasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailOperationContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Operations page loaded with full CRUD functionality');
    
    // Initialize DataTable
    $('#operationsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']]
    });
    
    // Filter handlers
    $('#filterStatus, #filterJenis, #filterTanggalMulai, #filterTanggalSelesai').on('change', function() {
        filterOperations();
    });
});

function showCreateOperationModal() {
    $('#operationModalTitle').text('Tambah Operasi');
    $('#operationForm')[0].reset();
    $('#operationId').val('');
    $('#status').val('perencanaan');
    var modal = new bootstrap.Modal(document.getElementById('operationModal'));
    modal.show();
}

function editOperation(id) {
    $.ajax({
        url: 'ajax/operations.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var operation = response.data;
                $('#operationModalTitle').text('Edit Operasi');
                $('#operationId').val(operation.id);
                $('#nama_operasi').val(operation.nama_operasi);
                $('#jenis_operasi').val(operation.jenis_operasi);
                $('#tingkat_operasi').val(operation.tingkat_operasi);
                $('#prioritas').val(operation.prioritas);
                $('#tanggal_mulai').val(operation.tanggal_mulai);
                $('#tanggal_selesai').val(operation.tanggal_selesai);
                $('#lokasi_utama').val(operation.lokasi_utama);
                $('#wilayah_hukum').val(operation.wilayah_hukum);
                $('#lokasi_detail').val(operation.lokasi_detail);
                $('#deskripsi').val(operation.deskripsi);
                $('#status').val(operation.status);
                
                var modal = new bootstrap.Modal(document.getElementById('operationModal'));
                modal.show();
            } else {
                alert('Gagal memuat data operasi: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function saveOperation() {
    var form = $('#operationForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', $('#operationId').val() ? 'update' : 'create');
    
    $.ajax({
        url: 'ajax/operations.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('operationModal')).hide();
                location.reload();
            } else {
                alert('Gagal menyimpan operasi: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function deleteOperation(id) {
    if (confirm('Apakah Anda yakin ingin menghapus operasi ini?')) {
        $.ajax({
            url: 'ajax/operations.php',
            method: 'POST',
            data: {action: 'delete', id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Gagal menghapus operasi: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    }
}

function viewOperation(id) {
    $.ajax({
        url: 'ajax/operations.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var operation = response.data;
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Umum</h6>
                            <table class="table table-sm">
                                <tr><td>Kode Operasi</td><td><strong>${operation.kode_operasi}</strong></td></tr>
                                <tr><td>Nama Operasi</td><td>${operation.nama_operasi}</td></tr>
                                <tr><td>Jenis</td><td>${operation.jenis_operasi}</td></tr>
                                <tr><td>Tingkat</td><td>${operation.tingkat_operasi}</td></tr>
                                <tr><td>Status</td><td>${operation.status}</td></tr>
                                <tr><td>Prioritas</td><td>${operation.prioritas}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Waktu & Lokasi</h6>
                            <table class="table table-sm">
                                <tr><td>Tanggal Mulai</td><td>${operation.tanggal_mulai}</td></tr>
                                <tr><td>Tanggal Selesai</td><td>${operation.tanggal_selesai || '-'}</td></tr>
                                <tr><td>Lokasi Utama</td><td>${operation.lokasi_utama}</td></tr>
                                <tr><td>Wilayah Hukum</td><td>${operation.wilayah_hukum || '-'}</td></tr>
                                <tr><td>Dibuat Oleh</td><td>${operation.created_by_name}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Deskripsi</h6>
                            <p>${operation.deskripsi || 'Tidak ada deskripsi'}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6>Personel Ditugaskan (${operation.assignments ? operation.assignments.length : 0})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Nama</th><th>Role</th></tr></thead>
                                    <tbody>
                                        ${operation.assignments ? operation.assignments.map(a => 
                                            `<tr><td>${a.nama}</td><td>${a.role_assignment}</td></tr>`
                                        ).join('') : '<tr><td colspan="2">Belum ada personel</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Resources (${operation.resources ? operation.resources.length : 0})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Nama</th><th>Jenis</th></tr></thead>
                                    <tbody>
                                        ${operation.resources ? operation.resources.map(r => 
                                            `<tr><td>${r.nama_resource}</td><td>${r.jenis_resource}</td></tr>`
                                        ).join('') : '<tr><td colspan="2">Belum ada resources</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Dokumen (${operation.documents ? operation.documents.length : 0})</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead><tr><th>Judul</th><th>Kategori</th></tr></thead>
                                    <tbody>
                                        ${operation.documents ? operation.documents.map(d => 
                                            `<tr><td>${d.judul_document}</td><td>${d.kategori}</td></tr>`
                                        ).join('') : '<tr><td colspan="2">Belum ada dokumen</td></tr>'}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#detailOperationContent').html(content);
                var modal = new bootstrap.Modal(document.getElementById('detailOperationModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail operasi: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function filterOperations() {
    var filters = {
        status: $('#filterStatus').val(),
        jenis_operasi: $('#filterJenis').val(),
        tanggal_mulai: $('#filterTanggalMulai').val(),
        tanggal_selesai: $('#filterTanggalSelesai').val()
    };
    
    $.ajax({
        url: 'ajax/operations.php',
        method: 'POST',
        data: {action: 'list', ...filters},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update table with filtered data
                updateOperationsTable(response.data);
            }
        },
        error: function() {
            alert('Error: Tidak dapat filter data');
        }
    });
}

function updateOperationsTable(data) {
    var tbody = $('#operationsTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="11" class="text-center">Tidak ada data operasi</td></tr>');
        return;
    }
    
    data.forEach(function(operation) {
        var row = `
            <tr>
                <td><strong>${operation.kode_operasi}</strong></td>
                <td>${operation.nama_operasi}</td>
                <td><span class="badge badge-primary">${operation.jenis_operasi}</span></td>
                <td><span class="badge bg-info">Tingkat ${operation.tingkat_operasi}</span></td>
                <td><span class="badge badge-success">${operation.status}</span></td>
                <td>${operation.tanggal_mulai || '-'}</td>
                <td>${operation.tanggal_selesai || '-'}</td>
                <td>${operation.lokasi_utama || '-'}</td>
                <td><span class="badge bg-primary">${operation.personnel_count || 0}</span></td>
                <td>${operation.created_by_name || '-'}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewOperation(${operation.id})"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-warning" onclick="editOperation(${operation.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="deleteOperation(${operation.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function refreshOperations() {
    location.reload();
}

function exportOperations() {
    alert('Fitur export akan segera tersedia');
}
</script>
