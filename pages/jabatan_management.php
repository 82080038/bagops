<?php
// Check permissions
if (!$auth->canAccessModule('settings')) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied');
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-briefcase me-2"></i>Manajemen Jabatan Dinamis
                    </h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJabatanModal">
                        <i class="fas fa-plus me-1"></i>Tambah Jabatan
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchJabatan" placeholder="Cari jabatan...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterLevel">
                                <option value="">Semua Level</option>
                                <option value="1">Level 1</option>
                                <option value="2">Level 2</option>
                                <option value="3">Level 3</option>
                                <option value="4">Level 4</option>
                                <option value="5">Level 5</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterSource">
                                <option value="">Semua Sumber</option>
                                <option value="dynamic">Database Dinamis</option>
                                <option value="personel">Data Personel</option>
                            </select>
                        </div>
                    </div>

                    <!-- Jabatan Table -->
                    <div class="table-responsive">
                        <table id="jabatanTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Jabatan</th>
                                    <th>Kode</th>
                                    <th>Level</th>
                                    <th>Parent</th>
                                    <th>Sumber</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="8" class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Memuat data jabatan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Jabatan Modal -->
<div class="modal fade" id="addJabatanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addJabatanForm">
                    <div class="mb-3">
                        <label for="namaJabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="namaJabatan" name="nama_jabatan" required>
                        <div class="form-text">Masukkan nama jabatan lengkap (contoh: Kepala Bagian Operasional)</div>
                    </div>
                    <div class="mb-3">
                        <label for="kodeJabatan" class="form-label">Kode Jabatan</label>
                        <input type="text" class="form-control" id="kodeJabatan" name="kode_jabatan" placeholder="Opsional, akan digenerate otomatis">
                        <div class="form-text">Kode unik untuk jabatan (contoh: KABAGOPS)</div>
                    </div>
                    <div class="mb-3">
                        <label for="levelJabatan" class="form-label">Level Jabatan</label>
                        <select class="form-select" id="levelJabatan" name="level_jabatan">
                            <option value="1">Level 1 - Tingkat Atas</option>
                            <option value="2">Level 2 - Tingkat Menengah Atas</option>
                            <option value="3">Level 3 - Tingkat Menengah</option>
                            <option value="4">Level 4 - Tingkat Bawah</option>
                            <option value="5">Level 5 - Tingkat Pelaksana</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="parentJabatan" class="form-label">Jabatan Induk</label>
                        <select class="form-select" id="parentJabatan" name="parent_id">
                            <option value="">Tidak Ada</option>
                        </select>
                        <div class="form-text">Jabatan atasan langsung (opsional)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveJabatan()">Simpan Jabatan</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Jabatan Modal -->
<div class="modal fade" id="editJabatanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jabatan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editJabatanForm">
                    <input type="hidden" id="editJabatanId" name="id">
                    <div class="mb-3">
                        <label for="editNamaJabatan" class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editNamaJabatan" name="nama_jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label for="editKodeJabatan" class="form-label">Kode Jabatan</label>
                        <input type="text" class="form-control" id="editKodeJabatan" name="kode_jabatan">
                    </div>
                    <div class="mb-3">
                        <label for="editLevelJabatan" class="form-label">Level Jabatan</label>
                        <select class="form-select" id="editLevelJabatan" name="level_jabatan">
                            <option value="1">Level 1 - Tingkat Atas</option>
                            <option value="2">Level 2 - Tingkat Menengah Atas</option>
                            <option value="3">Level 3 - Tingkat Menengah</option>
                            <option value="4">Level 4 - Tingkat Bawah</option>
                            <option value="5">Level 5 - Tingkat Pelaksana</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editParentJabatan" class="form-label">Jabatan Induk</label>
                        <select class="form-select" id="editParentJabatan" name="parent_id">
                            <option value="">Tidak Ada</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="updateJabatan()">Update Jabatan</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadJabatanList();
    loadParentOptions();
    
    // Search functionality
    $('#searchJabatan').on('input', function() {
        var keyword = $(this).val();
        if (keyword.length >= 2 || keyword.length === 0) {
            loadJabatanList();
        }
    });
    
    // Filter functionality
    $('#filterLevel, #filterSource').on('change', function() {
        loadJabatanList();
    });
    
    // Auto-generate kode when nama changes
    $('#namaJabatan').on('input', function() {
        var nama = $(this).val();
        if (nama && !$('#kodeJabatan').val()) {
            var kode = generateKodeFromNama(nama);
            $('#kodeJabatan').val(kode);
        }
    });
});

