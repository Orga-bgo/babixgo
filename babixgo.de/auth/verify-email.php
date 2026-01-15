<?php
/**
 * Email Verification Handler
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';
require_once __DIR__ . '/includes/mail-helper.php';

// Get token from query string
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /auth/login?message=' . urlencode('Invalid verification link') . '&type=error');
    exit;
}

// Verify email
$user = new User();
$db = Database::getInstance();

// Get user by token
$sql = "SELECT id, username, email FROM users WHERE verification_token = ? AND is_verified = 0";
$userData = $db->fetchOne($sql, [$token]);

if (!$userData) {
    header('Location: /auth/login?message=' . urlencode('Invalid or expired verification link') . '&type=error');
    exit;
}

// Verify the email
if ($user->verifyEmail($token)) {
    // Send welcome email
    sendWelcomeEmail($userData['email'], $userData['username']);
    
    header('Location: /auth/login?message=' . urlencode('Email verified successfully! You can now login.') . '&type=success');
    exit;
} else {
    header('Location: /auth/login?message=' . urlencode('Verification failed. Please try again.') . '&type=error');
    exit;
}
