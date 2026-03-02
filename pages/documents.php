<?php
/**
 * Documents Management Page
 * File upload, download, and management system
 */

// Load document manager
require_once 'classes/DocumentManager.php';

try {
    $documentManager = new DocumentManager($GLOBALS['db'], $GLOBALS['auth']);
    $documents = $documentManager->getDocuments();
    $stats = $documentManager->getDocumentStats();
    
} catch (Exception $e) {
    error_log("Documents Data Error: " . $e->getMessage());
    $documents = [];
    $stats = [
        'total_documents' => 0,
        'total_size' => 0,
        'surat_perintah' => 0,
        'laporan' => 0,
        'dokumentasi' => 0,
        'this_week' => 0
    ];
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-file-alt me-2"></i>Manajemen Dokumen</h2>
        <p class="text-muted">Sistem manajemen dokumen POLRES SAMOSIR (Upload, Download, Organize)</p>
        <small class="text-muted">Template: documents.php | Source: Database | Generated: <?php echo date('Y-m-d H:i:s'); ?></small>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Dokumen</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['total_documents']); ?></div>
                        <small class="text-muted">Database Real-time</small>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Size</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $documentManager->formatFileSize($stats['total_size']); ?></div>
                        <small class="text-muted">Storage Used</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database fa-2x text-gray-300"></i>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($stats['this_week']); ?></div>
                        <small class="text-muted">New Documents</small>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Kategori</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            SP: <?php echo $stats['surat_perintah']; ?> | 
                            L: <?php echo $stats['laporan']; ?> | 
                            D: <?php echo $stats['dokumentasi']; ?>
                        </div>
                        <small class="text-muted">SP/Laporan/Dok</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-folder fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Manajemen Dokumen</h6>
                <div>
                    <button class="btn btn-primary btn-sm" onclick="showUploadModal()">
                        <i class="fas fa-upload me-2"></i>Upload Dokumen
                    </button>
                    <button class="btn btn-success btn-sm" onclick="refreshDocuments()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                    <button class="btn btn-info btn-sm" onclick="exportDocuments()">
                        <i class="fas fa-download me-2"></i>Export List
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterKategori">
                            <option value="">Semua Kategori</option>
                            <option value="surat_perintah">Surat Perintah</option>
                            <option value="laporan">Laporan</option>
                            <option value="dokumentasi">Dokumentasi</option>
                            <option value="bukti">Bukti</option>
                            <option value="umum">Umum</option>
                            <option value="internal">Internal</option>
                            <option value="confidential">Confidential</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-sm" id="filterAccess">
                            <option value="">Semua Access Level</option>
                            <option value="public">Public</option>
                            <option value="internal">Internal</option>
                            <option value="confidential">Confidential</option>
                            <option value="secret">Secret</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control form-control-sm" id="searchDocument" placeholder="Cari dokumen...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="filterDocuments()">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>

                <!-- Documents Table -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="documentsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Judul Dokumen</th>
                                <th>Kategori</th>
                                <th>Access Level</th>
                                <th>Tipe File</th>
                                <th>Ukuran</th>
                                <th>Diupload Oleh</th>
                                <th>Tanggal</th>
                                <th>Download</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Tidak ada dokumen</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="<?php echo $documentManager->getFileIcon($document['nama_file'], $document['tipe_file']); ?> me-2"></i>
                                            <div>
                                                <strong><?php echo htmlspecialchars($document['judul_document']); ?></strong>
                                                <?php if (!empty($document['deskripsi'])): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($document['deskripsi'], 0, 50)) . '...'; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo match($document['kategori']) {
                                                'surat_perintah' => 'danger',
                                                'laporan' => 'success',
                                                'dokumentasi' => 'info',
                                                'bukti' => 'warning',
                                                'umum' => 'primary',
                                                'internal' => 'secondary',
                                                'confidential' => 'dark',
                                                default => 'light'
                                            };
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $document['kategori'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($document['access_level']) {
                                                'public' => 'success',
                                                'internal' => 'info',
                                                'confidential' => 'warning',
                                                'secret' => 'danger',
                                                'top_secret' => 'dark',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($document['access_level']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($document['tipe_file']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo $documentManager->formatFileSize($document['ukuran_file']); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo htmlspecialchars($document['uploaded_by_name'] ?? '-'); ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo $document['formatted_date']; ?></small>
                                    </td>
                                    <td>
                                        <small><?php echo number_format($document['download_count']); ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-success" title="Download" onclick="downloadDocument(<?php echo $document['id']; ?>)">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-warning" title="Edit" onclick="editDocument(<?php echo $document['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger" title="Hapus" onclick="deleteDocument(<?php echo $document['id']; ?>)">
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

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="documentFile" class="form-label">Pilih File *</label>
                        <input type="file" class="form-control" id="documentFile" name="document" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar,.7z">
                        <small class="form-text text-muted">Maksimal 10MB. Format: PDF, DOC, XLS, PPT, JPG, PNG, ZIP</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="judul_document" class="form-label">Judul Dokumen *</label>
                        <input type="text" class="form-control" id="judul_document" name="judul_document" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori *</label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="surat_perintah">Surat Perintah</option>
                                    <option value="laporan">Laporan</option>
                                    <option value="dokumentasi">Dokumentasi</option>
                                    <option value="bukti">Bukti</option>
                                    <option value="umum">Umum</option>
                                    <option value="internal">Internal</option>
                                    <option value="confidential">Confidential</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="access_level" class="form-label">Access Level *</label>
                                <select class="form-select" id="access_level" name="access_level" required>
                                    <option value="">Pilih Access Level</option>
                                    <option value="public">Public</option>
                                    <option value="internal">Internal</option>
                                    <option value="confidential">Confidential</option>
                                    <option value="secret">Secret</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Document Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="editId" name="id">
                    
                    <div class="mb-3">
                        <label for="edit_judul_document" class="form-label">Judul Dokumen *</label>
                        <input type="text" class="form-control" id="edit_judul_document" name="judul_document" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_kategori" class="form-label">Kategori *</label>
                                <select class="form-select" id="edit_kategori" name="kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <option value="surat_perintah">Surat Perintah</option>
                                    <option value="laporan">Laporan</option>
                                    <option value="dokumentasi">Dokumentasi</option>
                                    <option value="bukti">Bukti</option>
                                    <option value="umum">Umum</option>
                                    <option value="internal">Internal</option>
                                    <option value="confidential">Confidential</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_access_level" class="form-label">Access Level *</label>
                                <select class="form-select" id="edit_access_level" name="access_level" required>
                                    <option value="">Pilih Access Level</option>
                                    <option value="public">Public</option>
                                    <option value="internal">Internal</option>
                                    <option value="confidential">Confidential</option>
                                    <option value="secret">Secret</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="updateDocument()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    console.log('Documents page loaded with full management functionality');
    
    // Initialize DataTable
    $('#documentsTable').DataTable({
        responsive: true,
        pageLength: 25,
        order: [[0, 'desc']]
    });
    
    // Search on enter
    $('#searchDocument').on('keypress', function(e) {
        if (e.which === 13) {
            filterDocuments();
        }
    });
});

function showUploadModal() {
    $('#uploadForm')[0].reset();
    var modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    modal.show();
}

function uploadDocument() {
    var form = $('#uploadForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', 'upload');
    
    $.ajax({
        url: 'ajax/documents.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('uploadModal')).hide();
                location.reload();
            } else {
                alert('Gagal upload dokumen: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
        }
    });
}

function editDocument(id) {
    $.ajax({
        url: 'ajax/documents.php',
        method: 'POST',
        data: {action: 'get', id: id},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var document = response.data;
                $('#editId').val(document.id);
                $('#edit_judul_document').val(document.judul_document);
                $('#edit_kategori').val(document.kategori);
                $('#edit_access_level').val(document.access_level);
                $('#edit_deskripsi').val(document.deskripsi);
                
                var modal = new bootstrap.Modal(document.getElementById('editModal'));
                modal.show();
            } else {
                alert('Gagal memuat data dokumen: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function updateDocument() {
    var form = $('#editForm')[0];
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var formData = new FormData(form);
    formData.append('action', 'update');
    
    $.ajax({
        url: 'ajax/documents.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert(response.message);
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                location.reload();
            } else {
                alert('Gagal update dokumen: ' + response.message);
            }
        },
        error: function() {
            alert('Error: Tidak dapat terhubung ke server');
        }
    });
}

function deleteDocument(id) {
    if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
        $.ajax({
            url: 'ajax/documents.php',
            method: 'POST',
            data: {action: 'delete', id: id},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Gagal menghapus dokumen: ' + response.message);
                }
            },
            error: function() {
                alert('Error: Tidak dapat terhubung ke server');
            }
        });
    }
}

