<?php
/**
 * CSRF Protection Utilities
 * babixGO - partials/csrf.php
 */

/**
 * Generate a CSRF token and store it in the session
 * 
 * @return string The generated CSRF token
 */
function csrf_generate_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Generate a new token
    $token = bin2hex(random_bytes(32));
    
    // Store in session
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * Get the current CSRF token, generating one if it doesn't exist
 * 
 * @return string The CSRF token
 */
function csrf_get_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists and is not expired (1 hour)
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) ||
        (time() - $_SESSION['csrf_token_time']) > 3600) {
        return csrf_generate_token();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 * 
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function csrf_validate_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if token exists in session
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    // Check if token has expired (1 hour)
    if (isset($_SESSION['csrf_token_time']) && 
        (time() - $_SESSION['csrf_token_time']) > 3600) {
        return false;
    }
    
    // Compare tokens using timing-safe comparison
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden CSRF token field for forms
 */
function csrf_field() {
    $token = csrf_get_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
?>
