<?php
/**
 * SCRIPT OTOMATIS SETUP DATABASE IPM TARA
 * Script ini akan membantu setup database secara otomatis
 */

echo "<h1>🔧 Setup Database IPM TARA</h1>";
echo "<hr>";

// =====================================================
// KONFIGURASI DATABASE
// =====================================================

$config = [
    'host' => 'localhost',
    'dbname' => 'ipm_tara',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

echo "<h2>📊 Konfigurasi Database</h2>";
echo "<p><strong>Host:</strong> " . $config['host'] . "</p>";
echo "<p><strong>Database:</strong> " . $config['dbname'] . "</p>";
echo "<p><strong>Username:</strong> " . $config['username'] . "</p>";
echo "<p><strong>Password:</strong> " . ($config['password'] ? '***' : '(kosong)') . "</p>";
echo "<hr>";

// =====================================================
// TEST KONEKSI MYSQL
// =====================================================

echo "<h2>🔍 Test Koneksi MySQL</h2>";

try {
    $dsn = "mysql:host=" . $config['host'] . ";charset=" . $config['charset'];
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Koneksi MySQL berhasil!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Koneksi MySQL gagal: " . $e->getMessage() . "</p>";
    echo "<p><strong>Solusi:</strong></p>";
    echo "<ul>";
    echo "<li>Pastikan MySQL server berjalan</li>";
    echo "<li>Cek username dan password MySQL</li>";
    echo "<li>Cek host database</li>";
    echo "</ul>";
    exit();
}

// =====================================================
// BUAT DATABASE
// =====================================================

echo "<h2>🗄️ Membuat Database</h2>";

try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $config['dbname'] . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color: green;'>✅ Database '" . $config['dbname'] . "' berhasil dibuat!</p>";
} catch(PDOException $e) {
    echo "<p style='color: orange;'>⚠️ Database sudah ada atau error: " . $e->getMessage() . "</p>";
}

// =====================================================
// KONEKSI KE DATABASE
// =====================================================

echo "<h2>🔗 Koneksi ke Database</h2>";

try {
    $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=" . $config['charset'];
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Koneksi ke database berhasil!</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>❌ Koneksi ke database gagal: " . $e->getMessage() . "</p>";
    exit();
}

// =====================================================
// IMPORT SCHEMA DATABASE
// =====================================================

echo "<h2>📥 Import Schema Database</h2>";

$schemaFile = 'database_schema.sql';
if (!file_exists($schemaFile)) {
    echo "<p style='color: red;'>❌ File schema tidak ditemukan: " . $schemaFile . "</p>";
    exit();
}

$schema = file_get_contents($schemaFile);
if ($schema === false) {
    echo "<p style='color: red;'>❌ Gagal membaca file schema</p>";
    exit();
}

// Split SQL statements
$statements = array_filter(array_map('trim', explode(';', $schema)));
$successCount = 0;
$errorCount = 0;

foreach ($statements as $statement) {
    if (empty($statement) || strpos($statement, '--') === 0) {
        continue;
    }
    
    try {
        $pdo->exec($statement);
        $successCount++;
    } catch(PDOException $e) {
        $errorCount++;
        echo "<p style='color: orange;'>⚠️ Error: " . $e->getMessage() . "</p>";
    }
}

echo "<p style='color: green;'>✅ Berhasil menjalankan " . $successCount . " statement SQL</p>";
if ($errorCount > 0) {
    echo "<p style='color: orange;'>⚠️ " . $errorCount . " statement mengalami error</p>";
}

// =====================================================
// VERIFIKASI TABEL
// =====================================================

echo "<h2>🔍 Verifikasi Tabel</h2>";

$expectedTables = [
    'users', 'pimpinan_ranting', 'pimpinan_cabang', 'books',
    'news',
    'activities', 'activity_registrations', 'admins', 'settings'
];

