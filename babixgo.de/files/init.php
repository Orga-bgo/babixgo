<?php
/**
 * Initialization file for BabixGO Files
 * Sets up paths, loads shared classes, and initializes session
 */

// Define paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__) . '/');
}
if (!defined('SHARED_PATH')) {
    define('SHARED_PATH', BASE_PATH . 'shared/');
}
if (!defined('FILES_PATH')) {
    define('FILES_PATH', __DIR__ . '/');
}

// Load shared configuration and classes
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Load files-specific includes for backward compatibility
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// Initialize session (already done by shared/config/session.php, but call again if needed)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper wrapper for User class
if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return User::isLoggedIn();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool {
        return User::isAdmin();
    }
}

if (!function_exists('getCurrentUserId')) {
    function getCurrentUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
}

if (!function_exists('initSession')) {
    function initSession(): void {
        // Already initialized by shared config, no-op
    }
}
