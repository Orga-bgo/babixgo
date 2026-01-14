<?php
/**
 * Initialization file - Auto-detects includes path
 * Works in both development (includes outside public) and production (includes inside public)
 */

// Determine includes directory path
if (file_exists(__DIR__ . '/../includes/config.php')) {
    // Development: includes is outside public/
    define('INCLUDES_PATH', __DIR__ . '/../includes/');
} elseif (file_exists(__DIR__ . '/includes/config.php')) {
    // Production: includes is inside public/
    define('INCLUDES_PATH', __DIR__ . '/includes/');
} else {
    die('Error: Could not locate includes directory');
}

// Load core includes
require_once INCLUDES_PATH . 'config.php';
require_once INCLUDES_PATH . 'db.php';
require_once INCLUDES_PATH . 'auth.php';
require_once INCLUDES_PATH . 'functions.php';
