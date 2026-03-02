-- Operations Management System for BAGOPS
-- Complete CRUD implementation for police operations

-- Main operations table
CREATE TABLE IF NOT EXISTS operations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_operasi VARCHAR(50) UNIQUE NOT NULL,
    nama_operasi VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    jenis_operasi ENUM('rutin', 'khusus', 'darurat', 'pengamanan', 'penyelidikan') NOT NULL DEFAULT 'rutin',
    tingkat_operasi ENUM('A', 'B', 'C', 'D') NOT NULL DEFAULT 'C',
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    lokasi_utama VARCHAR(255) NOT NULL,
    lokasi_detail TEXT,
    wilayah_hukum VARCHAR(100),
    status ENUM('perencanaan', 'disetujui', 'aktif', 'ditangguhkan', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'perencanaan',
    prioritas ENUM('rendah', 'sedang', 'tinggi', 'kritikal') NOT NULL DEFAULT 'sedang',
    created_by INT NOT NULL,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_kode_operasi (kode_operasi),
    INDEX idx_status (status),
    INDEX idx_tanggal (tanggal_mulai, tanggal_selesai),
    INDEX idx_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Operation personnel assignments
CREATE TABLE IF NOT EXISTS operation_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    personel_id INT NOT NULL,
    role_assignment VARCHAR(100) NOT NULL DEFAULT 'Anggota',
    tugas_khusus TEXT,
    tanggal_assign DATE NOT NULL DEFAULT (CURRENT_DATE),
    jam_mulai TIME,
    jam_selesai TIME,
    status_assignment ENUM('ditugaskan', 'hadir', 'tidak_hadir', 'selesai', 'diganti') NOT NULL DEFAULT 'ditugaskan',
    catatan TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_operation_personel (operation_id, personel_id),
    INDEX idx_operation_id (operation_id),
    INDEX idx_personel_id (personel_id),
    INDEX idx_status_assignment (status_assignment),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE,
    FOREIGN KEY (personel_id) REFERENCES personel(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Operation resources/equipment
CREATE TABLE IF NOT EXISTS operation_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    jenis_resource ENUM('kendaraan', 'senjata', 'peralatan', 'komunikasi', 'lainnya') NOT NULL,
    nama_resource VARCHAR(200) NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    satuan VARCHAR(50) DEFAULT 'unit',
    kondisi ENUM('baik', 'rusak_ringan', 'rusak_berat', 'maintenance') NOT NULL DEFAULT 'baik',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_operation_id (operation_id),
    INDEX idx_jenis_resource (jenis_resource),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE
);

-- Operation documents/files
CREATE TABLE IF NOT EXISTS operation_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    judul_document VARCHAR(200) NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    tipe_file VARCHAR(100) NOT NULL,
    ukuran_file INT NOT NULL,
    path_file VARCHAR(500) NOT NULL,
    kategori ENUM('surat_perintah', 'laporan', 'dokumentasi', 'bukti', 'lainnya') NOT NULL DEFAULT 'dokumentasi',
    status_document ENUM('draft', 'final', 'arsip') NOT NULL DEFAULT 'draft',
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_operation_id (operation_id),
    INDEX idx_kategori (kategori),
    INDEX idx_uploaded_by (uploaded_by),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Operation timeline/log activities
CREATE TABLE IF NOT EXISTS operation_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    jenis_log ENUM('dibuat', 'diupdate', 'disetujui', 'dimulai', 'dijeda', 'diselesaikan', 'personel_ditambah', 'personel_dihapus', 'resource_ditambah', 'resource_dihapus', 'dokumen_ditambah') NOT NULL,
    deskripsi TEXT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_operation_id (operation_id),
    INDEX idx_jenis_log (jenis_log),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Operation objectives/targets
CREATE TABLE IF NOT EXISTS operation_objectives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    judul_objective VARCHAR(200) NOT NULL,
    deskripsi_objective TEXT,
    target_kuantitatif VARCHAR(100),
    satuan_target VARCHAR(50),
    status_achievement ENUM('belum', 'progres', 'tercapai', 'gagal') NOT NULL DEFAULT 'belum',
    nilai_achievement DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_operation_id (operation_id),
    INDEX idx_status_achievement (status_achievement),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE
);

