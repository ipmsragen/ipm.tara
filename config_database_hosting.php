<?php
// =====================================================
// KONFIGURASI DATABASE UNTUK HOSTING
// =====================================================
// File ini khusus untuk hosting, edit sesuai dengan info hosting Anda

// =====================================================
// KONFIGURASI DATABASE - EDIT BAGIAN INI
// =====================================================

// Host database (biasanya localhost atau IP hosting)
define('DB_HOST', 'localhost');

// Nama database (buat di cPanel atau panel hosting)
define('DB_NAME', 'ipm_tara');

// Username database (dari hosting)
define('DB_USER', 'your_username');

// Password database (dari hosting)
define('DB_PASS', 'your_password');

// Charset database
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// KONFIGURASI TAMBAHAN UNTUK HOSTING
// =====================================================

// URL website (ganti dengan domain Anda)
define('SITE_URL', 'https://yourdomain.com');

// Path upload (sesuaikan dengan hosting)
define('UPLOAD_PATH', 'uploads/');

// Max file size (dalam bytes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Allowed file types
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);

// =====================================================
// FUNGSI KONEKSI DATABASE
// =====================================================

function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    return $pdo;
}

// =====================================================
// FUNGSI HELPER
// =====================================================

function fetchData($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return [];
    }
}

function fetchOne($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return false;
    }
}

function executeQuery($sql, $params = []) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        return false;
    }
}

// =====================================================
// FUNGSI UPLOAD FILE
// =====================================================

function uploadFile($file, $targetDir = UPLOAD_PATH) {
    // Buat folder upload jika belum ada
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Validasi file
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, ALLOWED_FILE_TYPES)) {
        return ['success' => false, 'message' => 'File type not allowed'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    $targetPath = $targetDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $targetPath];
    } else {
        return ['success' => false, 'message' => 'Upload failed'];
    }
}

// =====================================================
// FUNGSI RESPONSE API
// =====================================================

function sendResponse($data = null, $statusCode = 200, $message = 'Success') {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    
    $response = [
        'status' => $statusCode < 400 ? 'success' : 'error',
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// =====================================================
// FUNGSI VALIDASI
// =====================================================

function validateRequired($required, $data) {
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return empty($missing) ? false : $missing;
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// =====================================================
// FUNGSI STATISTIK
// =====================================================

function getWebsiteStats() {
    $stats = [];
    
    try {
        // Total users
        $stats['total_users'] = fetchOne("SELECT COUNT(*) as count FROM users")['count'];
        $stats['active_users'] = fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'aktif'")['count'];
        
        // Total books
        $stats['total_books'] = fetchOne("SELECT COUNT(*) as count FROM books WHERE status = 'active'")['count'];
        
        return $stats;
    } catch (Exception $e) {
        error_log("Stats error: " . $e->getMessage());
        return ['error' => 'Failed to get statistics'];
    }
}

?>
