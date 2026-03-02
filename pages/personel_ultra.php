<?php
/**
 * Ultra Simple Personel Page Template
 */

// Get personel data from database
try {
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total FROM personel WHERE is_active = 1");
    $stmt->execute();
    $count = $stmt->fetch()['total'];
    
    $stmt = $GLOBALS['db']->prepare("SELECT id, nrp, nama, pangkat, jabatan, unit, is_active FROM personel WHERE is_active = 1 ORDER BY nama LIMIT 10");
    $stmt->execute();
    $personel = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $count = 0;
    $personel = [];
}
?>



<div class="row mb-4">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Personel (<?php echo number_format($count); ?> records)</h6>
                <button class="btn btn-primary btn-sm" onclick="addPersonel()">
                    <i class="fas fa-plus"></i> Tambah Personel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="personelTable_wrapper">
                    <table class="table table-bordered" id="personelTable">
                        <thead>
                            <tr>
                                <th>NRP</th>
                                <th>Nama</th>
                                <th>Pangkat</th>
                                <th>Jabatan</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($personel)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-3x mb-3 text-muted"></i><br>
                                        Belum ada data personel
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($personel as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['nrp'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['nama'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['pangkat'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['jabatan'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($p['unit'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($p['is_active'] ?? false): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" title="Edit" onclick="editPersonel('<?php echo $p['id']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" title="Hapus" onclick="deletePersonel('<?php echo $p['id']; ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
    // Initialize DataTable with AJAX
    $('#personelTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        autoWidth: false,
        scrollX: true,
        ajax: {
            url: 'ajax/get_personel.php',
            type: 'GET',
            data: function (d) {
                // Add any additional parameters if needed
                return d;
            },
            error: function(xhr, error, code) {
                console.error('DataTables error:', error, code);
                $('#personelTable_wrapper').append(
                    '<div class="alert alert-danger">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                    'Error loading data: ' + (xhr.responseJSON?.error || 'Unknown error') +
                    '</div>'
                );
            }
        },
        pageLength: 25,
        order: [[0, 'asc']],
        columns: [
            { data: 0, title: "NRP" },
            { data: 1, title: "Nama" },
            { data: 2, title: "Pangkat" },
            { data: 3, title: "Jabatan" },
            { data: 4, title: "Unit" },
            { data: 5, title: "Status", orderable: false },
            { data: 6, title: "Aksi", orderable: false }
        ],
        language: {
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Tidak ada data yang ditemukan",
            "info": "Halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "search": "Cari:",
            "processing": "Sedang memuat...",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        initComplete: function(settings, json) {
            console.log('DataTables initialized successfully');
        }
    });
});

// CRUD Functions
function addPersonel() {
    // TODO: Implement add personel modal/form
    alert("Fitur tambah personel akan segera tersedia");
}

function editPersonel(id) {
    // TODO: Implement edit personel modal/form
    alert("Edit personel ID: " + id + " akan segera tersedia");
}

function deletePersonel(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data personel ini?')) {
        // TODO: Implement delete personel via AJAX
        alert("Hapus personel ID: " + id + " akan segera tersedia");
    }
}
</script>
