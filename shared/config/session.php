<?php
/**
 * Session Configuration
 * Shared across all babixgo.de domains
 */

// Session configuration
ini_set('session.cookie_domain', '.babixgo.de');
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

// Use secure cookies in production (HTTPS)
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// Session timeout: 30 minutes of inactivity
define('SESSION_TIMEOUT', 1800);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name('BABIXGO_SESSION');
    session_start();
    
    // Check for session timeout
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            session_start();
        }
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Regenerate session ID periodically to prevent session fixation
    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// CSRF Token generation
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token for forms
 */
function getCsrfToken() {
    return $_SESSION['csrf_token'] ?? '';
}
