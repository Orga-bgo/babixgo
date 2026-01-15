<?php
/**
 * Configuration file for BabixGO Files
 * Contains database, SMTP, and application settings
 * 
 * IMPORTANT: Set environment variables on the server:
 * - DB_HOST, DB_NAME, DB_USER, DB_PASSWORD
 * - SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_KEY
 */

// Prevent direct access
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', dirname(__DIR__));
}

// Database Configuration - Use environment variables already loaded by shared config
// The shared/config/database.php (loaded in init.php) handles .env file loading
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'babixgo_files');
define('DB_USER', getenv('DB_USER') ?: 'root');
// Support both DB_PASSWORT (German) and DB_PASSWORD (English)
$dbPassword = getenv('DB_PASSWORT') ?: getenv('DB_PASSWORD') ?: '';
define('DB_PASS', $dbPassword);
define('DB_CHARSET', 'utf8mb4');

// SMTP Configuration (Brevo) - Use environment variables
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_KEY') ?: '');

// Application Settings
define('SITE_URL', getenv('SITE_URL') ?: 'https://files.babixgo.de');
define('SITE_NAME', 'BabixGO Files');

// Session Settings
define('SESSION_LIFETIME', 86400); // 24 hours
define('SESSION_NAME', 'babixgo_files_session');

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('PASSWORD_MIN_LENGTH', 8);

// Rate Limiting
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// File Upload Settings
define('UPLOAD_MAX_SIZE', 104857600); // 100MB
define('ALLOWED_EXTENSIONS', ['apk', 'zip', 'pdf', 'exe', 'dmg', 'tar', 'gz', '7z', 'rar']);

// Debug Mode - controlled via environment variable
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set('Europe/Berlin');
