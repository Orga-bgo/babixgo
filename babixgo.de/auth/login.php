<?php
/**
 * Login Page
 */

define('BASE_PATH', dirname(__DIR__, 3) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('SHARED_ASSETS_PATH', '../../shared/assets/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';
require_once SHARED_PATH . 'partials/security.php';

// Get redirect parameter and validate it (prevent open redirect vulnerability)
$redirect = validateRedirectUrl($_GET['redirect'] ?? '', '/user/');

// Redirect if already logged in
if (User::isLoggedIn()) {
    header('Location: ' . $redirect);
    exit;
}

// Get message from query string
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'info';

// Page configuration for header partial
$pageTitle = 'Login - babixgo.de';
$currentPage = 'login';

// Include header
require_once SHARED_PATH . 'partials/header.php';
require_once SHARED_PATH . 'partials/nav.php';
?>
<div class="auth-container">
    <div class="auth-box">
        <h1>Welcome Back</h1>
        <p class="subtitle">Login to your babixgo.de account</p>
        
        <div id="message-container">
            <?php if ($message): ?>
                <div class="message message-<?= htmlspecialchars($messageType, ENT_QUOTES) ?>">
                    <?= htmlspecialchars($message, ENT_QUOTES) ?>
                </div>
            <?php endif; ?>
        </div>
        
        <form id="login-form" method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES) ?>">
            
            <div class="form-group">
                <label for="identifier">Username or Email</label>
                <input 
                    type="text" 
                    id="identifier" 
                    name="identifier" 
                    required
                    autocomplete="username"
                >
                <span class="error-message" id="identifier-error"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="current-password"
                >
                <span class="error-message" id="password-error"></span>
            </div>
            
            <div class="form-group checkbox-group">
                <label>
                    <input type="checkbox" name="remember_me" value="1">
                    Remember me for 30 days
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        
        <div class="auth-footer">
            <a href="/auth/forgot-password">Forgot password?</a>
            <span>|</span>
            <a href="/auth/register">Create account</a>
        </div>
    </div>
</div>

<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const messageContainer = document.getElementById('message-container');
        
        // Clear previous messages
        messageContainer.innerHTML = '';
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
        
        try {
            const response = await fetch('/auth/includes/form-handlers/login-handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                setTimeout(() => {
                    window.location.href = result.redirect || '/user/';
                }, 1000);
            } else {
                showMessage(result.error, 'error');
            }
        } catch (error) {
            showMessage('An error occurred. Please try again.', 'error');
        }
    });
    
    function showMessage(message, type) {
        const messageContainer = document.getElementById('message-container');
        messageContainer.innerHTML = `<div class="message message-${type}">${message}</div>`;
    }
</script>

<?php
// Include footer with validation JS
$includeValidationJS = true;
require_once SHARED_PATH . 'partials/footer.php';
?>
