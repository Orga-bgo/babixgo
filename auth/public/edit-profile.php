<?php
/**
 * Edit Profile Page
 */

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - babixgo.de</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/" class="logo">babixgo.de</a>
            <ul class="nav-menu">
                <li><a href="/">Profile</a></li>
                <?php if (User::isAdmin()): ?>
                    <li><a href="/admin/">Admin Panel</a></li>
                <?php endif; ?>
                <li><a href="/logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>Edit Profile</h1>
        
        <div id="message-container"></div>
        
        <div class="profile-card">
            <h2>Update Profile Information</h2>
            
            <form id="profile-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        value="<?= htmlspecialchars($userData['username'], ENT_QUOTES) ?>"
                        required 
                        pattern="[a-zA-Z0-9_]{3,50}"
                    >
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="5"
                        placeholder="Tell us about yourself..."
                    ><?= htmlspecialchars($userData['description'] ?? '', ENT_QUOTES) ?></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
        
        <div class="profile-card">
            <h2>Change Password</h2>
            
            <form id="password-form" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                    >
                </div>
                
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
                    <label for="confirm_password">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
    
    <script src="/assets/js/form-validation.js"></script>
    <script>
        async function handleFormSubmit(form, endpoint) {
            const formData = new FormData(form);
            const messageContainer = document.getElementById('message-container');
            
            messageContainer.innerHTML = '';
            
            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    if (form.id === 'password-form') {
                        form.reset();
                    }
                    if (form.id === 'profile-form') {
                        setTimeout(() => window.location.href = '/', 2000);
                    }
                } else {
                    if (result.errors) {
                        result.errors.forEach(error => showMessage(error, 'error'));
                    } else {
                        showMessage(result.error, 'error');
                    }
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
        
        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(e.target, '/includes/form-handlers/profile-handler.php');
        });
        
        document.getElementById('password-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(e.target, '/includes/form-handlers/profile-handler.php');
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