function loadJabatanList() {
    var keyword = $('#searchJabatan').val();
    var level = $('#filterLevel').val();
    var source = $('#filterSource').val();
    
    $.ajax({
        url: 'ajax/jabatan_management.php',
        type: 'POST',
        data: {
            action: 'get_jabatan_list',
            keyword: keyword,
            level: level,
            source: source
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                displayJabatanTable(response.data);
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Error loading jabatan list');
        }
    });
}

function displayJabatanTable(data) {
    var tbody = $('#jabatanTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center">Tidak ada data jabatan</td></tr>');
        return;
    }
    
    data.forEach(function(jabatan) {
        var row = '<tr>' +
            '<td>' + jabatan.id + '</td>' +
            '<td><strong>' + jabatan.nama + '</strong></td>' +
            '<td><span class="badge bg-secondary">' + (jabatan.kode || '-') + '</span></td>' +
            '<td><span class="badge bg-info">Level ' + (jabatan.level || 0) + '</span></td>' +
            '<td>' + (jabatan.parent_id || '-') + '</td>' +
            '<td><span class="badge bg-' + (jabatan.source === 'dynamic' ? 'success' : 'warning') + '">' + jabatan.source + '</span></td>' +
            '<td><span class="badge bg-' + (jabatan.is_active ? 'success' : 'danger') + '">' + (jabatan.is_active ? 'Aktif' : 'Non-aktif') + '</span></td>' +
            '<td>' +
                '<button class="btn btn-sm btn-primary me-1" onclick="editJabatan(' + jabatan.id + ')" title="Edit">' +
                    '<i class="fas fa-edit"></i>' +
                '</button>' +
                (jabatan.source === 'dynamic' ? 
                    '<button class="btn btn-sm btn-danger" onclick="deleteJabatan(' + jabatan.id + ')" title="Hapus">' +
                        '<i class="fas fa-trash"></i>' +
                    '</button>' : ''
                ) +
            '</td>' +
            '</tr>';
        tbody.append(row);
    });
}

function loadParentOptions() {
    $.ajax({
        url: 'ajax/jabatan_management.php',
        type: 'POST',
        data: { action: 'get_jabatan_list' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var options = '<option value="">Tidak Ada</option>';
                response.data.forEach(function(jabatan) {
                    options += '<option value="' + jabatan.id + '">' + jabatan.nama + '</option>';
                });
                $('#parentJabatan, #editParentJabatan').html(options);
            }
        }
    });
}

function saveJabatan() {
    var formData = $('#addJabatanForm').serialize();
    formData += '&action=add_jabatan';
    
    $.ajax({
        url: 'ajax/jabatan_management.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addJabatanModal').modal('hide');
                $('#addJabatanForm')[0].reset();
                showAlert('success', response.message);
                loadJabatanList();
                loadParentOptions();
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Error saving jabatan');
        }
    });
}

function editJabatan(id) {
    $.ajax({
        url: 'ajax/jabatan_management.php',
        type: 'POST',
        data: { 
            action: 'get_jabatan_list',
            id: id 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var jabatan = response.data.find(j => j.id == id);
                if (jabatan) {
                    $('#editJabatanId').val(jabatan.id);
                    $('#editNamaJabatan').val(jabatan.nama);
                    $('#editKodeJabatan').val(jabatan.kode || '');
                    $('#editLevelJabatan').val(jabatan.level || 3);
                    $('#editParentJabatan').val(jabatan.parent_id || '');
                    
                    $('#editJabatanModal').modal('show');
                }
            } else {
                showAlert('danger', response.message);
            }
        }
    });
}

function updateJabatan() {
    var formData = $('#editJabatanForm').serialize();
    formData += '&action=update_jabatan';
    
    $.ajax({
        url: 'ajax/jabatan_management.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#editJabatanModal').modal('hide');
                showAlert('success', response.message);
                loadJabatanList();
                loadParentOptions();
            } else {
                showAlert('danger', response.message);
            }
        },
        error: function() {
            showAlert('danger', 'Error updating jabatan');
        }
    });
}

function deleteJabatan(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jabatan ini?')) {
        $.ajax({
            url: 'ajax/jabatan_management.php',
            type: 'POST',
            data: { 
                action: 'delete_jabatan',
                id: id 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadJabatanList();
                    loadParentOptions();
                } else {
                    showAlert('danger', response.message);
                }
            },
            error: function() {
                showAlert('danger', 'Error deleting jabatan');
            }
        });
    }
}

function generateKodeFromNama(nama) {
    var words = nama.toUpperCase().split(' ');
    var kode = '';
    
    for (var i = 0; i < words.length && kode.length < 10; i++) {
        if (words[i].length >= 3) {
            kode += words[i].substring(0, 3);
        } else {
            kode += words[i];
        }
    }
    
    return kode || 'JBT';
}

function showAlert(type, message) {
    // Create alert element
    var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>';
    
    // Add to top of card body
    $('.card-body').prepend(alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>
