<?php
/**
 * Authentication Check
 * Include this file to require user authentication
 */

// Define paths
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

// Load configuration
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Check if user is logged in
if (!User::isLoggedIn()) {
    header('Location: /auth/login');
    exit;
}
