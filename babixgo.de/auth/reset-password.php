<?php
/**
 * Reset Password Page
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Get token from query string
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /auth/login?message=' . urlencode('Invalid reset link') . '&type=error');
    exit;
}

// Verify token is valid
$db = Database::getInstance();
$sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()";
$validToken = $db->fetchOne($sql, [$token]);

if (!$validToken) {
    header('Location: /auth/login?message=' . urlencode('Reset link is invalid or expired') . '&type=error');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    if (strlen($newPassword) < 8) {
        $errors[] = 'Password must be at least 8 characters';
    } elseif (!preg_match('/[A-Z]/', $newPassword)) {
        $errors[] = 'Password must contain at least one uppercase letter';
    } elseif (!preg_match('/[a-z]/', $newPassword)) {
        $errors[] = 'Password must contain at least one lowercase letter';
    } elseif (!preg_match('/[0-9]/', $newPassword)) {
        $errors[] = 'Password must contain at least one number';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        $user = new User();
        $result = $user->resetPassword($token, $newPassword);
        
        if ($result['success']) {
            header('Location: /auth/login?message=' . urlencode('Password reset successfully! You can now login.') . '&type=success');
            exit;
        } else {
            $message = $result['error'];
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - babixgo.de</title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/main.css">
</head>
<body>
    <?php require_once SHARED_PATH . 'partials/header.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Set New Password</h1>
            <p class="subtitle">Enter your new password below</p>
            
            <?php if (isset($message)): ?>
                <div class="message message-<?= htmlspecialchars($messageType, ENT_QUOTES) ?>">
                    <?= htmlspecialchars($message, ENT_QUOTES) ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="message message-error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                        minlength="8"
                    >
                    <span class="hint">At least 8 characters with 1 uppercase, 1 lowercase, and 1 number</span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </form>
            
            <div class="auth-footer">
                <a href="/auth/login">Back to login</a>
            </div>
        </div>
    </div>
    
    <?php require_once SHARED_PATH . 'partials/footer.php'; ?>
    
    <script src="/shared/assets/js/main.js"></script>
</body>
</html>
