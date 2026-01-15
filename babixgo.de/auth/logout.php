<?php
/**
 * Logout Handler
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Logout user
User::logout();

// Delete remember me cookie
if (isset($_COOKIE['remember_me'])) {
    setcookie('remember_me', '', time() - 3600, '/', '.babixgo.de', false, true);
}

// Redirect to login page
header('Location: /auth/login?message=' . urlencode('You have been logged out successfully') . '&type=success');
exit;
