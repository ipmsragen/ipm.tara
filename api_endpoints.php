<?php
/**
 * API ENDPOINTS IPM TARA
 * File ini berisi API endpoints untuk website IPM TARA
 */

// Include konfigurasi database
require_once 'config_database.php';

// Set header untuk JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =====================================================
// HELPER FUNCTIONS
// =====================================================

/**
 * Mengirim response JSON
 * @param mixed $data
 * @param int $statusCode
 * @param string $message
 */
function sendResponse($data = null, $statusCode = 200, $message = 'Success') {
    http_response_code($statusCode);
    echo json_encode([
        'status' => $statusCode < 400 ? 'success' : 'error',
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

/**
 * Validasi input required
 * @param array $required
 * @param array $data
 * @return array|false
 */
function validateRequired($required, $data) {
    $missing = [];
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        return $missing;
    }
    return false;
}

/**
 * Sanitize input data
 * @param array $data
 * @return array
 */
function sanitizeInput($data) {
    $sanitized = [];
    foreach ($data as $key => $value) {
        $sanitized[$key] = htmlspecialchars(strip_tags(trim($value)));
    }
    return $sanitized;
}

// =====================================================
// ROUTING
// =====================================================

$method = $_SERVER['REQUEST_METHOD'];
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$pathParts = explode('/', trim($path, '/'));

// Remove 'api' from path if present
if (isset($pathParts[0]) && $pathParts[0] === 'api') {
    array_shift($pathParts);
}

$endpoint = isset($pathParts[0]) ? $pathParts[0] : '';
$id = isset($pathParts[1]) ? $pathParts[1] : null;

// =====================================================
// AUTH ENDPOINTS
// =====================================================

if ($endpoint === 'auth') {
    $action = isset($pathParts[1]) ? $pathParts[1] : '';
    
    switch ($action) {
        case 'register':
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendResponse(null, 400, 'Invalid JSON input');
                }
                
                // Validasi required fields
                $required = ['nama_depan', 'nama_belakang', 'email', 'username', 'password', 
                           'tempat_lahir', 'jenis_kelamin', 'alamat', 'no_hp', 'sekolah', 
                           'nisn', 'pimpinan_ranting'];
                
                $missing = validateRequired($required, $input);
                if ($missing) {
                    sendResponse($missing, 400, 'Missing required fields: ' . implode(', ', $missing));
                }
                
                // Validasi data
                if (!validateEmail($input['email'])) {
                    sendResponse(null, 400, 'Invalid email format');
                }
                
                if (!validatePhone($input['no_hp'])) {
                    sendResponse(null, 400, 'Invalid phone number format');
                }
                
                if (!validateNISN($input['nisn'])) {
                    sendResponse(null, 400, 'Invalid NISN format (must be 10 digits)');
                }
                
                if (isUsernameExists($input['username'])) {
                    sendResponse(null, 400, 'Username already exists');
                }
                
                if (isEmailExists($input['email'])) {
                    sendResponse(null, 400, 'Email already exists');
                }
                
                // Sanitize input
                $userData = sanitizeInput($input);
                
                // Register user
                $userId = registerUser($userData);
                
                if ($userId) {
                    sendResponse(['user_id' => $userId], 201, 'User registered successfully');
                } else {
                    sendResponse(null, 500, 'Failed to register user');
                }
            } else {
                sendResponse(null, 405, 'Method not allowed');
            }
            break;
            
        case 'login':
            if ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendResponse(null, 400, 'Invalid JSON input');
                }
                
                if (!isset($input['username']) || !isset($input['password'])) {
                    sendResponse(null, 400, 'Username and password required');
                }
                
                $user = loginUser($input['username'], $input['password']);
                
                if ($user) {
                    // Generate session token (simple example)
                    $token = base64_encode(json_encode([
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'expires' => time() + (24 * 60 * 60) // 24 hours
                    ]));
                    
                    sendResponse([
                        'user' => $user,
                        'token' => $token
                    ], 200, 'Login successful');
                } else {
                    sendResponse(null, 401, 'Invalid credentials');
                }
            } else {
                sendResponse(null, 405, 'Method not allowed');
            }
            break;
            
        default:
            sendResponse(null, 404, 'Endpoint not found');
            break;
    }
}

