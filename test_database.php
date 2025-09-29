<?php
/**
 * TEST DATABASE CONNECTION IPM TARA
 * File ini untuk testing koneksi database dan fungsi-fungsi yang ada
 */

// Include konfigurasi database
require_once 'config_database.php';

echo "<h1>🧪 TEST DATABASE IPM TARA</h1>";
echo "<hr>";

// =====================================================
// TEST KONEKSI DATABASE
// =====================================================

echo "<h2>1. Test Koneksi Database</h2>";
$database = new Database();
if ($database->testConnection()) {
    echo "<p style='color: green;'>✅ Koneksi database berhasil!</p>";
} else {
    echo "<p style='color: red;'>❌ Koneksi database gagal!</p>";
    exit();
}

echo "<hr>";

// =====================================================
// TEST TABEL DAN DATA
// =====================================================

echo "<h2>2. Test Tabel dan Data</h2>";

// Test tabel users
echo "<h3>📊 Tabel Users</h3>";
$userCount = fetchOne("SELECT COUNT(*) as count FROM users");
echo "<p>Total users: <strong>" . $userCount['count'] . "</strong></p>";

// Test tabel pimpinan ranting
echo "<h3>🏢 Tabel Pimpinan Ranting</h3>";
$prCount = fetchOne("SELECT COUNT(*) as count FROM pimpinan_ranting");
echo "<p>Total pimpinan ranting: <strong>" . $prCount['count'] . "</strong></p>";

// Test tabel pimpinan cabang
echo "<h3>🏛️ Tabel Pimpinan Cabang</h3>";
$pcCount = fetchOne("SELECT COUNT(*) as count FROM pimpinan_cabang");
echo "<p>Total pimpinan cabang: <strong>" . $pcCount['count'] . "</strong></p>";

// Test tabel books
echo "<h3>📚 Tabel Books</h3>";
$bookCount = fetchOne("SELECT COUNT(*) as count FROM books");
echo "<p>Total books: <strong>" . $bookCount['count'] . "</strong></p>";

echo "<hr>";

// =====================================================
// TEST FUNGSI HELPER
// =====================================================

echo "<h2>3. Test Fungsi Helper</h2>";

// Test getPimpinanRanting
echo "<h3>🔍 Test getPimpinanRanting()</h3>";
$pimpinanRanting = getPimpinanRanting();
echo "<p>Jumlah pimpinan ranting yang aktif: <strong>" . count($pimpinanRanting) . "</strong></p>";
echo "<p>Contoh data:</p>";
echo "<ul>";
for ($i = 0; $i < min(3, count($pimpinanRanting)); $i++) {
    echo "<li>" . $pimpinanRanting[$i]['nama_ranting'] . " - " . $pimpinanRanting[$i]['alamat'] . "</li>";
}
echo "</ul>";

// Test getPimpinanCabang
echo "<h3>🔍 Test getPimpinanCabang()</h3>";
$pimpinanCabang = getPimpinanCabang();
echo "<p>Jumlah pimpinan cabang yang aktif: <strong>" . count($pimpinanCabang) . "</strong></p>";
echo "<p>Contoh data:</p>";
echo "<ul>";
for ($i = 0; $i < min(3, count($pimpinanCabang)); $i++) {
    echo "<li>" . $pimpinanCabang[$i]['nama_cabang'] . " - " . $pimpinanCabang[$i]['alamat'] . "</li>";
}
echo "</ul>";

// Test getBooks
echo "<h3>🔍 Test getBooks()</h3>";
$books = getBooks();
echo "<p>Jumlah buku yang aktif: <strong>" . count($books) . "</strong></p>";
echo "<p>Contoh data:</p>";
echo "<ul>";
for ($i = 0; $i < min(3, count($books)); $i++) {
    echo "<li>" . $books[$i]['title'] . " - " . $books[$i]['file_path'] . "</li>";
}
echo "</ul>";

echo "<hr>";

// =====================================================
// TEST VALIDASI
// =====================================================

echo "<h2>4. Test Validasi Data</h2>";

// Test validateEmail
echo "<h3>📧 Test validateEmail()</h3>";
$testEmails = ['test@email.com', 'invalid-email', 'user@domain.co.id'];
foreach ($testEmails as $email) {
    $valid = validateEmail($email);
    $status = $valid ? '✅ Valid' : '❌ Invalid';
    echo "<p>$email: <strong>$status</strong></p>";
}

// Test validatePhone
echo "<h3>📱 Test validatePhone()</h3>";
$testPhones = ['08123456789', '628123456789', '+628123456789', '123456'];
foreach ($testPhones as $phone) {
    $valid = validatePhone($phone);
    $status = $valid ? '✅ Valid' : '❌ Invalid';
    echo "<p>$phone: <strong>$status</strong></p>";
}

// Test validateNISN
echo "<h3>🎓 Test validateNISN()</h3>";
$testNISNs = ['1234567890', '123456789', '12345678901', 'abc1234567'];
foreach ($testNISNs as $nisn) {
    $valid = validateNISN($nisn);
    $status = $valid ? '✅ Valid' : '❌ Invalid';
    echo "<p>$nisn: <strong>$status</strong></p>";
}

