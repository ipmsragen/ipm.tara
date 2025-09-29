<?php
/**
 * KONFIGURASI DATABASE IPM TARA
 * File ini berisi konfigurasi koneksi database untuk website IPM TARA
 */

// =====================================================
// KONFIGURASI DATABASE - EDIT BAGIAN INI
// =====================================================

// Host database (biasanya localhost)
define('DB_HOST', 'localhost');

// Nama database (harus sudah dibuat di MySQL)
define('DB_NAME', 'ipm_tara');

// Username MySQL (biasanya root)
define('DB_USER', 'root');

// Password MySQL (kosongkan jika tidak ada password)
define('DB_PASS', '');

// Charset database
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// KONEKSI DATABASE
// =====================================================

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $charset = DB_CHARSET;
    public $conn;

    /**
     * Membuat koneksi ke database
     * @return PDO|null
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            echo "Koneksi database gagal: " . $exception->getMessage();
            return null;
        }

        return $this->conn;
    }

    /**
     * Test koneksi database
     * @return bool
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                echo "✅ Koneksi database berhasil!";
                return true;
            }
        } catch(Exception $e) {
            echo "❌ Koneksi database gagal: " . $e->getMessage();
            return false;
        }
        return false;
    }
}

// =====================================================
// FUNGSI HELPER DATABASE
// =====================================================

/**
 * Mendapatkan koneksi database
 * @return PDO
 */
function getDBConnection() {
    $database = new Database();
    return $database->getConnection();
}

/**
 * Menjalankan query dengan prepared statement
 * @param string $sql
 * @param array $params
 * @return PDOStatement|false
 */
function executeQuery($sql, $params = []) {
    try {
        $conn = getDBConnection();
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        error_log("Query error: " . $e->getMessage());
        return false;
    }
}

/**
 * Mendapatkan data dari query
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function fetchData($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return $stmt->fetchAll();
    }
    return false;
}

/**
 * Mendapatkan satu baris data
 * @param string $sql
 * @param array $params
 * @return array|false
 */
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return $stmt->fetch();
    }
    return false;
}

/**
 * Menghitung jumlah baris
 * @param string $sql
 * @param array $params
 * @return int
 */
function countRows($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return $stmt->rowCount();
    }
    return 0;
}

// =====================================================
// FUNGSI KHUSUS IPM TARA
// =====================================================

/**
 * Registrasi anggota baru
 * @param array $userData
 * @return bool|int
 */
function registerUser($userData) {
    $sql = "INSERT INTO users (
        nama_depan, nama_belakang, email, username, password,
        tempat_lahir, jenis_kelamin, alamat, no_hp, sekolah,
        nisn, pimpinan_ranting, pimpinan_cabang, foto_path
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = [
        $userData['nama_depan'],
        $userData['nama_belakang'],
        $userData['email'],
        $userData['username'],
        password_hash($userData['password'], PASSWORD_DEFAULT),
        $userData['tempat_lahir'],
        $userData['jenis_kelamin'],
        $userData['alamat'],
        $userData['no_hp'],
        $userData['sekolah'],
        $userData['nisn'],
        $userData['pimpinan_ranting'],
        $userData['pimpinan_cabang'] ?? null,
        $userData['foto_path'] ?? null
    ];
    
    $stmt = executeQuery($sql, $params);
    if ($stmt) {
        return getDBConnection()->lastInsertId();
    }
    return false;
}

/**
 * Login user
 * @param string $username
 * @param string $password
 * @return array|false
 */
function loginUser($username, $password) {
    $sql = "SELECT id, username, email, password, status, nama_depan, nama_belakang 
            FROM users 
            WHERE (username = ? OR email = ?) AND status = 'aktif'";
    
    $user = fetchOne($sql, [$username, $username]);
    
    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']); // Hapus password dari hasil
        return $user;
    }
    
    return false;
}

/**
 * Mendapatkan daftar pimpinan ranting
 * @return array
 */
