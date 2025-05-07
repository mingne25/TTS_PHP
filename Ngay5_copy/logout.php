<?php
require_once 'includes/config.php';
require_once 'includes/logger.php';

// Log the logout activity if user is logged in
if (isAuthenticated()) {
    logActivity('Đăng xuất khỏi hệ thống');
}

// Destroy session
session_start();
$_SESSION = [];

// If session cookie is used, destroy it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit;
?>