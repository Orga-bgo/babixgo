<?php
/**
 * Registration Page
 */

define('BASE_PATH', dirname(__DIR__, 3) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Redirect if already logged in
if (User::isLoggedIn()) {
    header('Location: /user/');
    exit;
}

// Page configuration
$pageTitle = 'Register - babixgo.de';
$currentPage = 'register';
$includeValidationJS = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/main.css">
</head>
<body>
    <?php require_once SHARED_PATH . 'partials/header.php'; ?>
    <div class="auth-container">
        <div class="auth-box">
            <h1>Create Account</h1>
            <p class="subtitle">Join the babixgo.de community</p>
            
            <div id="message-container"></div>
            
            <form id="register-form" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        pattern="[a-zA-Z0-9_]{3,50}"
                        title="3-50 characters, letters, numbers, and underscores only"
                    >
                    <span class="error-message" id="username-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                    >
                    <span class="error-message" id="email-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="8"
                    >
                    <span class="hint">At least 8 characters with 1 uppercase, 1 lowercase, and 1 number</span>
                    <span class="error-message" id="password-error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password_confirm">Confirm Password</label>
                    <input 
                        type="password" 
                        id="password_confirm" 
                        name="password_confirm" 
                        required
                    >
                    <span class="error-message" id="password-confirm-error"></span>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Account</button>
            </form>
            
            <div class="auth-footer">
                Already have an account? <a href="/auth/login">Login here</a>
            </div>
        </div>
    </div>
    
    <?php require_once SHARED_PATH . 'partials/footer.php'; ?>
    <script src="/shared/assets/js/main.js"></script>
    <script src="/shared/assets/js/form-validation.js"></script>
    <script>
        document.getElementById('register-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const messageContainer = document.getElementById('message-container');
            
            // Clear previous messages
            messageContainer.innerHTML = '';
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            try {
                const response = await fetch('/auth/includes/form-handlers/register-handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    form.reset();
                    setTimeout(() => {
                        window.location.href = '/auth/login';
                    }, 3000);
                } else {
                    if (result.errors) {
                        result.errors.forEach(error => {
                            showMessage(error, 'error');
                        });
                    } else {
                        showMessage(result.error, 'error');
                    }
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        });
        
        function showMessage(message, type) {
            const messageContainer = document.getElementById('message-container');
            const div = document.createElement('div');
            div.className = `message message-${type}`;
            div.textContent = message;
            messageContainer.appendChild(div);
            
            if (type === 'success') {
                setTimeout(() => div.remove(), 5000);
            }
        }
    </script>
</body>
</html>
