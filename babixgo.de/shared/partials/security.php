<?php
/**
 * Security Utilities
 * babixGO - partials/security.php
 */

/**
 * Generate or retrieve CSRF token
 * 
 * @return string The CSRF token
 */
function getCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function validateCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Alias for validateCsrfToken (backwards compatibility)
 */
function verifyCsrfToken($token) {
    return validateCsrfToken($token);
}

/**
 * Validate and sanitize a redirect URL to prevent open redirect vulnerabilities
 * 
 * @param string $redirect The redirect URL to validate
 * @param string $default The default redirect URL if validation fails (default: '/user/')
 * @return string The validated redirect URL or the default
 */
function validateRedirectUrl($redirect, $default = '/user/') {
    // Only allow internal paths: must start with / (but not //), no traversal, safe chars, optional query params
    if (!str_starts_with($redirect, '/') || 
        str_starts_with($redirect, '//') || 
        str_contains($redirect, '://') ||
        str_contains($redirect, '..') ||
        !preg_match('#^/[a-zA-Z0-9/_\.\-]+(?:\?[a-zA-Z0-9_=&\-]+)?$#', $redirect)) {
        return $default;
    }
    
    return $redirect;
}
?>
