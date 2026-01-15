<?php
/**
 * User Authentication Check
 * Include at top of all /user/* pages
 */

// Define paths if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 3) . '/');
}
if (!defined('SHARED_PATH')) {
    define('SHARED_PATH', BASE_PATH . 'shared/');
}

// Load configuration if not already loaded
if (!class_exists('User')) {
    require_once SHARED_PATH . 'config/database.php';
    require_once SHARED_PATH . 'config/session.php';
    require_once SHARED_PATH . 'config/autoload.php';
}

// Check if user is logged in
if (!User::isLoggedIn()) {
    // Redirect to login with return URL
    $returnUrl = urlencode($_SERVER['REQUEST_URI']);
    header("Location: /auth/login?redirect={$returnUrl}");
    exit;
}

// User is authenticated - set variables for convenience
$currentUserId = $_SESSION['user_id'];
$currentUsername = $_SESSION['username'] ?? 'User';
$currentUserEmail = $_SESSION['email'] ?? '';
$currentUserRole = $_SESSION['role'] ?? 'user';
