-- Update existing assignments table for full task management
ALTER TABLE assignments 
ADD COLUMN judul_assignment VARCHAR(200) NOT NULL AFTER id,
ADD COLUMN deskripsi TEXT AFTER judul_assignment,
ADD COLUMN tanggal_mulai DATE NOT NULL AFTER operation_id,
ADD COLUMN tanggal_selesai DATE AFTER tanggal_mulai,
ADD COLUMN prioritas ENUM('rendah', 'sedang', 'tinggi', 'kritikal') NOT NULL DEFAULT 'sedang' AFTER tanggal_selesai,
ADD COLUMN catatan TEXT AFTER prioritas,
ADD COLUMN progress_percent INT DEFAULT 0 AFTER catatan,
ADD COLUMN created_by INT NOT NULL AFTER progress_percent,
ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
MODIFY COLUMN status_assignment ENUM('ditugaskan', 'diproses', 'selesai', 'terlambat', 'dibatalkan') NOT NULL DEFAULT 'ditugaskan';

-- Add indexes
ALTER TABLE assignments 
ADD INDEX idx_judul_assignment (judul_assignment),
ADD INDEX idx_tanggal (tanggal_mulai, tanggal_selesai),
ADD INDEX idx_prioritas (prioritas),
ADD INDEX idx_created_by (created_by);

-- Add foreign key for created_by
ALTER TABLE assignments 
ADD CONSTRAINT fk_assignments_created_by 
FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT;

-- Update existing records with sample data
UPDATE assignments SET 
    judul_assignment = 'Assignment Default',
    deskripsi = 'Deskripsi assignment',
    tanggal_mulai = CURDATE(),
    tanggal_selesai = DATE_ADD(CURDATE(), INTERVAL 7 DAY),
    prioritas = 'sedang',
    catatan = 'Catatan assignment',
    progress_percent = 0,
    created_by = 1
WHERE judul_assignment IS NULL;

-- Create assignment templates table
CREATE TABLE IF NOT EXISTS assignment_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_template VARCHAR(200) NOT NULL,
    deskripsi_template TEXT,
    default_prioritas ENUM('rendah', 'sedang', 'tinggi', 'kritikal') NOT NULL DEFAULT 'sedang',
    default_status ENUM('ditugaskan', 'diproses', 'selesai', 'terlambat', 'dibatalkan') NOT NULL DEFAULT 'ditugaskan',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_created_by (created_by),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Create assignment progress tracking table
CREATE TABLE IF NOT EXISTS assignment_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT NOT NULL,
    progress_percent INT NOT NULL DEFAULT 0,
    status_progress ENUM('mulai', 'progres', 'kendala', 'selesai') NOT NULL DEFAULT 'mulai',
    catatan_progress TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_assignment_id (assignment_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (assignment_id) REFERENCES assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Insert sample templates
INSERT INTO assignment_templates (
    nama_template, deskripsi_template, default_prioritas, default_status, created_by
) VALUES 
(
    'Patroli Rutin',
    'Template untuk tugas patroli rutin wilayah hukum',
    'sedang', 'ditugaskan', 1
),
(
    'Pengamanan Kegiatan',
    'Template untuk pengamanan kegiatan masyarakat',
    'tinggi', 'ditugaskan', 1
),
(
    'Penyelidikan Kasus',
    'Template untuk tugas penyelidikan kasus kriminal',
    'sedang', 'ditugaskan', 1
),
(
    'Administrasi Kantor',
    'Template untuk tugas administrasi kantor',
    'rendah', 'ditugaskan', 1
);

-- Insert sample assignments
INSERT INTO assignments (
    judul_assignment, deskripsi, personel_id, operation_id,
    tanggal_mulai, tanggal_selesai, prioritas, status_assignment, created_by
) VALUES 
(
    'Patroli Rutin Wilayah Kota',
    'Melakukan patroli rutin di wilayah kota Pangururan untuk menjaga kamtibmas',
    1, 1, '2026-03-01', '2026-03-31', 'sedang', 'diproses', 1
),
(
    'Pengamanan Lokasi Wisata',
    'Melakukan pengamanan di lokasi wisata Danau Toba selama musim liburan',
    2, 2, '2026-12-20', '2026-01-05', 'tinggi', 'ditugaskan', 1
),
(
    'Penyelidikan Kasus Pencurian',
    'Melakukan penyelidikan terkait laporan pencurian di wilayah Samosir Utara',
    3, 3, '2026-03-15', '2026-03-25', 'sedang', 'ditugaskan', 1
),
(
    'Administrasi Kantor',
    'Menangani administrasi kantor dan laporan harian',
    4, NULL, '2026-03-01', '2026-03-31', 'rendah', 'selesai', 1
),
(
    'Koordinasi dengan Instansi Terkait',
    'Melakukan koordinasi dengan pemerintah daerah dan instansi terkait',
    5, 1, '2026-03-10', '2026-03-15', 'sedang', 'diproses', 1
);

-- Insert sample progress tracking
INSERT INTO assignment_progress (
    assignment_id, progress_percent, status_progress, catatan_progress, created_by
) VALUES 
(1, 25, 'progres', 'Patroli minggu pertama berhasil dilaksanakan', 1),
(4, 100, 'selesai', 'Semua administrasi bulan Maret selesai', 1),
(5, 50, 'progres', 'Koordinasi dengan 3 instansi sudah dilakukan', 1);