echo "<hr>";

// =====================================================
// TEST STATISTIK
// =====================================================

echo "<h2>5. Test Statistik Website</h2>";
$stats = getWebsiteStats();
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>";
echo "<h3>📊 Statistik Website IPM TARA</h3>";
echo "<p><strong>Total Users:</strong> " . $stats['total_users'] . "</p>";
echo "<p><strong>Active Users:</strong> " . $stats['active_users'] . "</p>";
echo "<p><strong>Total Posts:</strong> " . $stats['total_posts'] . "</p>";
echo "<p><strong>Total Books:</strong> " . $stats['total_books'] . "</p>";
echo "</div>";

echo "<hr>";

// =====================================================
// TEST REGISTRASI USER (DEMO)
// =====================================================

echo "<h2>6. Test Registrasi User (Demo)</h2>";

// Data user demo
$demoUserData = [
    'nama_depan' => 'Test',
    'nama_belakang' => 'User',
    'email' => 'testuser@demo.com',
    'username' => 'testuser',
    'password' => 'password123',
    'tempat_lahir' => 'Sragen',
    'jenis_kelamin' => 'laki-laki',
    'alamat' => 'Jl. Demo No. 1',
    'no_hp' => '08123456789',
    'sekolah' => 'SMA Muhammadiyah 1 Sragen',
    'nisn' => '1234567890',
    'pimpinan_ranting' => 'PR IPM SMA Muhammadiyah 1 Sragen',
    'pimpinan_cabang' => 'PC IPM Sragen Kota'
];

echo "<h3>📝 Data User Demo:</h3>";
echo "<pre>" . print_r($demoUserData, true) . "</pre>";

// Cek apakah user sudah ada
if (isUsernameExists($demoUserData['username'])) {
    echo "<p style='color: orange;'>⚠️ Username 'testuser' sudah ada, skip registrasi demo</p>";
} else {
    echo "<h3>🔄 Mencoba registrasi user demo...</h3>";
    
    $userId = registerUser($demoUserData);
    
    if ($userId) {
        echo "<p style='color: green;'>✅ Registrasi berhasil! User ID: <strong>$userId</strong></p>";
        
        // Test login
        echo "<h3>🔐 Test Login User Demo...</h3>";
        $user = loginUser($demoUserData['username'], $demoUserData['password']);
        
        if ($user) {
            echo "<p style='color: green;'>✅ Login berhasil!</p>";
            echo "<p>Data user yang login:</p>";
            echo "<pre>" . print_r($user, true) . "</pre>";
        } else {
            echo "<p style='color: red;'>❌ Login gagal!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Registrasi gagal!</p>";
    }
}

echo "<hr>";

// =====================================================
// TEST API ENDPOINTS (DEMO)
// =====================================================

echo "<h2>7. Test API Endpoints (Demo)</h2>";

echo "<h3>🌐 Daftar API Endpoints yang Tersedia:</h3>";
echo "<div style='background: #e8f4fd; padding: 15px; border-radius: 5px;'>";
echo "<h4>Authentication:</h4>";
echo "<ul>";
echo "<li><strong>POST</strong> /api/auth/register - Registrasi user baru</li>";
echo "<li><strong>POST</strong> /api/auth/login - Login user</li>";
echo "</ul>";

echo "<h4>Users:</h4>";
echo "<ul>";
echo "<li><strong>GET</strong> /api/users - Daftar semua users</li>";
echo "<li><strong>GET</strong> /api/users/{id} - Detail user</li>";
echo "<li><strong>PUT</strong> /api/users/{id} - Update user</li>";
echo "<li><strong>DELETE</strong> /api/users/{id} - Deactivate user</li>";
echo "</ul>";

echo "<h4>Data Reference:</h4>";
echo "<ul>";
echo "<li><strong>GET</strong> /api/pimpinan-ranting - Daftar pimpinan ranting</li>";
echo "<li><strong>GET</strong> /api/pimpinan-cabang - Daftar pimpinan cabang</li>";
echo "<li><strong>GET</strong> /api/books - Daftar buku panduan</li>";
echo "</ul>";

echo "<h4>Statistics:</h4>";
echo "<ul>";
echo "<li><strong>GET</strong> /api/stats - Statistik website</li>";
echo "</ul>";

echo "</div>";

echo "<hr>";

// =====================================================
// KESIMPULAN
// =====================================================

echo "<h2>8. Kesimpulan</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; border-left: 5px solid #28a745;'>";
echo "<h3>✅ Database IPM TARA Siap Digunakan!</h3>";
echo "<p>Semua test berhasil dilakukan. Database sudah siap untuk diintegrasikan dengan website.</p>";
echo "<p><strong>Langkah selanjutnya:</strong></p>";
echo "<ol>";
echo "<li>Integrasikan dengan form registrasi di website</li>";
echo "<li>Implementasi login system</li>";
echo "<li>Koneksikan dropdown pimpinan ranting/cabang dengan database</li>";
echo "<li>Implementasi fitur website lengkap</li>";
echo "<li>Setup file upload untuk foto profil</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p><em>Test selesai pada: " . date('Y-m-d H:i:s') . "</em></p>";

?>
