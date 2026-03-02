<!-- Modal Templates for CRUD Operations -->

<!-- Personel Modal -->
<div class="modal fade" id="personelModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="personelModalTitle">Tambah Personel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="personelForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nrp" class="form-label">NRP</label>
                                <input type="text" class="form-control" id="nrp" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pangkat" class="form-label">Pangkat</label>
                                <select class="form-control" id="pangkat" required>
                                    <option value="">Pilih Pangkat</option>
                                    <option value="AKP">AKP</option>
                                    <option value="IPTU">IPTU</option>
                                    <option value="IPDA">IPDA</option>
                                    <option value="BRIPTU">BRIPTU</option>
                                    <option value="BRIPDA">BRIPDA</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan" class="form-label">Jabatan</label>
                                <select class="form-control" id="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit" class="form-label">Unit</label>
                                <select class="form-control" id="unit" required>
                                    <option value="">Pilih Unit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" required>
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="savePersonel()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Operations Modal -->
<div class="modal fade" id="operationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="operationModalTitle">Tambah Operasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="operationForm">
                    <div class="mb-3">
                        <label for="nama_operasi" class="form-label">Nama Operasi</label>
                        <input type="text" class="form-control" id="nama_operasi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_operasi" class="form-label">Jenis Operasi</label>
                                <select class="form-control" id="jenis_operasi" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="RENOPS">RENOPS</option>
                                    <option value="SPRIN">SPRIN</option>
                                    <option value="LAPHAR">LAPHAR</option>
                                    <option value="AAR">AAR</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" id="status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="planning">Planning</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="tanggal_selesai">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="lokasi" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" rows="3"></textarea>
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

<!-- Reports Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalTitle">Tambah Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reportForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="operation_id" class="form-label">Operasi</label>
                                <select class="form-control" id="operation_id" required>
                                    <option value="">Pilih Operasi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jenis_laporan" class="form-label">Jenis Laporan</label>
                                <select class="form-control" id="jenis_laporan" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="Harian">Laporan Harian</option>
                                    <option value="Mingguan">Laporan Mingguan</option>
                                    <option value="Bulanan">Laporan Bulanan</option>
                                    <option value="Akhir">Laporan Akhir</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="isi_laporan" class="form-label">Isi Laporan</label>
                        <textarea class="form-control" id="isi_laporan" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_lampiran" class="form-label">Lampiran (opsional)</label>
                        <input type="file" class="form-control" id="file_lampiran" accept=".pdf,.doc,.docx,.xls,.xlsx">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveReport()">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Assignments Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalTitle">Buat Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignmentForm">
                    <div class="mb-3">
                        <label for="personel_id" class="form-label">Personel</label>
                        <select class="form-control" id="personel_id" required>
                            <option value="">Pilih Personel</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="operation_id" class="form-label">Operasi</label>
                        <select class="form-control" id="operation_id" required>
                            <option value="">Pilih Operasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role_assignment" class="form-label">Role Assignment</label>
                        <input type="text" class="form-control" id="role_assignment" placeholder="Contoh: Team Leader, Investigator, etc." required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" required>
                            <option value="assigned">Assigned</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveAssignment()">Simpan</button>
            </div>
        </div>
    </div>
</div>
