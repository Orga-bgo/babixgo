<?php
/**
 * Database Configuration
 * Shared across all babixgo.de domains
 */

// Database credentials - UPDATE THESE FOR PRODUCTION
define('DB_HOST', 'localhost');
define('DB_NAME', 'babixgo_db');
define('DB_USER', 'babixgo_user');
define('DB_PASS', 'your_secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// Error reporting - set to false in production
define('DB_DEBUG', true);

// PDO Options
define('PDO_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
]);
