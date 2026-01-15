<?php
/**
 * Security Utilities
 * babixGO - partials/security.php
 */

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
