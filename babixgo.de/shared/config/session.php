<?php

/**
 * Session configuration
 */

// Only set cookie domain in production
$isProduction = strpos($_SERVER['HTTP_HOST'] ?? '', 'babixgo.de') !== false;
if ($isProduction) {
    ini_set('session.cookie_domain', '.babixgo.de');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

return [
    'lifetime' => 3600,
    'path' => '/',
    'domain' => $isProduction ? '.babixgo.de' : '',
    'secure' => $isProduction,
    'httponly' => true,
    'samesite' => 'Lax'
];
