-- Enhanced Kantor Table for BAGOPS
-- Add missing columns for comprehensive office management

ALTER TABLE kantor 
ADD COLUMN alamat TEXT AFTER pimpinan_default_pangkat,
ADD COLUMN latitude DECIMAL(10,8) NULL AFTER alamat,
ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude,
ADD COLUMN telepon VARCHAR(20) NULL AFTER longitude,
ADD COLUMN email VARCHAR(100) NULL AFTER telepon,
ADD COLUMN jam_operasional VARCHAR(50) NULL AFTER email,
ADD COLUMN status ENUM('aktif', 'non_aktif') DEFAULT 'aktif' AFTER jam_operasional,
ADD COLUMN foto_kantor VARCHAR(255) NULL AFTER status;

-- Add indexes for performance
ALTER TABLE kantor 
ADD INDEX idx_status (status),
ADD INDEX idx_tipe_kantor_polisi (tipe_kantor_polisi),
ADD INDEX idx_klasifikasi (klasifikasi),
ADD INDEX idx_level_kompleksitas (level_kompleksitas);

-- Update existing records with sample data
UPDATE kantor SET 
    alamat = CASE 
        WHEN nama_kantor = 'POLRES SAMOSIR' THEN 'Jl. Sisingamangaraja No. 1, Pangururan, Samosir, Sumatera Utara'
        WHEN nama_kantor = 'POLSEK SIMANINDO' THEN 'Jl. Lintas Samosir, Simanindo, Samosir, Sumatera Utara'
        WHEN nama_kantor = 'POLSEK HARIAN BOHO' THEN 'Jl. Harian Boho, Samosir, Sumatera Utara'
        WHEN nama_kantor = 'POLSEK PALIPI' THEN 'Jl. Palipi, Samosir, Sumatera Utara'
        WHEN nama_kantor = 'POLSEK ONAN RUNGGU' THEN 'Jl. Onan Runggu, Samosir, Sumatera Utara'
        WHEN nama_kantor = 'POLSEK PANGURURAN' THEN 'Jl. Pangururan, Samosir, Sumatera Utara'
        ELSE alamat
    END,
    latitude = CASE 
        WHEN nama_kantor = 'POLRES SAMOSIR' THEN 2.6091
        WHEN nama_kantor = 'POLSEK SIMANINDO' THEN 2.6234
        WHEN nama_kantor = 'POLSEK HARIAN BOHO' THEN 2.5876
        WHEN nama_kantor = 'POLSEK PALIPI' THEN 2.6789
        WHEN nama_kantor = 'POLSEK ONAN RUNGGU' THEN 2.5432
        WHEN nama_kantor = 'POLSEK PANGURURAN' THEN 2.6091
        ELSE NULL
    END,
    longitude = CASE 
        WHEN nama_kantor = 'POLRES SAMOSIR' THEN 98.6156
        WHEN nama_kantor = 'POLSEK SIMANINDO' THEN 98.6345
        WHEN nama_kantor = 'POLSEK HARIAN BOHO' THEN 98.5876
        WHEN nama_kantor = 'POLSEK PALIPI' THEN 98.6789
        WHEN nama_kantor = 'POLSEK ONAN RUNGGU' THEN 98.5432
        WHEN nama_kantor = 'POLSEK PANGURURAN' THEN 98.6156
        ELSE NULL
    END,
    telepon = CASE 
        WHEN nama_kantor = 'POLRES SAMOSIR' THEN '(0633) 12345'
        WHEN nama_kantor = 'POLSEK SIMANINDO' THEN '(0633) 12346'
        WHEN nama_kantor = 'POLSEK HARIAN BOHO' THEN '(0633) 12347'
        WHEN nama_kantor = 'POLSEK PALIPI' THEN '(0633) 12348'
        WHEN nama_kantor = 'POLSEK ONAN RUNGGU' THEN '(0633) 12349'
        WHEN nama_kantor = 'POLSEK PANGURURAN' THEN '(0633) 12350'
        ELSE NULL
    END,
    email = CASE 
        WHEN nama_kantor = 'POLRES SAMOSIR' THEN 'polres.samosir@polri.go.id'
        WHEN nama_kantor = 'POLSEK SIMANINDO' THEN 'polsek.simanindo@polri.go.id'
        WHEN nama_kantor = 'POLSEK HARIAN BOHO' THEN 'polsek.harianboho@polri.go.id'
        WHEN nama_kantor = 'POLSEK PALIPI' THEN 'polsek.palipi@polri.go.id'
        WHEN nama_kantor = 'POLSEK ONAN RUNGGU' THEN 'polsek.onanrunggu@polri.go.id'
        WHEN nama_kantor = 'POLSEK PANGURURAN' THEN 'polsek.pangururan@polri.go.id'
        ELSE NULL
    END,
    jam_operasional = '08:00 - 16:00 WIB',
    status = 'aktif'
WHERE alamat IS NULL;
