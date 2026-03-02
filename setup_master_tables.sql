-- Master tables setup

USE bagops_db;

-- Insert master kantor types
INSERT INTO master_kantor_type (type_code, type_name, description) VALUES
('polda_a_k', 'Polda Tipe A-K', 'Polda Metro Jaya - Khusus untuk Polda Metro Jaya'),
('polda_a', 'Polda Tipe A', 'Polda dengan kompleksitas tinggi'),
('polda_b', 'Polda Tipe B', 'Polda dengan kompleksitas sedang'),
('polrestabes', 'Polrestabes', 'Polisi Resort Kota Besar'),
('polresta', 'Polresta', 'Polisi Resort Kota'),
('polres', 'Polres', 'Polisi Resort'),
('polsek', 'Polsek', 'Polisi Sektor');

-- Insert master jabatan
INSERT INTO master_jabatan (jabatan, kantor_type, unsur) VALUES
-- Polda A-K
('Kapolda', 'polda_a_k', 'Unsur Pimpinan'),
('Wakapolda', 'polda_a_k', 'Unsur Pimpinan'),
('Sekretaris Utama', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Irwasum', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Asrena', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Aslog', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('AsSDM', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Asops', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Asintelkam', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Asren', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Aspropam', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Askumdatin', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Asbindiklat', 'polda_a_k', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Dir Intelkam', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Reskrim', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Resnarkoba', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Lantas', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Samapta', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Binmas', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Pengamanan Objek Vital', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Teknologi Informasi Komunikasi', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Profesi dan Pengamanan', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Keuangan', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Logistik', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Dir Sarana dan Prasarana', 'polda_a_k', 'Unsur Pelaksana Tugas Pokok'),
('Satuan Wilayah', 'polda_a_k', 'Unsur Pelaksana Tugas Kewilayahan'),
('Bagian Keuangan', 'polda_a_k', 'Unsur Pendukung'),
('Bagian Personalia', 'polda_a_k', 'Unsur Pendukung'),
('Bagian Kesehatan', 'polda_a_k', 'Unsur Pendukung'),
('Bagian Pendidikan', 'polda_a_k', 'Unsur Pendukung'),
('Bagian Logistik', 'polda_a_k', 'Unsur Pendukung'),

-- Polres
('Kapolres', 'polres', 'Unsur Pimpinan'),
('Wakapolres', 'polres', 'Unsur Pimpinan'),
('Kabag Ren', 'polres', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Kabag Ops', 'polres', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Kabag SDM', 'polres', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Kabag Log', 'polres', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Kasat Reskrim', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Intelkam', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Resnarkoba', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Lantas', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Binmas', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Samapta', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Kasat Tahti', 'polres', 'Unsur Pelaksana Tugas Pokok'),
('Polsek', 'polres', 'Unsur Pelaksana Tugas Kewilayahan'),
('Bhabin', 'polres', 'Unsur Pelaksana Tugas Kewilayahan'),
('Polmas', 'polres', 'Unsur Pelaksana Tugas Kewilayahan'),
('Pospol', 'polres', 'Unsur Pelaksana Tugas Kewilayahan'),
('Patroli Kewilayahan', 'polres', 'Unsur Pelaksana Tugas Kewilayahan'),
('Kasi Propam', 'polres', 'Unsur Pendukung'),
('Kasi Keu', 'polres', 'Unsur Pendukung'),
('Kasi Humas', 'polres', 'Unsur Pendukung'),
('Kasi Dokkes', 'polres', 'Unsur Pendukung'),
('Kasi TIK', 'polres', 'Unsur Pendukung'),
('Kasi Kum', 'polres', 'Unsur Pendukung'),
('Kasi Was', 'polres', 'Unsur Pendukung'),
('Kasi Pers', 'polres', 'Unsur Pendukung'),
('Kasi Ops', 'polres', 'Unsur Pendukung'),

-- Polsek
('Kapolsek', 'polsek', 'Unsur Pimpinan'),
('Wakapolsek', 'polsek', 'Unsur Pimpinan'),
('Bagian Administrasi', 'polsek', 'Unsur Pengawas dan Pembantu Pimpinan'),
('Unit Reserse', 'polsek', 'Unsur Pelaksana Tugas Pokok'),
('Unit Intelijen', 'polsek', 'Unsur Pelaksana Tugas Pokok'),
('Unit Lalu Lintas', 'polsek', 'Unsur Pelaksana Tugas Pokok'),
('Unit Pembinaan Masyarakat', 'polsek', 'Unsur Pelaksana Tugas Pokok'),
('Bhabinkamtibmas', 'polsek', 'Unsur Pelaksana Tugas Kewilayahan'),
('Polmas', 'polsek', 'Unsur Pelaksana Tugas Kewilayahan'),
('Patroli Kecamatan', 'polsek', 'Unsur Pelaksana Tugas Kewilayahan'),
('Seksi Keuangan', 'polsek', 'Unsur Pendukung'),
('Seksi Humas', 'polsek', 'Unsur Pendukung'),
('Seksi Kedokteran', 'polsek', 'Unsur Pendukung'),
('Seksi Teknologi Informasi', 'polsek', 'Unsur Pendukung');

-- Update units table to add kantor_type reference
ALTER TABLE units ADD COLUMN kantor_type VARCHAR(50) NULL;
UPDATE units SET kantor_type = 'polres' WHERE tipe = 'POLRES';
UPDATE units SET kantor_type = 'polsek' WHERE tipe = 'POLSEK';

-- Update kantor_positions to use master_jabatan
ALTER TABLE kantor_positions ADD COLUMN master_jabatan_id INT NULL;
ALTER TABLE kantor_positions ADD FOREIGN KEY (master_jabatan_id) REFERENCES master_jabatan(id);

-- Populate master_jabatan_id in kantor_positions
UPDATE kantor_positions kp 
JOIN master_jabatan mj ON kp.jabatan = mj.jabatan 
SET kp.master_jabatan_id = mj.id;
