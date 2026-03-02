<?php
/**
 * Kantor Page - Enhanced Office Management
 * Complete CRUD with statistics and advanced features
 */

// Load kantor manager
require_once 'classes/KantorManager.php';

try {
    $kantorManager = new KantorManager($GLOBALS['db'], $GLOBALS['auth']);
    $kantor = $kantorManager->getKantor();
    $stats = $kantorManager->getKantorStats();
    
} catch (Exception $e) {
    error_log("Kantor Data Error: " . $e->getMessage());
    $kantor = [];
    $stats = [
        'total_kantor' => 0,
        'total_polres' => 0,
        'total_polsek' => 0,
        'total_pos' => 0,
        'aktif' => 0,
        'non_aktif' => 0,
        'kabupaten' => 0,
        'kecamatan' => 0,
        'kompleksitas_tinggi' => 0,
        'kompleksitas_menengah' => 0,
        'kompleksitas_rendah' => 0,
        'personel_distribution' => []
    ];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-building me-2"></i>Data Kantor</h2>
        <p class="text-muted">Manajemen data kantor kepolisian POLRES SAMOSIR (Enhanced Management)</p>
        <small class="text-muted">Template: kantor.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- Enhanced Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kantor</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_kantor']); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['aktif']); ?></div>
                        <small class="text-muted">Operational</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Polsek</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_polsek']); ?></div>
                        <small class="text-muted">Polres: <?php echo $stats['total_polres']; ?></small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kompleksitas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            T: <?php echo $stats['kompleksitas_tinggi']; ?> | 
                            M: <?php echo $stats['kompleksitas_menengah']; ?> | 
                            R: <?php echo $stats['kompleksitas_rendah']; ?>
                        </div>
                        <small class="text-muted">Tinggi/Menengah/Rendah</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics Row -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Klasifikasi</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Kabupaten: <?php echo $stats['kabupaten']; ?> | 
                            Kecamatan: <?php echo $stats['kecamatan']; ?>
                        </div>
                        <small class="text-muted">Administrative Level</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-sitemap fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Non-Aktif</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['non_aktif']); ?></div>
                        <small class="text-muted">Inactive Offices</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Personel</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php echo array_sum(array_column($stats['personel_distribution'], 'jumlah_personel')); ?>
                        </div>
                        <small class="text-muted">Across All Offices</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kantor (<?php echo number_format($stats['total_kantor']); ?> records)</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="showCreateKantorModal()">
                        <i class="fas fa-plus me-2"></i>Tambah Kantor
                    </button>
                    <button class="btn btn-success btn-sm" onclick="refreshKantor()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                    <button class="btn btn-info btn-sm" onclick="exportKantor('excel')">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="exportKantor('pdf')">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filterTipe">
                            <option value="">Semua Tipe</option>
                            <option value="POLRES">POLRES</option>
                            <option value="POLSEK">POLSEK</option>
                            <option value="POS">POS</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filterKlasifikasi">
                            <option value="">Semua Klasifikasi</option>
                            <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                            <option value="Kecamatan">Kecamatan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filterKompleksitas">
                            <option value="">Semua Kompleksitas</option>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Menengah">Menengah</option>
                            <option value="Rendah">Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-select-sm" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="non_aktif">Non-Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control form-control-sm" id="searchKantor" placeholder="Cari kantor...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="filterKantor()">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Enhanced Kantor Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="kantorTable" width="100%" cellspacing="0" style="margin-bottom: 0;">
                        <thead class="table-dark">
                                <th class="py-2">ID</th>
                                <th class="py-2">Nama Kantor</th>
                                <th class="py-2">Pimpinan</th>
                                <th class="py-2">Jumlah Personel</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kantor)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data kantor</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($kantor as $k): ?>
                                <tr class="align-middle">
                                    <td class="py-1"><?php echo htmlspecialchars($k['id']); ?></td>
                                    <td class="py-1">
                                        <div>
                                            <strong><?php echo htmlspecialchars($k['nama_kantor']); ?></strong>
                                            <?php if (!empty($k['email'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($k['email']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-1">
                                        <?php if (!empty($k["pimpinan_nama"])): ?>
                                            <div>
                                                <strong><?php echo htmlspecialchars($k["pimpinan_pangkat_asli"]); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($k["pimpinan_nama"]); ?></small>
                                                <?php if (!empty($k["pimpinan_nrp"])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($k["pimpinan_nrp"]); ?></small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-danger">0</span>
                                        <?php endif; ?>
                                    <td>
                                        <span class="badge bg-primary"><?php echo number_format($k["jumlah_personel"] ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $k['status'] === 'aktif' ? 'bg-success' : 'bg-danger'; ?>">
                                    <td>
                                        <span class="badge bg-primary"><?php echo number_format($k['jumlah_personel'] ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" title="Detail" onclick="viewKantor(<?php echo $k['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" title="Edit" onclick="editKantor(<?php echo $k['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-<?php echo $k['status'] === 'aktif' ? 'secondary' : 'success'; ?>" 
                                                    title="<?php echo $k['status'] === 'aktif' ? 'Non-aktifkan' : 'Aktifkan'; ?>" 
                                                    onclick="toggleStatus(<?php echo $k['id']; ?>)">
                                                <i class="fas fa-<?php echo $k['status'] === 'aktif' ? 'pause' : 'play'; ?>"></i>
                                            </button>
                                            <button class="btn btn-danger" title="Hapus" onclick="deleteKantor(<?php echo $k['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
        </div>
    </div>
</div>

<!-- Create/Edit Kantor Modal -->
<div class="modal fade" id="kantorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kantorModalTitle">Tambah Kantor Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="kantorForm">
                    <input type="hidden" id="kantorId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_kantor" class="form-label">Nama Kantor *</label>
                                <input type="text" class="form-control" id="nama_kantor" name="nama_kantor" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipe_kantor_polisi" class="form-label">Tipe Kantor Polisi *</label>
                                <select class="form-select" id="tipe_kantor_polisi" name="tipe_kantor_polisi" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="POLRES">POLRES</option>
                                    <option value="POLSEK">POLSEK</option>
                                    <option value="POS">POS</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="klasifikasi" class="form-label">Klasifikasi</label>
                                <select class="form-select" id="klasifikasi" name="klasifikasi">
                                    <option value="">Pilih Klasifikasi</option>
                                    <option value="Kabupaten/Kota">Kabupaten/Kota</option>
                                    <option value="Kecamatan">Kecamatan</option>
                                    <option value="Kelurahan">Kelurahan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="level_kompleksitas" class="form-label">Level Kompleksitas</label>
                                <select class="form-select" id="level_kompleksitas" name="level_kompleksitas">
                                    <option value="">Pilih Level</option>
                                    <option value="Tinggi">Tinggi</option>
                                    <option value="Menengah">Menengah</option>
                                    <option value="Rendah">Rendah</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pimpinan_default_pangkat" class="form-label">Pimpinan Default Pangkat</label>
                                <select class="form-select" id="pimpinan_default_pangkat" name="pimpinan_default_pangkat">
                                    <option value="">Pilih Pangkat</option>
                                    <option value="AKBP">AKBP</option>
                                    <option value="Kompol">Kompol</option>
                                    <option value="AKP">AKP</option>
                                    <option value="Iptu">Iptu</option>
                                    <option value="Ipda">Ipda</option>
                                    <option value="Aipda">Aipda</option>
                                    <option value="Bripka">Bripka</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="aktif">Aktif</option>
                                    <option value="non_aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" placeholder="(0633) 12345">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="kantor@polri.go.id">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jam_operasional" class="form-label">Jam Operasional</label>
                                <input type="text" class="form-control" id="jam_operasional" name="jam_operasional" placeholder="08:00 - 16:00 WIB">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="latitude" class="form-label">Latitude (GPS)</label>
                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="2.6091">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="longitude" class="form-label">Longitude (GPS)</label>
                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="98.6156">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveKantor()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Kantor Modal -->
<div class="modal fade" id="detailKantorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kantor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailKantorContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Kantor page loaded with enhanced management functionality');
    
    // Initialize DataTable
    $('#kantorTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'asc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'Export Excel',
                className: 'btn btn-success btn-sm'
            },
            {
                extend: 'pdf',
                text: 'Export PDF',
                className: 'btn btn-warning btn-sm'
            }
        ]
    });
    
    // Filter handlers
    $('#filterTipe, #filterKlasifikasi, #filterKompleksitas, #filterStatus').on('change', function() {
        filterKantor();
    });
    
    // Search on enter
    $('#searchKantor').on('keypress', function(e) {
        if (e.which === 13) {
            filterKantor();
        }
    });
});

function showCreateKantorModal() {
    $('#kantorModalTitle').text('Tambah Kantor Baru');
    $('#kantorForm')[0].reset();
    $('#kantorId').val('');
    $('#status').val('aktif');
    var modal = new bootstrap.Modal(document.getElementById('kantorModal'));
    modal.show();
}

function editKantor(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var kantor = response.data;
                $('#kantorModalTitle').text('Edit Kantor');
                $('#kantorId').val(kantor.id);
                $('#nama_kantor').val(kantor.nama_kantor);
                $('#tipe_kantor_polisi').val(kantor.tipe_kantor_polisi);
                $('#klasifikasi').val(kantor.klasifikasi);
                $('#level_kompleksitas').val(kantor.level_kompleksitas);
                $('#pimpinan_default_pangkat').val(kantor.pimpinan_default_pangkat);
                $('#alamat').val(kantor.alamat);
                $('#telepon').val(kantor.telepon);
                $('#email').val(kantor.email);
                $('#jam_operasional').val(kantor.jam_operasional);
                $('#latitude').val(kantor.latitude);
                $('#longitude').val(kantor.longitude);
                $('#status').val(kantor.status);
                
                var modal = new bootstrap.Modal(document.getElementById('kantorModal'));
                modal.show();
            } else {
                alert('Gagal memuat data kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function saveKantor() {
    var form = $('#kantorForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', $('#kantorId').val() ? 'update' : 'create');
    
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('kantorModal')).hide();
                location.reload();
            } else {
                alert('Gagal menyimpan kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function deleteKantor(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kantor ini?')) {
        $.ajax({
            url: 'ajax/kantor.php',
            method: 'POST',
            data: {action: 'delete', id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Gagal menghapus kantor: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    }
}

function viewKantor(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var kantor = response.data;
                var content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informasi Umum</h6>
                            <table class="table table-sm">
                                <tr><td>Nama Kantor</td><td><strong>${kantor.nama_kantor}</strong></td></tr>
                                <tr><td>Tipe</td><td>${kantor.tipe_kantor_polisi || '-'}</td></tr>
                                <tr><td>Klasifikasi</td><td>${kantor.klasifikasi || '-'}</td></tr>
                                <tr><td>Level Kompleksitas</td><td>${kantor.level_kompleksitas || '-'}</td></tr>
                                <tr><td>Pimpinan Aktual</td><td>${kantor.pimpinan_nama ? kantor.pimpinan_pangkat_asli + " - " + kantor.pimpinan_nama : "Tidak ada"}</td></tr>
                                <tr><td>Status</td><td><span class="badge ${kantor.status === 'aktif' ? 'bg-success' : 'bg-danger'}">${kantor.status}</span></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Kontak & Lokasi</h6>
                            <table class="table table-sm">
                                <tr><td>Alamat</td><td>${kantor.alamat || '-'}</td></tr>
                                <tr><td>Telepon</td><td>${kantor.telepon || '-'}</td></tr>
                                <tr><td>Email</td><td>${kantor.email || '-'}</td></tr>
                                <tr><td>Jam Operasional</td><td>${kantor.jam_operasional || '-'}</td></tr>
                                <tr><td>Latitude</td><td>${kantor.latitude || '-'}</td></tr>
                                <tr><td>Longitude</td><td>${kantor.longitude || '-'}</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Personel</h6>
                            <p><strong>Total Personel:</strong> ${kantor.jumlah_personel || 0} personel</p>
                        </div>
                    </div>
                `;
                
                $('#detailKantorContent').html(content);
                var modal = new bootstrap.Modal(document.getElementById('detailKantorModal'));
                modal.show();
            } else {
                alert('Gagal memuat detail kantor: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function toggleStatus(id) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'toggle_status', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
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

function filterKantor() {
    var filters = {
        tipe_kantor_polisi: $('#filterTipe').val(),
        klasifikasi: $('#filterKlasifikasi').val(),
        level_kompleksitas: $('#filterKompleksitas').val(),
        status: $('#filterStatus').val(),
        search: $('#searchKantor').val()
    };
    
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'list', ...filters},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateKantorTable(response.data);
            }
        },
        error: function() {
            alert('Error: Tidak dapat filter data');
        }
    });
}

function updateKantorTable(data) {
    var tbody = $('#kantorTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center">Tidak ada data kantor</td></tr>');
        return;
    }
    
    data.forEach(function(kantor) {
        
        var row = `
            <tr>
                <td>${kantor.id}</td>
                <td>
                    <div>
                        <strong>${kantor.nama_kantor}</strong>
                        ${kantor.email ? '<br><small class="text-muted">' + kantor.email + '</small>' : ''}
                    </div>
                </td>
                        `<span class="badge bg-danger">0</span>`
                    }
                <td><span class="badge bg-primary">${kantor.jumlah_personel || 0}</span></td>
                    ${kantor.pimpinan_nama ? 
                        `<div><strong>${kantor.pimpinan_pangkat_asli}</strong><br><small class="text-muted">${kantor.pimpinan_nama}</small></div>` : 
                        `<span class="badge bg-danger">0</span>`
                    }
                </td>
                <td><span class="badge bg-primary">${kantor.jumlah_personel || 0}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-warning" onclick="editKantor(${kantor.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn ${kantor.status === 'aktif' ? 'btn-secondary' : 'btn-success'}" 
                                title="${kantor.status === 'aktif' ? 'Non-aktifkan' : 'Aktifkan'}">
                            <i class="fas fa-${kantor.status === 'aktif' ? 'pause' : 'play'}"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteKantor(${kantor.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getTipeBadge(tipe) {
    var badgeMap = {
        'POLRES': 'bg-danger',
        'POLSEK': 'bg-primary',
        'POS': 'bg-info'
    };
    return '<span class="badge ' + (badgeMap[tipe] || 'bg-secondary') + '">' + (tipe || '-') + '</span>';
}

function getKompleksitasBadge(kompleksitas) {
    var badgeMap = {
        'Tinggi': 'bg-danger',
        'Menengah': 'bg-warning',
        'Rendah': 'bg-success'
    };
    return '<span class="badge ' + (badgeMap[kompleksitas] || 'bg-secondary') + '">' + (kompleksitas || '-') + '</span>';
}

function getStatusBadge(status) {
    return '<span class="badge ' + (status === 'aktif' ? 'bg-success' : 'bg-danger') + '">' + (status || '-').toUpperCase() + '</span>';
}

function refreshKantor() {
    location.reload();
}

function exportKantor(format) {
    $.ajax({
        url: 'ajax/kantor.php',
        method: 'POST',
        data: {action: 'export', format: format},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Download the file
                window.open('ajax/kantor.php?action=download_export&filename=' + response.filename, '_blank');
            } else {
                alert('Gagal export: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat export data');
        }
    });
}
</script>