function downloadDocument(id) {
    // Open download in new window/tab
    window.open('ajax/documents.php?action=download&id=' + id, '_blank');
}

function filterDocuments() {
    var filters = {
        kategori: $('#filterKategori').val(),
        access_level: $('#filterAccess').val(),
        search: $('#searchDocument').val()
    };
    
    $.ajax({
        url: 'ajax/documents.php',
        method: 'POST',
        data: {action: 'list', ...filters},
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateDocumentsTable(response.data);
            }
        },
        error: function() {
            alert('Error: Tidak dapat filter data');
        }
    });
}

function updateDocumentsTable(data) {
    var tbody = $('#documentsTable tbody');
    tbody.empty();
    
    if (data.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">Tidak ada dokumen</td></tr>');
        return;
    }
    
    data.forEach(function(document) {
        var fileIcon = getFileIcon(document.nama_file, document.tipe_file);
        var kategoriBadge = getKategoriBadge(document.kategori);
        var accessBadge = getAccessBadge(document.access_level);
        
        var row = `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="${fileIcon} me-2"></i>
                        <div>
                            <strong>${document.judul_document}</strong>
                            ${document.deskripsi ? '<br><small class="text-muted">' + document.deskripsi.substring(0, 50) + '...</small>' : ''}
                        </div>
                    </div>
                </td>
                <td>${kategoriBadge}</td>
                <td>${accessBadge}</td>
                <td><small>${document.tipe_file}</small></td>
                <td><small>${formatFileSize(document.ukuran_file)}</small></td>
                <td><small>${document.uploaded_by_name || '-'}</small></td>
                <td><small>${document.formatted_date}</small></td>
                <td><small>${document.download_count}</small></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-success" onclick="downloadDocument(${document.id})"><i class="fas fa-download"></i></button>
                        <button class="btn btn-warning" onclick="editDocument(${document.id})"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-danger" onclick="deleteDocument(${document.id})"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getFileIcon(filename, mimeType) {
    var ext = filename.split('.').pop().toLowerCase();
    var iconMap = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'ppt': 'fas fa-file-powerpoint text-warning',
        'pptx': 'fas fa-file-powerpoint text-warning',
        'jpg': 'fas fa-file-image text-info',
        'jpeg': 'fas fa-file-image text-info',
        'png': 'fas fa-file-image text-info',
        'gif': 'fas fa-file-image text-info',
        'zip': 'fas fa-file-archive text-secondary',
        'rar': 'fas fa-file-archive text-secondary',
        '7z': 'fas fa-file-archive text-secondary'
    };
    return iconMap[ext] || 'fas fa-file text-muted';
}

function getKategoriBadge(kategori) {
    var badgeMap = {
        'surat_perintah': 'badge-danger',
        'laporan': 'badge-success',
        'dokumentasi': 'badge-info',
        'bukti': 'badge-warning',
        'umum': 'badge-primary',
        'internal': 'badge-secondary',
        'confidential': 'badge-dark'
    };
    return '<span class="badge ' + (badgeMap[kategori] || 'badge-light') + '">' + kategori.replace('_', ' ').toUpperCase() + '</span>';
}

function getAccessBadge(accessLevel) {
    var badgeMap = {
        'public': 'bg-success',
        'internal': 'bg-info',
        'confidential': 'bg-warning',
        'secret': 'bg-danger',
        'top_secret': 'bg-dark'
    };
    return '<span class="badge ' + (badgeMap[accessLevel] || 'bg-secondary') + '">' + accessLevel.toUpperCase() + '</span>';
}

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

function refreshDocuments() {
    location.reload();
}

function exportDocuments() {
    alert('Fitur export akan segera tersedia');
}
</script>
