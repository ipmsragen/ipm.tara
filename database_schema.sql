ipm_tara-- =====================================================
-- DATABASE SCHEMA UNTUK WEBSITE IPM TARA
-- =====================================================
-- File ini berisi struktur database lengkap untuk website IPM TARA
-- Jalankan file ini di MySQL untuk membuat database dan tabel

-- Membuat database
CREATE DATABASE IF NOT EXISTS ipm_tara;
USE ipm_tara;

-- =====================================================
-- TABEL USERS (Anggota IPM)
-- =====================================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_depan VARCHAR(100) NOT NULL,
    nama_belakang VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    tempat_lahir VARCHAR(100) NOT NULL,
    jenis_kelamin ENUM('laki-laki', 'perempuan') NOT NULL,
    alamat TEXT NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    sekolah VARCHAR(100) NOT NULL,
    nisn VARCHAR(20) NOT NULL,
    pimpinan_ranting VARCHAR(100) NOT NULL,
    pimpinan_cabang VARCHAR(100) NULL,
    foto_path VARCHAR(255) NULL,
    status ENUM('aktif', 'non-aktif', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- TABEL PIMPINAN RANTING
-- =====================================================
CREATE TABLE pimpinan_ranting (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_ranting VARCHAR(100) NOT NULL,
    alamat VARCHAR(255) NOT NULL,
    no_telp VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    status ENUM('aktif', 'non-aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABEL PIMPINAN CABANG
-- =====================================================
CREATE TABLE pimpinan_cabang (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_cabang VARCHAR(100) NOT NULL,
    alamat VARCHAR(255) NOT NULL,
    no_telp VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    status ENUM('aktif', 'non-aktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- =====================================================
-- TABEL BERITA & PENGUMUMAN
-- =====================================================
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    featured_image VARCHAR(255) NULL,
    category VARCHAR(50) DEFAULT 'umum',
    status ENUM('published', 'draft', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    author_id INT NOT NULL,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- TABEL KEGIATAN
-- =====================================================
CREATE TABLE activities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    max_participants INT NULL,
    registration_deadline DATETIME NULL,
    status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- =====================================================
-- TABEL REGISTRASI KEGIATAN
-- =====================================================
CREATE TABLE activity_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('registered', 'confirmed', 'cancelled') DEFAULT 'registered',
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (activity_id, user_id)
);

-- =====================================================
-- TABEL BUKU PANDUAN
-- =====================================================
CREATE TABLE books (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT NULL, -- dalam bytes
    file_type VARCHAR(50) NULL,
    download_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABEL ADMIN
-- =====================================================
CREATE TABLE admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABEL SETTINGS
-- =====================================================
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NULL,
    description TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================================================
-- INSERT DATA AWAL
-- =====================================================

-- Insert Pimpinan Ranting
INSERT INTO pimpinan_ranting (nama_ranting, alamat, no_telp, email) VALUES
('PR IPM SMP Muhammadiyah 1 Sragen', 'Jl. Raya Sragen No. 1', '0271-123456', 'smp1@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 2 Masaran', 'Jl. Raya Masaran No. 2', '0271-123457', 'smp2@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 3 Sambungmacan', 'Jl. Raya Sambungmacan No. 3', '0271-123458', 'smp3@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 4 Sukodono', 'Jl. Raya Sukodono No. 4', '0271-123459', 'smp4@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 5 Tanon', 'Jl. Raya Tanon No. 5', '0271-123460', 'smp5@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 7 Sumberlawang', 'Jl. Raya Sumberlawang No. 7', '0271-123461', 'smp7@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 9 Gemolong', 'Jl. Raya Gemolong No. 9', '0271-123462', 'smp9@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 11 Kedawung', 'Jl. Raya Kedawung No. 11', '0271-123463', 'smp11@muhammadiyah.sragen.id'),
('PR IPM SMP Muhammadiyah 12 Kalijambe', 'Jl. Raya Kalijambe No. 12', '0271-123464', 'smp12@muhammadiyah.sragen.id'),
('PR IPM SMP Al Basyiir Muhammadiyah Gondang', 'Jl. Raya Gondang No. 13', '0271-123465', 'smp_albas@muhammadiyah.sragen.id'),
('PR IPM SMP Al-Qalam Muhammadiyah Gemolong', 'Jl. Raya Gemolong No. 14', '0271-123466', 'smp_alqalam@muhammadiyah.sragen.id'),
('PR IPM SMP Birrul Walidain Muhammadiyah Plupuh', 'Jl. Raya Plupuh No. 15', '0271-123467', 'smp_birrul@muhammadiyah.sragen.id'),
('PR IPM SMP Birrul Walidain Muh Sragen', 'Jl. Raya Sragen No. 16', '0271-123468', 'smp_birrul2@muhammadiyah.sragen.id'),
('PR IPM SMP Darul Ihsan Muhammadiyah Sragen', 'Jl. Raya Sragen No. 17', '0271-123469', 'smp_darul@muhammadiyah.sragen.id'),
('PR IPM SMP At-Taqwa Muhammadiyah Miri', 'Jl. Raya Miri No. 18', '0271-123470', 'smp_attaqwa@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 1 Gemolong', 'Jl. Raya Gemolong No. 19', '0271-123471', 'mts1@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 2 Kalijambe', 'Jl. Raya Kalijambe No. 20', '0271-123472', 'mts2@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 3 Masaran', 'Jl. Raya Masaran No. 21', '0271-123473', 'mts3@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 4 Sambungmacan', 'Jl. Raya Sambungmacan No. 22', '0271-123474', 'mts4@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 5 Trombol', 'Jl. Raya Trombol No. 23', '0271-123475', 'mts5@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 6 Sidoharjo', 'Jl. Raya Sidoharjo No. 24', '0271-123476', 'mts6@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 7 Sambirejo', 'Jl. Raya Sambirejo No. 25', '0271-123477', 'mts7@muhammadiyah.sragen.id'),
('PR IPM MTs Muhammadiyah 9 Mondokan', 'Jl. Raya Mondokan No. 26', '0271-123478', 'mts9@muhammadiyah.sragen.id'),
('PR IPM SMA Muhammadiyah 1 Sragen', 'Jl. Raya Sragen No. 27', '0271-123479', 'sma1@muhammadiyah.sragen.id'),
('PR IPM SMA Muhammadiyah 2 Gemolong', 'Jl. Raya Gemolong No. 28', '0271-123480', 'sma2@muhammadiyah.sragen.id'),
('PR IPM SMA Muhammadiyah 3 Masaran', 'Jl. Raya Masaran No. 29', '0271-123481', 'sma3@muhammadiyah.sragen.id'),
('PR IPM SMA Muhammadiyah 8 Kalijambe', 'Jl. Raya Kalijambe No. 30', '0271-123482', 'sma8@muhammadiyah.sragen.id'),
('PR IPM SMA Muhammadiyah 9 Sambirejo', 'Jl. Raya Sambirejo No. 31', '0271-123483', 'sma9@muhammadiyah.sragen.id'),
('PR IPM SMA Trensains Muhammadiyah Sragen', 'Jl. Raya Sragen No. 32', '0271-123484', 'sma_trensains@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 1 Sragen', 'Jl. Raya Sragen No. 33', '0271-123485', 'smk1@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 2 Sragen', 'Jl. Raya Sragen No. 34', '0271-123486', 'smk2@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 3 Gemolong', 'Jl. Raya Gemolong No. 35', '0271-123487', 'smk3@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 4 Sragen', 'Jl. Raya Sragen No. 36', '0271-123488', 'smk4@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 5 Miri', 'Jl. Raya Miri No. 37', '0271-123489', 'smk5@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 6 Gemolong', 'Jl. Raya Gemolong No. 38', '0271-123490', 'smk6@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 7 Sambungmacan', 'Jl. Raya Sambungmacan No. 39', '0271-123491', 'smk7@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 8 Tanon', 'Jl. Raya Tanon No. 40', '0271-123492', 'smk8@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 9 Gondang', 'Jl. Raya Gondang No. 41', '0271-123493', 'smk9@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 10 Masaran', 'Jl. Raya Masaran No. 42', '0271-123494', 'smk10@muhammadiyah.sragen.id'),
('PR IPM SMK Muhammadiyah 11 Sumberlawang', 'Jl. Raya Sumberlawang No. 43', '0271-123495', 'smk11@muhammadiyah.sragen.id'),
('PR IPM SMK At-Taqwa Muhammadiyah Miri', 'Jl. Raya Miri No. 44', '0271-123496', 'smk_attaqwa@muhammadiyah.sragen.id'),
('PR IPM MA Darul Ihsan Muhammadiyah Sragen', 'Jl. Raya Sragen No. 45', '0271-123497', 'ma_darul@muhammadiyah.sragen.id'),
('PR IPM PPTQM Darrul Hikmah Muh Masaran', 'Jl. Raya Masaran No. 46', '0271-123498', 'pptqm@muhammadiyah.sragen.id');

-- Insert Pimpinan Cabang
INSERT INTO pimpinan_cabang (nama_cabang, alamat, no_telp, email) VALUES
('PC IPM Sragen Kota', 'Jl. Raya Sragen No. 100', '0271-123500', 'sragen_kota@ipm.sragen.id'),
('PC IPM Sumberlawang', 'Jl. Raya Sumberlawang No. 101', '0271-123501', 'sumberlawang@ipm.sragen.id'),
('PC IPM Kalijambe', 'Jl. Raya Kalijambe No. 102', '0271-123502', 'kalijambe@ipm.sragen.id'),
('PC IPM Gemolong', 'Jl. Raya Gemolong No. 103', '0271-123503', 'gemolong@ipm.sragen.id');

-- Insert Buku Panduan
INSERT INTO books (title, description, file_path, file_size, file_type) VALUES
('Ideologi Gerakan IPM', 'Buku panduan yang menjelaskan landasan ideologis, visi, misi, dan nilai-nilai dasar yang menjadi pedoman dalam setiap gerakan dan aktivitas IPM.', 'https://drive.google.com/uc?export=download&id=1x4W3EafiDLdUkaVmaU_Ls0RtDUEhPkJ7', 2048000, 'pdf'),
('Pedoman Administrasi Kesekretariatan IPM', 'Panduan lengkap untuk mengelola administrasi kesekretariatan IPM, termasuk sistem surat-menyurat, arsip, dan dokumentasi organisasi.', '#', 0, 'pdf'),
('Pedoman Administrasi Keuangan IPM', 'Panduan pengelolaan keuangan organisasi IPM, termasuk sistem akuntansi, pelaporan, dan transparansi keuangan.', '#', 0, 'pdf'),
('Pedoman Persidangan IPM', 'Panduan teknis pelaksanaan persidangan IPM, termasuk tata tertib, prosedur, dan protokol persidangan.', '#', 0, 'pdf'),
('Pedoman Tata Keorganisasian IPM', 'Panduan struktur organisasi, hierarki kepemimpinan, dan tata kelola organisasi IPM di semua tingkatan.', '#', 0, 'pdf'),
('Protokoler Organisasi IPM', 'Panduan protokoler dan tata cara dalam berbagai acara dan kegiatan resmi IPM.', '#', 0, 'pdf'),
('Pedoman Ranting IPM', 'Panduan khusus untuk pengelolaan ranting IPM, termasuk struktur, program, dan aktivitas di tingkat ranting.', '#', 0, 'pdf');

-- Insert Admin Default
INSERT INTO admins (username, email, password, full_name, role) VALUES
('admin', 'admin@ipmtara.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator IPM TARA', 'super_admin');

-- Insert Settings Default
INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'IPM TARA', 'Nama website'),
('site_description', 'Website Resmi Ikatan Pelajar Muhammadiyah TARA', 'Deskripsi website'),
('contact_email', 'info@ipmtara.org', 'Email kontak'),
('contact_phone', '+62 271 123456', 'Nomor telepon kontak'),
('max_file_size', '2097152', 'Ukuran maksimal file upload (dalam bytes)'),
('allowed_file_types', 'jpg,jpeg,png,pdf,doc,docx', 'Tipe file yang diizinkan untuk upload');

-- =====================================================
-- INDEXES UNTUK PERFORMANCE
-- =====================================================

-- Index untuk users
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_created_at ON users(created_at);


-- Index untuk news
CREATE INDEX idx_news_author_id ON news(author_id);
CREATE INDEX idx_news_category ON news(category);
CREATE INDEX idx_news_status ON news(status);
CREATE INDEX idx_news_published_at ON news(published_at);

-- Index untuk activities
CREATE INDEX idx_activities_created_by ON activities(created_by);
CREATE INDEX idx_activities_status ON activities(status);
CREATE INDEX idx_activities_start_date ON activities(start_date);

-- =====================================================
-- VIEWS UNTUK KEMUDAHAN QUERY
-- =====================================================

-- View untuk statistik user
CREATE VIEW v_user_stats AS
SELECT 
    COUNT(*) as total_users,
    COUNT(CASE WHEN status = 'aktif' THEN 1 END) as active_users,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_users,
    COUNT(CASE WHEN jenis_kelamin = 'laki-laki' THEN 1 END) as male_users,
    COUNT(CASE WHEN jenis_kelamin = 'perempuan' THEN 1 END) as female_users
FROM users;


-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure untuk registrasi user baru
DELIMITER //
CREATE PROCEDURE RegisterUser(
    IN p_nama_depan VARCHAR(100),
    IN p_nama_belakang VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_tempat_lahir VARCHAR(100),
    IN p_jenis_kelamin ENUM('laki-laki', 'perempuan'),
    IN p_alamat TEXT,
    IN p_no_hp VARCHAR(20),
    IN p_sekolah VARCHAR(100),
    IN p_nisn VARCHAR(20),
    IN p_pimpinan_ranting VARCHAR(100),
    IN p_pimpinan_cabang VARCHAR(100),
    IN p_foto_path VARCHAR(255)
)
BEGIN
    INSERT INTO users (
        nama_depan, nama_belakang, email, username, password,
        tempat_lahir, jenis_kelamin, alamat, no_hp, sekolah,
        nisn, pimpinan_ranting, pimpinan_cabang, foto_path
    ) VALUES (
        p_nama_depan, p_nama_belakang, p_email, p_username, p_password,
        p_tempat_lahir, p_jenis_kelamin, p_alamat, p_no_hp, p_sekolah,
        p_nisn, p_pimpinan_ranting, p_pimpinan_cabang, p_foto_path
    );
END //
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger untuk update updated_at pada users
DELIMITER //
CREATE TRIGGER tr_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //
DELIMITER ;

-- =====================================================
-- SELESAI
-- =====================================================
-- Database IPM TARA telah siap digunakan!
-- 
-- Cara menggunakan:
-- 1. Buka MySQL Workbench atau phpMyAdmin
-- 2. Jalankan file ini untuk membuat database dan tabel
-- 3. Database akan otomatis terisi dengan data awal
-- 4. Siap untuk diintegrasikan dengan website
--
-- Struktur database ini mendukung:
-- - Sistem registrasi dan login anggota
-- - Manajemen berita dan pengumuman
-- - Manajemen kegiatan
-- - Download buku panduan
-- - Sistem admin
-- - Dan fitur-fitur lainnya
