<?php
/**
 * Login Handler
 * Processes user login form submissions
 */

define('BASE_PATH', dirname(__DIR__, 4) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

// Verify CSRF token
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// Get and sanitize input
$identifier = trim($_POST['identifier'] ?? '');
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';
$redirect = $_POST['redirect'] ?? '/user/';

// Validate redirect parameter (prevent open redirect vulnerability)
// Only allow internal paths (starting with / but not //)
if (!str_starts_with($redirect, '/') || str_starts_with($redirect, '//') || str_contains($redirect, '://')) {
    $redirect = '/user/';
}

// Validation
if (empty($identifier)) {
    echo json_encode(['success' => false, 'error' => 'Username or email is required']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Password is required']);
    exit;
}

// Attempt login
try {
    $user = new User();
    $result = $user->login($identifier, $password);
    
    if ($result['success']) {
        // Set remember me cookie if requested
        if ($rememberMe) {
            $cookieValue = base64_encode(json_encode([
                'user_id' => $result['user']['id'],
                'token' => bin2hex(random_bytes(32))
            ]));
            
            setcookie(
                'remember_me',
                $cookieValue,
                time() + (30 * 24 * 60 * 60), // 30 days
                '/',
                '.babixgo.de',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                true
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful!',
            'redirect' => $redirect
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $result['error']]);
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Login failed. Please try again.']);
}
