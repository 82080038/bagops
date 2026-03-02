-- Create tables and populate data

USE bagops_db;

CREATE TABLE IF NOT EXISTS required_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kantor_type VARCHAR(50),
    unsur VARCHAR(100),
    jabatan VARCHAR(200),
    is_mandatory BOOLEAN DEFAULT 1
);

CREATE TABLE IF NOT EXISTS kantor_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kantor_id INT,
    jabatan VARCHAR(200),
    personel_id INT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kantor_id) REFERENCES kantor(id),
    FOREIGN KEY (personel_id) REFERENCES personel(id)
);

-- Insert required positions for polda_a_k
INSERT INTO required_positions (kantor_type, unsur, jabatan) VALUES
('polda_a_k', 'Unsur Pimpinan', 'Kapolda'),
('polda_a_k', 'Unsur Pimpinan', 'Wakapolda'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Sekretaris Utama'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Irwasum'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Asrena'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Aslog'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'AsSDM'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Asops'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Asintelkam'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Asren'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Aspropam'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Askumdatin'),
('polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan', 'Asbindiklat'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Intelkam'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Reskrim'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Resnarkoba'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Lantas'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Samapta'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Binmas'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Pengamanan Objek Vital'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Teknologi Informasi Komunikasi'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Profesi dan Pengamanan'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Keuangan'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Logistik'),
('polda_a_k', 'Unsur Pelaksana Tugas Pokok', 'Dir Sarana dan Prasarana'),
('polda_a_k', 'Unsur Pelaksana Tugas Kewilayahan', 'Satuan Wilayah'),
('polda_a_k', 'Unsur Pendukung', 'Bagian Keuangan'),
('polda_a_k', 'Unsur Pendukung', 'Bagian Personalia'),
('polda_a_k', 'Unsur Pendukung', 'Bagian Kesehatan'),
('polda_a_k', 'Unsur Pendukung', 'Bagian Pendidikan'),
('polda_a_k', 'Unsur Pendukung', 'Bagian Logistik');

-- Insert for polres
INSERT INTO required_positions (kantor_type, unsur, jabatan) VALUES
('polres', 'Unsur Pimpinan', 'Kapolres'),
('polres', 'Unsur Pimpinan', 'Wakapolres'),
('polres', 'Unsur Pengawas dan Pembantu Pimpinan', 'Kabag Ren'),
('polres', 'Unsur Pengawas dan Pembantu Pimpinan', 'Kabag Ops'),
('polres', 'Unsur Pengawas dan Pembantu Pimpinan', 'Kabag SDM'),
('polres', 'Unsur Pengawas dan Pembantu Pimpinan', 'Kabag Log'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Reskrim'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Intelkam'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Resnarkoba'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Lantas'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Binmas'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Samapta'),
('polres', 'Unsur Pelaksana Tugas Pokok', 'Kasat Tahti'),
('polres', 'Unsur Pelaksana Tugas Kewilayahan', 'Polsek'),
('polres', 'Unsur Pelaksana Tugas Kewilayahan', 'Bhabin'),
('polres', 'Unsur Pelaksana Tugas Kewilayahan', 'Polmas'),
('polres', 'Unsur Pelaksana Tugas Kewilayahan', 'Pospol'),
('polres', 'Unsur Pelaksana Tugas Kewilayahan', 'Patroli Kewilayahan'),
('polres', 'Unsur Pendukung', 'Kasi Propam'),
('polres', 'Unsur Pendukung', 'Kasi Keu'),
('polres', 'Unsur Pendukung', 'Kasi Humas'),
('polres', 'Unsur Pendukung', 'Kasi Dokkes'),
('polres', 'Unsur Pendukung', 'Kasi TIK'),
('polres', 'Unsur Pendukung', 'Kasi Kum'),
('polres', 'Unsur Pendukung', 'Kasi Was'),
('polres', 'Unsur Pendukung', 'Kasi Pers'),
('polres', 'Unsur Pendukung', 'Kasi Ops');

-- Insert for polsek
INSERT INTO required_positions (kantor_type, unsur, jabatan) VALUES
('polsek', 'Unsur Pimpinan', 'Kapolsek'),
('polsek', 'Unsur Pimpinan', 'Wakapolsek'),
('polsek', 'Unsur Pengawas dan Pembantu Pimpinan', 'Bagian Administrasi'),
('polsek', 'Unsur Pelaksana Tugas Pokok', 'Unit Reserse'),
('polsek', 'Unsur Pelaksana Tugas Pokok', 'Unit Intelijen'),
('polsek', 'Unsur Pelaksana Tugas Pokok', 'Unit Lalu Lintas'),
('polsek', 'Unsur Pelaksana Tugas Pokok', 'Unit Pembinaan Masyarakat'),
('polsek', 'Unsur Pelaksana Tugas Kewilayahan', 'Bhabinkamtibmas'),
('polsek', 'Unsur Pelaksana Tugas Kewilayahan', 'Polmas'),
('polsek', 'Unsur Pelaksana Tugas Kewilayahan', 'Patroli Kecamatan'),
('polsek', 'Unsur Pendukung', 'Seksi Keuangan'),
('polsek', 'Unsur Pendukung', 'Seksi Humas'),
('polsek', 'Unsur Pendukung', 'Seksi Kedokteran'),
('polsek', 'Unsur Pendukung', 'Seksi Teknologi Informasi');

-- Populate kantor_positions for existing kantor
INSERT INTO kantor_positions (kantor_id, jabatan)
SELECT k.id, rp.jabatan
FROM kantor k
JOIN required_positions rp ON (
    (k.jenis = 'polres' AND rp.kantor_type = 'polres') OR
    (k.jenis = 'polsek' AND rp.kantor_type = 'polsek') OR
    (k.jenis = 'polda' AND rp.kantor_type = 'polda_b')
);