$stmt = $pdo->query("SHOW TABLES");
$existingTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "<p><strong>Tabel yang ada:</strong></p>";
echo "<ul>";
foreach ($existingTables as $table) {
    echo "<li>✅ " . $table . "</li>";
}
echo "</ul>";

$missingTables = array_diff($expectedTables, $existingTables);
if (!empty($missingTables)) {
    echo "<p style='color: red;'><strong>Tabel yang hilang:</strong></p>";
    echo "<ul>";
    foreach ($missingTables as $table) {
        echo "<li>❌ " . $table . "</li>";
    }
    echo "</ul>";
}

// =====================================================
// VERIFIKASI DATA AWAL
// =====================================================

echo "<h2>📊 Verifikasi Data Awal</h2>";

$dataChecks = [
    'pimpinan_ranting' => 'Pimpinan Ranting',
    'pimpinan_cabang' => 'Pimpinan Cabang',
    'books' => 'Buku Panduan',
    'admins' => 'Admin',
    'settings' => 'Settings'
];

foreach ($dataChecks as $table => $name) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "<p>📋 $name: <strong>$count</strong> data</p>";
    } catch(PDOException $e) {
        echo "<p style='color: red;'>❌ Error cek $name: " . $e->getMessage() . "</p>";
    }
}

// =====================================================
// TEST FUNGSI DATABASE
// =====================================================

echo "<h2>🧪 Test Fungsi Database</h2>";

// Include config database
require_once 'config_database.php';

// Test koneksi
$database = new Database();
if ($database->testConnection()) {
    echo "<p style='color: green;'>✅ Test koneksi berhasil!</p>";
} else {
    echo "<p style='color: red;'>❌ Test koneksi gagal!</p>";
}

// Test fungsi helper
try {
    $pimpinanRanting = getPimpinanRanting();
    echo "<p>📋 Pimpinan Ranting: <strong>" . count($pimpinanRanting) . "</strong> data</p>";
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error getPimpinanRanting: " . $e->getMessage() . "</p>";
}

try {
    $pimpinanCabang = getPimpinanCabang();
    echo "<p>📋 Pimpinan Cabang: <strong>" . count($pimpinanCabang) . "</strong> data</p>";
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error getPimpinanCabang: " . $e->getMessage() . "</p>";
}

try {
    $books = getBooks();
    echo "<p>📚 Buku Panduan: <strong>" . count($books) . "</strong> data</p>";
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error getBooks: " . $e->getMessage() . "</p>";
}

// =====================================================
// KESIMPULAN
// =====================================================

echo "<hr>";
echo "<h2>🎉 Kesimpulan Setup</h2>";

$totalTables = count($existingTables);
$expectedTableCount = count($expectedTables);

if ($totalTables >= $expectedTableCount) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 5px solid #28a745;'>";
    echo "<h3 style='color: #155724;'>✅ Database Setup Berhasil!</h3>";
    echo "<p>Database IPM TARA telah berhasil dikonfigurasi dan siap digunakan.</p>";
    echo "<p><strong>Langkah selanjutnya:</strong></p>";
    echo "<ul>";
    echo "<li>Buka website: <a href='index.html'>http://localhost/website-ipm-sra/</a></li>";
    echo "<li>Test registrasi anggota baru</li>";
    echo "<li>Test login dengan akun yang dibuat</li>";
    echo "<li>Test fitur website</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; border-left: 5px solid #dc3545;'>";
    echo "<h3 style='color: #721c24;'>❌ Database Setup Gagal!</h3>";
    echo "<p>Beberapa tabel tidak berhasil dibuat. Silakan cek error di atas.</p>";
    echo "<p><strong>Solusi:</strong></p>";
    echo "<ul>";
    echo "<li>Cek file database_schema.sql</li>";
    echo "<li>Pastikan MySQL server berjalan</li>";
    echo "<li>Cek permission database</li>";
    echo "<li>Jalankan ulang script ini</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Setup selesai pada: " . date('Y-m-d H:i:s') . "</em></p>";

?>

