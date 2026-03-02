-- Documents Management System for BAGOPS
-- File upload, download, and management

CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul_document VARCHAR(200) NOT NULL,
    nama_file_asli VARCHAR(255) NOT NULL,
    nama_file VARCHAR(255) NOT NULL,
    tipe_file VARCHAR(100) NOT NULL,
    ukuran_file INT NOT NULL,
    path_file VARCHAR(500) NOT NULL,
    kategori ENUM('surat_perintah', 'laporan', 'dokumentasi', 'bukti', 'umum', 'internal', 'confidential') NOT NULL DEFAULT 'umum',
    deskripsi TEXT,
    access_level ENUM('public', 'internal', 'confidential', 'secret', 'top_secret') NOT NULL DEFAULT 'internal',
    download_count INT DEFAULT 0,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_kategori (kategori),
    INDEX idx_access_level (access_level),
    INDEX idx_uploaded_by (uploaded_by),
    INDEX idx_uploaded_at (uploaded_at),
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Insert sample documents
INSERT INTO documents (
    judul_document, nama_file_asli, nama_file, tipe_file, ukuran_file,
    path_file, kategori, deskripsi, access_level, uploaded_by
) VALUES 
(
    'Surat Perintah Operasi Kewilayahan',
    'SP_OPS_Kewilayahan_2026.pdf',
    '20260303_12345678_surat_perintah_ops_kewilayahan.pdf',
    'application/pdf',
    1024000,
    '/var/www/html/bagops/storage/documents/20260303_12345678_surat_perintah_ops_kewilayahan.pdf',
    'surat_perintah',
    'Surat perintah untuk operasi kewilayahan rutin bulan Maret 2026',
    'internal',
    1
),
(
    'Laporan Operasi Lilin Samosir',
    'Laporan_Lilin_Samosir_2026.docx',
    '20260303_87654321_laporan_lilin_samosir.docx',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    2048000,
    '/var/www/html/bagops/storage/documents/20260303_87654321_laporan_lilin_samosir.docx',
    'laporan',
    'Laporan pelaksanaan operasi Lilin Samosir 2026',
    'confidential',
    1
),
(
    'Dokumentasi Foto Operasi',
    'Foto_Operasi_Maret_2026.zip',
    '20260303_11223344_foto_operasi_maret_2026.zip',
    'application/zip',
    5120000,
    '/var/www/html/bagops/storage/documents/20260303_11223344_foto_operasi_maret_2026.zip',
    'dokumentasi',
    'Kumpulan foto dokumentasi operasi bulan Maret 2026',
    'internal',
    1
);