-- Operation evaluation/report
CREATE TABLE IF NOT EXISTS operation_evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation_id INT NOT NULL,
    tipe_evaluasi ENUM('akhir', 'periodik', 'insidental') NOT NULL DEFAULT 'akhir',
    tanggal_evaluasi DATE NOT NULL,
    evaluator_id INT NOT NULL,
    kesimpulan_evaluasi TEXT,
    rekomendasi TEXT,
    nilai_kinerja DECIMAL(5,2) DEFAULT 0.00,
    status_evaluasi ENUM('draft', 'submitted', 'approved') NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_operation_id (operation_id),
    INDEX idx_evaluator_id (evaluator_id),
    FOREIGN KEY (operation_id) REFERENCES operations(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluator_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Insert sample data for testing
INSERT INTO operations (
    kode_operasi, nama_operasi, deskripsi, jenis_operasi, tingkat_operasi,
    tanggal_mulai, tanggal_selesai, lokasi_utama, lokasi_detail,
    wilayah_hukum, status, prioritas, created_by
) VALUES 
(
    'OPS-2026-001', 'Operasi Kewilayahan Rutin', 
    'Patroli dan pengamanan wilayah hukum Polres Samosir secara rutin',
    'rutin', 'C', '2026-03-01', '2026-03-31', 
    'Kota Pangururan', 'Seluruh kecamatan di wilayah Samosir',
    'Polres Samosir', 'aktif', 'sedang', 1
),
(
    'OPS-2026-002', 'Operasi Lilin Samosir 2026',
    'Pengamanan Natal dan Tahun Baru di wilayah pariwisata Danau Toba',
    'pengamanan', 'A', '2026-12-20', '2026-01-05',
    'Danau Toba', 'Kawasan pariwisata Toba, Hotel, dan tempat ibadah',
    'Polres Samosir', 'perencanaan', 'tinggi', 1
),
(
    'OPS-2026-003', 'Operasi Pemberantasan Judi',
    'Penertiban dan penindakan perjudian di wilayah Samosir',
    'penyelidikan', 'B', '2026-03-15', '2026-03-20',
    'Samosir Utara', 'Lokasi judi konvensional dan online',
    'Polres Samosir', 'perencanaan', 'sedang', 1
);

-- Insert sample assignments
INSERT INTO operation_assignments (
    operation_id, personel_id, role_assignment, tugas_khusus, 
    tanggal_assign, jam_mulai, jam_selesai, status_assignment, created_by
) SELECT 
    o.id, p.id, 'Ketua Tim', 'Memimpin operasi dan koordinasi personel',
    CURRENT_DATE, '08:00', '16:00', 'ditugaskan', 1
FROM operations o 
CROSS JOIN (SELECT id FROM personel WHERE is_active = 1 LIMIT 3) p 
WHERE o.id IN (1, 2) LIMIT 6;

-- Insert sample resources
INSERT INTO operation_resources (
    operation_id, jenis_resource, nama_resource, jumlah, satuan, kondisi
) VALUES 
(1, 'kendaraan', 'Mobil Patroli', 2, 'unit', 'baik'),
(1, 'komunikasi', 'HT Radio', 4, 'unit', 'baik'),
(2, 'kendaraan', 'Mobil Dinas', 3, 'unit', 'baik'),
(2, 'peralatan', 'Traffic Cone', 20, 'unit', 'baik'),
(3, 'senjata', 'Pistol Standar', 5, 'unit', 'baik');

-- Insert sample objectives
INSERT INTO operation_objectives (
    operation_id, judul_objective, deskripsi_objective, 
    target_kuantitatif, satuan_target, status_achievement
) VALUES 
(1, 'Menjaga Kamtibmas', 'Menjaga keamanan dan ketertiban masyarakat', '0', 'kejadian kriminal', 'progres'),
(1, 'Patroli Rutin', 'Melakukan patroli di semua wilayah', '30', 'kali patroli', 'progres'),
(2, 'Zero Accident', 'Mencapai nol kecelakaan', '0', 'kecelakaan lalu lintas', 'belum'),
(3, 'Penertiban Judi', 'Menertibkan semua lokasi perjudian', '100', 'persen lokasi', 'belum');
