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

/**
 * Load environment variables from .env file if it exists
 */
function loadEnvFile() {
    // Check both possible locations
    $envPaths = [
        dirname(__DIR__) . '/.env',  // Parent directory (production: web root)
        __DIR__ . '/.env'            // includes/.env (alternative location)
    ];
    
    $envFile = null;
    foreach ($envPaths as $path) {
        if (file_exists($path) && is_readable($path)) {
            $envFile = $path;
            break;
        }
    }
    
    if ($envFile === null) {
        return;
    }
    
    // Prevent memory exhaustion by checking file size (max 1MB for .env file)
    $maxFileSize = 1048576; // 1MB
    if (filesize($envFile) > $maxFileSize) {
        return;
    }
    
    $lines = file($envFile, FILE_IGNORE_NEW_LINES);
    
    // Handle file read failure
    if ($lines === false) {
        return;
    }
    
    // Allowlist of expected environment variable keys for security
    // Only includes variables that are set by the deployment workflow
    $allowedKeys = [
        'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD',
        'SMTP_HOST', 'SMTP_PORT', 'SMTP_USER', 'SMTP_KEY'
    ];
    
    foreach ($lines as $line) {
        $trimmedLine = trim($line);
        
        // Skip empty lines
        if ($trimmedLine === '') {
            continue;
        }
        
        // Skip comments
        if (strpos($trimmedLine, '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE format
        if (strpos($trimmedLine, '=') !== false) {
            $parts = explode('=', $trimmedLine, 2);
            
            // Safety check (should always be 2 parts with explode limit)
            if (count($parts) !== 2) {
                continue;
            }
            
            [$key, $value] = $parts;
            
            $key = trim($key);
            $value = trim($value);
            
            // Skip invalid or disallowed keys (security measure)
            if (empty($key) || !in_array($key, $allowedKeys, true)) {
                continue;
            }
            
            // Remove surrounding quotes if present (must be matching pairs and value length >= 2)
            if (strlen($value) >= 2) {
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
            }
            
            // Set as environment variable if not already set
            // Use both methods for maximum compatibility
            if (getenv($key) === false) {
                putenv("$key=$value");
                $_ENV[$key] = $value;
            }
        }
    }
}

// Load .env file before using environment variables
loadEnvFile();

// Database Configuration - Use environment variables
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'babixgo_files');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: '');
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
