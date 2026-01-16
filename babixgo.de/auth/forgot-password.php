<?php
/**
 * Forgot Password Page
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';
require_once __DIR__ . '/includes/mail-helper.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $email = trim($_POST['email'] ?? '');
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $user = new User();
        $result = $user->requestPasswordReset($email);
        
        if ($result['success']) {
            // Get user data for email
            $db = Database::getInstance();
            $sql = "SELECT username, email FROM users WHERE id = ?";
            $userData = $db->fetchOne($sql, [$result['user_id']]);
            
            // Send reset email
            sendPasswordResetEmail($userData['email'], $userData['username'], $result['reset_token']);
            
            $message = 'Password reset instructions have been sent to your email.';
            $messageType = 'success';
        } else {
            // Don't reveal if email exists or not for security
            $message = 'If that email is registered, you will receive password reset instructions.';
            $messageType = 'info';
        }
    } else {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once SHARED_PATH . 'partials/head-meta.php'; ?>
    <title>Forgot Password - babixgo.de</title>
    <meta name="description" content="Reset your babixgo.de account password">
    <?php require_once SHARED_PATH . 'partials/head-links.php'; ?>
</head>
<body>
    <?php require_once SHARED_PATH . 'partials/header.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Reset Password</h1>
            <p class="subtitle">Enter your email to receive reset instructions</p>
            
            <?php if (isset($message)): ?>
                <div class="message message-<?= htmlspecialchars($messageType, ENT_QUOTES) ?>">
                    <?= htmlspecialchars($message, ENT_QUOTES) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        autocomplete="email"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </form>
            
            <div class="auth-footer">
                <a href="/auth/login">Back to login</a>
                <span>|</span>
                <a href="/auth/register">Create account</a>
            </div>
        </div>
    </div>
    
    <?php require_once SHARED_PATH . 'partials/footer.php'; ?>
    
    <script src="/shared/assets/js/main.js"></script>
</body>
</html>
