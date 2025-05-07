<?php
// Database configuration
define('DB_FILE', 'data/users.json');

// Set session lifetime to 30 minutes
ini_set('session.gc_maxlifetime', 1800);
session_set_cookie_params(1800);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create data directory if it doesn't exist
if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Initialize users database if it doesn't exist
if (!file_exists(DB_FILE)) {
    $defaultUsers = [
        [
            'id' => 1,
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'fullname' => 'Quản trị viên',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'username' => 'user',
            'password' => password_hash('user123', PASSWORD_DEFAULT),
            'fullname' => 'Người dùng',
            'role' => 'user',
            'email' => 'user@example.com',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    file_put_contents(DB_FILE, json_encode($defaultUsers, JSON_PRETTY_PRINT));
}

// Application settings
define('APP_NAME', 'Hệ thống Nhật ký Hoạt động');
define('EMAIL_NOTIFICATIONS', true);
define('ADMIN_EMAIL', 'admin@example.com');
define('LOG_RETENTION_DAYS', 90);

/**
 * Check if the user is authenticated
 *
 * @return bool True if authenticated, false otherwise
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if the user has admin role
 *
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get all users from database
 *
 * @return array Array of users
 */
function getAllUsers() {
    if (file_exists(DB_FILE)) {
        $users = json_decode(file_get_contents(DB_FILE), true);
        return $users ? $users : [];
    }
    return [];
}

/**
 * Get user by ID
 *
 * @param int $id User ID
 * @return array|null User data or null if not found
 */
function getUserById($id) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['id'] == $id) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by username
 *
 * @param string $username Username
 * @return array|null User data or null if not found
 */
function getUserByUsername($username) {
    $users = getAllUsers();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

/**
 * Redirect to login page if not authenticated
 */
function requireLogin() {
    if (!isAuthenticated()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: login.php');
        exit;
    }
}

/**
 * Redirect to dashboard if already authenticated
 */
function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * Check if user has admin privileges, redirect if not
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['flash_message'] = 'Bạn không có quyền truy cập trang này.';
        $_SESSION['flash_type'] = 'danger';
        header('Location: index.php');
        exit;
    }
}
?>