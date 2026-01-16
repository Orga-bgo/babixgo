---
applyTo: "**/auth/**/*.php"
---

# Authentication System Instructions

When working with authentication files in the `/auth/` directory, follow these security-critical guidelines:

## Authentication Pages

The authentication system includes:
- `/auth/login.php` - User login
- `/auth/register.php` - New user registration
- `/auth/logout.php` - Session termination
- `/auth/verify-email.php` - Email verification
- `/auth/forgot-password.php` - Password reset request
- `/auth/reset-password.php` - Password reset with token

## Security Requirements

### 1. Password Hashing

**ALWAYS use bcrypt for password hashing:**
```php
// Hashing password (registration)
$hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);

// Verifying password (login)
if (password_verify($_POST['password'], $user['password'])) {
    // Password is correct
} else {
    // Password is incorrect
}
```

**NEVER:**
- Store passwords in plain text
- Use MD5 or SHA1 for passwords
- Use reversible encryption

### 2. Session Management

**Starting a session:**
```php
// Session config is in shared/config/session.php
require_once SHARED_PATH . 'config/session.php';

// After successful login
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role']; // 'user' or 'admin'

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);
```

**Logout:**
```php
// Destroy all session data
session_start();
session_unset();
session_destroy();

// Redirect to homepage
header('Location: /');
exit;
```

### 3. CSRF Protection

**ALL authentication forms MUST have CSRF tokens:**
```php
// Generate token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include in form
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Validate in handler
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid request');
}
```

## Login Implementation

### Login Form Validation
```php
$errors = [];

// Validate email
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $errors[] = 'Please enter a valid email address';
}

// Check password is provided
if (empty($_POST['password'])) {
    $errors[] = 'Password is required';
}

if (!empty($errors)) {
    // Display errors and stop
    foreach ($errors as $error) {
        echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '<br>';
    }
    exit;
}
```

### Login Handler
```php
// Fetch user by email
$stmt = $db->prepare("SELECT id, username, email, password, role, verified FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // User not found - use generic message to prevent user enumeration
    $error = 'Invalid email or password';
    exit;
}

// Verify password
if (!password_verify($_POST['password'], $user['password'])) {
    // Incorrect password - use same generic message
    $error = 'Invalid email or password';
    exit;
}

// Check if email is verified
if (!$user['verified']) {
    $error = 'Please verify your email address before logging in';
    exit;
}

// Login successful - set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

// Regenerate session ID
session_regenerate_id(true);

// Handle "remember me"
if (isset($_POST['remember_me'])) {
    // Set long-lived cookie (30 days)
    $token = bin2hex(random_bytes(32));
    
    // Store token in database
    $stmt = $db->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
    $stmt->execute([$token, $user['id']]);
    
    // Set cookie - use environment-specific domain from config
    // In production: 'babixgo.de', in dev: 'localhost', in docker: container name
    $cookieDomain = $_ENV['COOKIE_DOMAIN'] ?? ini_get('session.cookie_domain') ?: '';
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', $cookieDomain, true, true);
}

// Redirect to return URL or user dashboard
$redirect = $_GET['redirect'] ?? '/user/';
header('Location: ' . $redirect);
exit;
```

## Registration Implementation

### Registration Validation
```php
$errors = [];

// Username validation
$username = trim($_POST['username']);
if (strlen($username) < 3 || strlen($username) > 20) {
    $errors[] = 'Username must be 3-20 characters';
}
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    $errors[] = 'Username can only contain letters, numbers, underscores, and hyphens';
}

// Email validation
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$email) {
    $errors[] = 'Please enter a valid email address';
}

// Password validation
$password = $_POST['password'];
if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}
if ($password !== $_POST['password_confirm']) {
    $errors[] = 'Passwords do not match';
}

// Check for existing username
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    $errors[] = 'Username already taken';
}

// Check for existing email
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $errors[] = 'Email already registered';
}

if (!empty($errors)) {
    // Display errors
    exit;
}
```

### Registration Handler
```php
// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Generate verification token
$verificationToken = bin2hex(random_bytes(32));

// Insert user
$stmt = $db->prepare("
    INSERT INTO users (username, email, password, verification_token, verified, role, created_at)
    VALUES (?, ?, ?, ?, 0, 'user', NOW())
");
$stmt->execute([$username, $email, $hashedPassword, $verificationToken]);
$userId = $db->lastInsertId();

// Send verification email
require_once SHARED_PATH . 'classes/Email.php';
$emailSender = new Email($db);
$emailSender->sendVerificationEmail($email, $username, $verificationToken);

// Success message
$_SESSION['success_message'] = 'Registration successful! Please check your email to verify your account.';
header('Location: /auth/login');
exit;
```

## Email Verification

### Verification Handler
```php
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Invalid verification link');
}

// Find user with this token
$stmt = $db->prepare("SELECT id, verified FROM users WHERE verification_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Invalid or expired verification link');
}

if ($user['verified']) {
    // Already verified
    $_SESSION['success_message'] = 'Your email is already verified. You can log in.';
    header('Location: /auth/login');
    exit;
}

// Mark as verified
$stmt = $db->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE id = ?");
$stmt->execute([$user['id']]);

// Success
$_SESSION['success_message'] = 'Email verified successfully! You can now log in.';
header('Location: /auth/login');
exit;
```

## Password Reset