function getPimpinanRanting() {
    $sql = "SELECT id, nama_ranting, alamat, no_telp, email 
            FROM pimpinan_ranting 
            WHERE status = 'aktif' 
            ORDER BY nama_ranting";
    
    return fetchData($sql);
}

/**
 * Mendapatkan daftar pimpinan cabang
 * @return array
 */
function getPimpinanCabang() {
    $sql = "SELECT id, nama_cabang, alamat, no_telp, email 
            FROM pimpinan_cabang 
            WHERE status = 'aktif' 
            ORDER BY nama_cabang";
    
    return fetchData($sql);
}

/**
 * Mendapatkan daftar buku panduan
 * @return array
 */
function getBooks() {
    $sql = "SELECT id, title, description, file_path, file_size, file_type, download_count 
            FROM books 
            WHERE status = 'active' 
            ORDER BY title";
    
    return fetchData($sql);
}

/**
 * Update download count buku
 * @param int $bookId
 * @return bool
 */
function updateBookDownloadCount($bookId) {
    $sql = "UPDATE books SET download_count = download_count + 1 WHERE id = ?";
    $stmt = executeQuery($sql, [$bookId]);
    return $stmt !== false;
}

/**
 * Mendapatkan statistik website
 * @return array
 */
function getWebsiteStats() {
    $stats = [];
    
    // Total users
    $stats['total_users'] = fetchOne("SELECT COUNT(*) as count FROM users")['count'];
    $stats['active_users'] = fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'aktif'")['count'];
    
    
    // Total books
    $stats['total_books'] = fetchOne("SELECT COUNT(*) as count FROM books WHERE status = 'active'")['count'];
    
    return $stats;
}

// =====================================================
// VALIDASI DATA
// =====================================================

/**
 * Validasi email
 * @param string $email
 * @return bool
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validasi nomor telepon Indonesia
 * @param string $phone
 * @return bool
 */
function validatePhone($phone) {
    $pattern = '/^(\+62|62|0)[0-9]{9,13}$/';
    return preg_match($pattern, $phone);
}

/**
 * Validasi NISN
 * @param string $nisn
 * @return bool
 */
function validateNISN($nisn) {
    return preg_match('/^[0-9]{10}$/', $nisn);
}

/**
 * Cek apakah username sudah ada
 * @param string $username
 * @return bool
 */
function isUsernameExists($username) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $result = fetchOne($sql, [$username]);
    return $result['count'] > 0;
}

/**
 * Cek apakah email sudah ada
 * @param string $email
 * @return bool
 */
function isEmailExists($email) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $result = fetchOne($sql, [$email]);
    return $result['count'] > 0;
}

// =====================================================
// CONTOH PENGGUNAAN
// =====================================================

/*
// Test koneksi database
$database = new Database();
$database->testConnection();

// Registrasi user baru
$userData = [
    'nama_depan' => 'Ahmad',
    'nama_belakang' => 'Fauzi',
    'email' => 'ahmad@email.com',
    'username' => 'ahmadfauzi',
    'password' => 'password123',
    'tempat_lahir' => 'Sragen',
    'jenis_kelamin' => 'laki-laki',
    'alamat' => 'Jl. Contoh No. 1',
    'no_hp' => '08123456789',
    'sekolah' => 'SMA Muhammadiyah 1 Sragen',
    'nisn' => '1234567890',
    'pimpinan_ranting' => 'PR IPM SMA Muhammadiyah 1 Sragen',
    'pimpinan_cabang' => 'PC IPM Sragen Kota'
];

$userId = registerUser($userData);
if ($userId) {
    echo "Registrasi berhasil! ID: " . $userId;
} else {
    echo "Registrasi gagal!";
}

// Login user
$user = loginUser('ahmadfauzi', 'password123');
if ($user) {
    echo "Login berhasil! Selamat datang " . $user['nama_depan'];
} else {
    echo "Login gagal!";
}

// Mendapatkan daftar pimpinan ranting
$pimpinanRanting = getPimpinanRanting();
foreach ($pimpinanRanting as $pr) {
    echo $pr['nama_ranting'] . " - " . $pr['alamat'] . "\n";
}
*/

?>
