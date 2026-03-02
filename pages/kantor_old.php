<?php
/**
 * Kantor Page Template
 */

// Get kantor data from database
try {
    $stmt = $GLOBALS['db']->prepare("SELECT COUNT(*) as total FROM kantor");
    $stmt->execute();
    $count = $stmt->fetch()['total'];
    
    $stmt = $GLOBALS['db']->prepare("SELECT id, nama_kantor, tipe_kantor_polisi, klasifikasi, level_kompleksitas, pimpinan_default_pangkat FROM kantor ORDER BY nama_kantor LIMIT 10");
    $stmt->execute();
    $kantor = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $count = 0;
    $kantor = [];
}
?>

<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Kantor</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($count); ?></div>
                        <small class="text-muted">Database Real-time</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Daftar Kantor (<?php echo number_format($count); ?> records)</h6>
                <button class="btn btn-primary btn-sm" onclick="addKantor()">
                    <i class="fas fa-plus"></i> Tambah Kantor
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="kantorTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Kantor</th>
                                <th>Tipe Kantor</th>
                                <th>Klasifikasi</th>
                                <th>Level Kompleksitas</th>
                                <th>Pimpinan Default</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kantor)): ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data kantor</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($kantor as $k): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($k['id']); ?></td>
                                <td><?php echo htmlspecialchars($k['nama_kantor']); ?></td>
                                <td><?php echo htmlspecialchars($k['tipe_kantor_polisi'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($k['klasifikasi'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($k['level_kompleksitas'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($k['pimpinan_default_pangkat'] ?? '-'); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="editKantor(<?php echo $k['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteKantor(<?php echo $k['id']; ?>)">
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

<!-- Add Kantor Modal -->
<div class="modal fade" id="addKantorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kantor Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addKantorForm">
                    <div class="mb-3">
                        <label for="namaKantor" class="form-label">Nama Kantor</label>
                        <input type="text" class="form-control" id="namaKantor" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipeKantor" class="form-label">Tipe Kantor Polisi</label>
                        <select class="form-select" id="tipeKantor">
                            <option value="">Pilih Tipe</option>
                            <option value="Polres">Polres</option>
                            <option value="Polsek">Polsek</option>
                            <option value="Pos">Pos</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="klasifikasi" class="form-label">Klasifikasi</label>
                        <select class="form-select" id="klasifikasi">
                            <option value="">Pilih Klasifikasi</option>
                            <option value="Madya">Madya</option>
                            <option value="Pratama">Pratama</option>
                            <option value="Wilayah">Wilayah</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="levelKompleksitas" class="form-label">Level Kompleksitas</label>
                        <select class="form-select" id="levelKompleksitas">
                            <option value="">Pilih Level</option>
                            <option value="Rendah">Rendah</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Tinggi">Tinggi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pimpinanDefault" class="form-label">Pimpinan Default Pangkat</label>
                        <select class="form-select" id="pimpinanDefault">
                            <option value="">Pilih Pangkat</option>
                            <option value="AKBP">AKBP</option>
                            <option value="Kompol">Kompol</option>
                            <option value="AKP">AKP</option>
                            <option value="Iptu">Iptu</option>
                            <option value="Ipda">Ipda</option>
                        </select>
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

<script>
function addKantor() {
    $('#addKantorModal').modal('show');
}

function saveKantor() {
    const formData = {
        nama_kantor: $('#namaKantor').val(),
        tipe_kantor_polisi: $('#tipeKantor').val(),
        klasifikasi: $('#klasifikasi').val(),
        level_kompleksitas: $('#levelKompleksitas').val(),
        pimpinan_default_pangkat: $('#pimpinanDefault').val()
    };
    
    // Here you would normally send this to server
    console.log('Saving kantor:', formData);
    alert('Fitur simpan kantor akan segera tersedia');
    $('#addKantorModal').modal('hide');
}

function editKantor(id) {
    console.log('Edit kantor:', id);
    alert('Fitur edit kantor akan segera tersedia');
}

function deleteKantor(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kantor ini?')) {
        console.log('Delete kantor:', id);
        alert('Fitur hapus kantor akan segera tersedia');
    }
}
</script>