### Forgot Password Handler
```php
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    $error = 'Please enter a valid email address';
    exit;
}

// Find user
$stmt = $db->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // Generate reset token
    $resetToken = bin2hex(random_bytes(32));
    $tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store token
    $stmt = $db->prepare("
        UPDATE users 
        SET reset_token = ?, reset_token_expiry = ? 
        WHERE id = ?
    ");
    $stmt->execute([$resetToken, $tokenExpiry, $user['id']]);
    
    // Send reset email
    require_once SHARED_PATH . 'classes/Email.php';
    $emailSender = new Email($db);
    $emailSender->sendPasswordResetEmail($email, $user['username'], $resetToken);
}

// ALWAYS show success message (prevent user enumeration)
$_SESSION['success_message'] = 'If that email exists, a password reset link has been sent.';
header('Location: /auth/login');
exit;
```

### Reset Password Handler
```php
$token = $_POST['token'] ?? '';
$newPassword = $_POST['password'] ?? '';
$confirmPassword = $_POST['password_confirm'] ?? '';

// Validate token
if (empty($token)) {
    die('Invalid reset link');
}

// Validate passwords
$errors = [];
if (strlen($newPassword) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}
if ($newPassword !== $confirmPassword) {
    $errors[] = 'Passwords do not match';
}

if (!empty($errors)) {
    // Display errors
    exit;
}

// Find user with valid token
$stmt = $db->prepare("
    SELECT id 
    FROM users 
    WHERE reset_token = ? 
    AND reset_token_expiry > NOW()
");
$stmt->execute([$token]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('Invalid or expired reset link');
}

// Hash new password
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

// Update password and clear token
$stmt = $db->prepare("
    UPDATE users 
    SET password = ?, reset_token = NULL, reset_token_expiry = NULL 
    WHERE id = ?
");
$stmt->execute([$hashedPassword, $user['id']]);

// Success
$_SESSION['success_message'] = 'Password reset successfully! You can now log in.';
header('Location: /auth/login');
exit;
```

## Remember Me Implementation

### Checking Remember Me Token
```php
// At top of pages (before session check)
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Find user with this token
    $stmt = $db->prepare("SELECT id, username, email, role FROM users WHERE remember_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Auto-login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Regenerate session ID
        session_regenerate_id(true);
    } else {
        // Invalid token - remove cookie
        $cookieDomain = $_ENV['COOKIE_DOMAIN'] ?? ini_get('session.cookie_domain') ?: '';
        setcookie('remember_token', '', time() - 3600, '/', $cookieDomain, true, true);
    }
}
```

## Rate Limiting

### Login Rate Limiting
```php
// Track failed login attempts
function trackFailedLogin($db, $email) {
    $stmt = $db->prepare("
        INSERT INTO login_attempts (email, attempt_time, ip_address)
        VALUES (?, NOW(), ?)
    ");
    $stmt->execute([$email, $_SERVER['REMOTE_ADDR']]);
}

// Check if rate limited
function isRateLimited($db, $email) {
    // Allow 5 attempts per 15 minutes
    $stmt = $db->prepare("
        SELECT COUNT(*) 
        FROM login_attempts 
        WHERE email = ? 
        AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
    ");
    $stmt->execute([$email]);
    $attempts = $stmt->fetchColumn();
    
    return $attempts >= 5;
}

// In login handler
if (isRateLimited($db, $email)) {
    die('Too many login attempts. Please try again in 15 minutes.');
}

// After failed login
trackFailedLogin($db, $email);
```

## Redirect After Login

### Handling Return URLs
```php
// In login form
<form method="POST" action="/auth/login">
    <?php
    $redirect = $_GET['redirect'] ?? '/user/';
    ?>
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Form fields -->
</form>

// In login handler (VALIDATE REDIRECT URL)
$redirect = $_POST['redirect'] ?? '/user/';

// Security: Only allow internal redirects
if (!empty($redirect) && $redirect[0] !== '/') {
    $redirect = '/user/';
}

// Security: Prevent open redirect
$allowedPaths = ['/user/', '/files/', '/admin/', '/'];
$redirectBase = explode('?', $redirect)[0];
$isAllowed = false;
foreach ($allowedPaths as $path) {
    if (strpos($redirectBase, $path) === 0) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed) {
    $redirect = '/user/';
}

header('Location: ' . $redirect);
exit;
```

## Testing Checklist for Authentication

Before committing authentication changes:
- [ ] CSRF tokens are present on ALL forms
- [ ] Passwords are hashed with bcrypt (cost 12)
- [ ] Password verification uses password_verify()
- [ ] Session ID is regenerated after login
- [ ] Generic error messages prevent user enumeration
- [ ] Email verification is required for new accounts
- [ ] Password reset tokens expire after 1 hour
- [ ] Remember me tokens are stored securely
- [ ] Rate limiting is implemented for login attempts
- [ ] Redirect URLs are validated to prevent open redirect
- [ ] All user input is validated and sanitized
- [ ] SQL queries use prepared statements
- [ ] Error messages are user-friendly
- [ ] Success/error messages use session flash messages
- [ ] Logout destroys session completely
- [ ] All sensitive operations are logged

## Common Patterns

### Flash Messages
```php
// Setting message in handler
$_SESSION['success_message'] = 'Operation successful!';
$_SESSION['error_message'] = 'Operation failed!';

// Displaying in view
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
```

### Form Error Display
```php
<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
```