// =====================================================
// USERS ENDPOINTS
// =====================================================

elseif ($endpoint === 'users') {
    switch ($method) {
        case 'GET':
            if ($id) {
                // Get single user
                $sql = "SELECT id, nama_depan, nama_belakang, email, username, 
                               tempat_lahir, jenis_kelamin, alamat, no_hp, sekolah, 
                               nisn, pimpinan_ranting, pimpinan_cabang, status, created_at 
                        FROM users WHERE id = ?";
                $user = fetchOne($sql, [$id]);
                
                if ($user) {
                    sendResponse($user);
                } else {
                    sendResponse(null, 404, 'User not found');
                }
            } else {
                // Get all users with pagination
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;
                
                $sql = "SELECT id, nama_depan, nama_belakang, email, username, 
                               tempat_lahir, jenis_kelamin, sekolah, pimpinan_ranting, 
                               pimpinan_cabang, status, created_at 
                        FROM users 
                        ORDER BY created_at DESC 
                        LIMIT ? OFFSET ?";
                
                $users = fetchData($sql, [$limit, $offset]);
                
                // Get total count
                $totalSql = "SELECT COUNT(*) as total FROM users";
                $total = fetchOne($totalSql)['total'];
                
                sendResponse([
                    'users' => $users,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            }
            break;
            
        case 'PUT':
            if ($id) {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendResponse(null, 400, 'Invalid JSON input');
                }
                
                // Sanitize input
                $updateData = sanitizeInput($input);
                
                // Build dynamic update query
                $fields = [];
                $values = [];
                
                $allowedFields = ['nama_depan', 'nama_belakang', 'email', 'tempat_lahir', 
                                'jenis_kelamin', 'alamat', 'no_hp', 'sekolah', 'nisn', 
                                'pimpinan_ranting', 'pimpinan_cabang', 'foto_path'];
                
                foreach ($allowedFields as $field) {
                    if (isset($updateData[$field])) {
                        $fields[] = "$field = ?";
                        $values[] = $updateData[$field];
                    }
                }
                
                if (empty($fields)) {
                    sendResponse(null, 400, 'No valid fields to update');
                }
                
                $values[] = $id;
                $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
                
                $stmt = executeQuery($sql, $values);
                
                if ($stmt) {
                    sendResponse(null, 200, 'User updated successfully');
                } else {
                    sendResponse(null, 500, 'Failed to update user');
                }
            } else {
                sendResponse(null, 400, 'User ID required');
            }
            break;
            
        case 'DELETE':
            if ($id) {
                $sql = "UPDATE users SET status = 'non-aktif' WHERE id = ?";
                $stmt = executeQuery($sql, [$id]);
                
                if ($stmt) {
                    sendResponse(null, 200, 'User deactivated successfully');
                } else {
                    sendResponse(null, 500, 'Failed to deactivate user');
                }
            } else {
                sendResponse(null, 400, 'User ID required');
            }
            break;
            
        default:
            sendResponse(null, 405, 'Method not allowed');
            break;
    }
}

// =====================================================
// PIMPINAN RANTING ENDPOINTS
// =====================================================

elseif ($endpoint === 'pimpinan-ranting') {
    if ($method === 'GET') {
        $pimpinanRanting = getPimpinanRanting();
        sendResponse($pimpinanRanting);
    } else {
        sendResponse(null, 405, 'Method not allowed');
    }
}

// =====================================================
// PIMPINAN CABANG ENDPOINTS
// =====================================================

elseif ($endpoint === 'pimpinan-cabang') {
    if ($method === 'GET') {
        $pimpinanCabang = getPimpinanCabang();
        sendResponse($pimpinanCabang);
    } else {
        sendResponse(null, 405, 'Method not allowed');
    }
}

// =====================================================
// BOOKS ENDPOINTS
// =====================================================

elseif ($endpoint === 'books') {
    switch ($method) {
        case 'GET':
            $books = getBooks();
            sendResponse($books);
            break;
            
        case 'POST':
            if ($id && $id === 'download') {
                // Update download count
                $bookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : 0;
                
                if ($bookId > 0) {
                    $success = updateBookDownloadCount($bookId);
                    if ($success) {
                        sendResponse(null, 200, 'Download count updated');
                    } else {
                        sendResponse(null, 500, 'Failed to update download count');
                    }
                } else {
                    sendResponse(null, 400, 'Invalid book ID');
                }
            } else {
                sendResponse(null, 405, 'Method not allowed');
            }
            break;
            
        default:
            sendResponse(null, 405, 'Method not allowed');
            break;
    }
}

// =====================================================
// STATS ENDPOINTS
// =====================================================

elseif ($endpoint === 'stats') {
    if ($method === 'GET') {
        $stats = getWebsiteStats();
        sendResponse($stats);
    } else {
        sendResponse(null, 405, 'Method not allowed');
    }
}

// =====================================================
// FORUM ENDPOINTS
// =====================================================

elseif ($endpoint === 'forum') {
    sendResponse(null, 404, 'Forum feature not available');
}

// =====================================================
// DEFAULT RESPONSE
// =====================================================
else {
    sendResponse(null, 404, 'API endpoint not found');
}

?> 
                        ORDER BY fp.created_at DESC 
                        LIMIT ? OFFSET ?";
                
                $posts = fetchData($sql, [$limit, $offset]);
                
                // Get total count
                $totalSql = "SELECT COUNT(*) as total FROM forum_posts WHERE status = 'published'";
                $total = fetchOne($totalSql)['total'];
                
                sendResponse([
                    'posts' => $posts,
                    'pagination' => [
                        'page' => $page,
                        'limit' => $limit,
                        'total' => $total,
                        'pages' => ceil($total / $limit)
                    ]
                ]);
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendResponse(null, 400, 'Invalid JSON input');
                }
                
                $required = ['user_id', 'title', 'content'];
                $missing = validateRequired($required, $input);
                if ($missing) {
                    sendResponse($missing, 400, 'Missing required fields: ' . implode(', ', $missing));
                }
                
                $sql = "INSERT INTO forum_posts (user_id, title, content, category) VALUES (?, ?, ?, ?)";
                $params = [
                    $input['user_id'],
                    $input['title'],
                    $input['content'],
                    $input['category'] ?? 'umum'
                ];
                
                $stmt = executeQuery($sql, $params);
                
                if ($stmt) {
                    $postId = getDBConnection()->lastInsertId();
                    sendResponse(['post_id' => $postId], 201, 'Post created successfully');
                } else {
                    sendResponse(null, 500, 'Failed to create post');
                }
            } else {
                sendResponse(null, 405, 'Method not allowed');
            }
            break;
            
        case 'comments':
            if ($method === 'GET') {
                $postId = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
                
                if ($postId > 0) {
                    $sql = "SELECT fc.*, u.nama_depan, u.nama_belakang 
                            FROM forum_comments fc 
                            JOIN users u ON fc.user_id = u.id 
                            WHERE fc.post_id = ? AND fc.status = 'published' 
                            ORDER BY fc.created_at ASC";
                    
                    $comments = fetchData($sql, [$postId]);
                    sendResponse($comments);
                } else {
                    sendResponse(null, 400, 'Post ID required');
                }
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    sendResponse(null, 400, 'Invalid JSON input');
                }
                
                $required = ['post_id', 'user_id', 'content'];
                $missing = validateRequired($required, $input);
                if ($missing) {
                    sendResponse($missing, 400, 'Missing required fields: ' . implode(', ', $missing));
                }
                
                $sql = "INSERT INTO forum_comments (post_id, user_id, content) VALUES (?, ?, ?)";
                $params = [$input['post_id'], $input['user_id'], $input['content']];
                
                $stmt = executeQuery($sql, $params);
                
                if ($stmt) {
                    $commentId = getDBConnection()->lastInsertId();
                    sendResponse(['comment_id' => $commentId], 201, 'Comment created successfully');
                } else {
                    sendResponse(null, 500, 'Failed to create comment');
                }
            } else {
                sendResponse(null, 405, 'Method not allowed');
            }
            break;
            
        default:
            sendResponse(null, 404, 'Endpoint not found');
            break;
    }
}

// =====================================================
// DEFAULT RESPONSE
// =====================================================

else {
    sendResponse(null, 404, 'API endpoint not found');
}

?>
