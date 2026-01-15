<?php
/**
 * Authentication and session management
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Initialize secure session
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        
        session_name(SESSION_NAME);
        session_start();
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn(): bool {
    initSession();
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Check if current user is admin
 * @return bool
 */
function isAdmin(): bool {
    initSession();
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId(): ?int {
    initSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) {
        return null;
    }
    
    return fetchOne(
        "SELECT id, username, email, description, role, comment_count, created_at FROM users WHERE id = ?",
        'i',
        [getCurrentUserId()]
    );
}

/**
 * Attempt to login user
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function loginUser(string $username, string $password): array {
    initSession();
    
    // Check rate limiting
    if (isLoginLocked()) {
        return [
            'success' => false,
            'message' => 'Zu viele Anmeldeversuche. Bitte warte 15 Minuten.',
            'user' => null
        ];
    }
    
    $user = fetchOne(
        "SELECT id, username, email, password, role, email_verified FROM users WHERE username = ? OR email = ?",
        'ss',
        [$username, $username]
    );
    
    if (!$user) {
        recordFailedLogin();
        return [
            'success' => false,
            'message' => 'Ungültige Anmeldedaten.',
            'user' => null
        ];
    }
    
    if (!password_verify($password, $user['password'])) {
        recordFailedLogin();
        return [
            'success' => false,
            'message' => 'Ungültige Anmeldedaten.',
            'user' => null
        ];
    }
    
    if (!$user['email_verified']) {
        return [
            'success' => false,
            'message' => 'Bitte bestätige zuerst deine E-Mail-Adresse.',
            'user' => null
        ];
    }
    
    // Successful login
    clearFailedLogins();
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['username'] = $user['username'];
    
    return [
        'success' => true,
        'message' => 'Erfolgreich angemeldet.',
        'user' => $user
    ];
}

/**
 * Logout current user
 */
function logoutUser(): void {
    initSession();
    
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Register a new user
 * @param string $username
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
 */
function registerUser(string $username, string $email, string $password): array {
    // Validate input
    if (strlen($username) < 3 || strlen($username) > 50) {
        return ['success' => false, 'message' => 'Username muss zwischen 3 und 50 Zeichen sein.', 'user_id' => null];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Ungültige E-Mail-Adresse.', 'user_id' => null];
    }
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return ['success' => false, 'message' => 'Passwort muss mindestens ' . PASSWORD_MIN_LENGTH . ' Zeichen sein.', 'user_id' => null];
    }
    
    // Check if username or email already exists
    $existing = fetchOne(
        "SELECT id FROM users WHERE username = ? OR email = ?",
        'ss',
        [$username, $email]
    );
    
    if ($existing) {
        return ['success' => false, 'message' => 'Username oder E-Mail bereits vergeben.', 'user_id' => null];
    }
    
    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Insert user
    $userId = insertRow(
        "INSERT INTO users (username, email, password, verification_token, email_verified) VALUES (?, ?, ?, ?, 0)",
        'ssss',
        [$username, $email, $passwordHash, $verificationToken]
    );
    
    if (!$userId) {
        return ['success' => false, 'message' => 'Registrierung fehlgeschlagen. Bitte versuche es erneut.', 'user_id' => null];
    }
    
    // Send verification email
    require_once __DIR__ . '/email.php';
    $emailSent = sendVerificationEmail($email, $username, $verificationToken);
    
    if (!$emailSent) {
        return [
            'success' => true,
            'message' => 'Registrierung erfolgreich, aber Verifizierungs-E-Mail konnte nicht gesendet werden. Bitte kontaktiere den Support.',
            'user_id' => $userId
        ];
    }
    
    return [
        'success' => true,
        'message' => 'Registrierung erfolgreich! Bitte überprüfe deine E-Mails zur Bestätigung.',
        'user_id' => $userId
    ];
}

/**
 * Verify user email with token
 * @param string $token
 * @return bool
 */
function verifyEmail(string $token): bool {
    $user = fetchOne(
        "SELECT id FROM users WHERE verification_token = ? AND email_verified = 0",
        's',
        [$token]
    );
    
    if (!$user) {
        return false;
    }
    
    executeQuery(
        "UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?",
        'i',
        [$user['id']]
    );
    
    return getAffectedRows() > 0;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken(): string {
    initSession();
    
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validateCsrfToken(string $token): bool {
    initSession();
    
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Record a failed login attempt
 */
function recordFailedLogin(): void {
    initSession();
    
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['first_failed_login'] = time();
    }
    
    $_SESSION['login_attempts']++;
}

/**
 * Check if login is locked due to too many attempts
 * @return bool
 */
function isLoginLocked(): bool {
    initSession();
    
    if (!isset($_SESSION['login_attempts'])) {
        return false;
    }
    
    if ($_SESSION['login_attempts'] >= LOGIN_ATTEMPTS_LIMIT) {
        if (time() - $_SESSION['first_failed_login'] < LOGIN_LOCKOUT_TIME) {
            return true;
        }
        // Reset after lockout period
        clearFailedLogins();
    }
    
    return false;
}

/**
 * Clear failed login attempts
 */
function clearFailedLogins(): void {
    initSession();
    unset($_SESSION['login_attempts']);
    unset($_SESSION['first_failed_login']);
}

/**
 * Require user to be logged in, redirect otherwise
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Require user to be admin, redirect otherwise
 */
function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: /');
        exit;
    }
}
