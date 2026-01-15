<?php
/**
 * Admin Authorization Check
 * Include at top of all /admin/* pages
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
    // Not logged in - redirect to login
    header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Check if user has admin role
if (!User::isAdmin()) {
    // Logged in but not admin - show 403
    http_response_code(403);
    die('
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Zugriff verweigert</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; background: #1a1a1a; color: #fff; }
            h1 { color: #ef4444; }
            a { color: #6366f1; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
    </head>
    <body>
        <h1>403 - Zugriff verweigert</h1>
        <p>Sie haben keine Berechtigung für den Admin-Bereich.</p>
        <p><a href="/">Zurück zur Startseite</a></p>
    </body>
    </html>
    ');
}

// Admin is authenticated and authorized
$adminUserId = $_SESSION['user_id'];
$adminUsername = $_SESSION['username'] ?? 'Admin';
