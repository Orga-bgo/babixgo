<?php
/**
 * Edit Profile Page
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$pageTitle = 'Profil bearbeiten - BabixGO';
$currentPage = 'edit-profile';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require SHARED_PATH . 'partials/head-meta.php'; ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php require SHARED_PATH . 'partials/head-links.php'; ?>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
    <?php require SHARED_PATH . 'partials/header.php'; ?>
    
    <main class="user-content">
        <div class="container">
            <h1>Profil bearbeiten</h1>
            
            <div id="message-container"></div>
            
            <div class="profile-card">
                <h2>Profilinformationen</h2>
                
                <form id="profile-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-group">
                        <label for="username">Benutzername</label>
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
                        <label for="description">Beschreibung</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="5"
                            placeholder="Erzähl uns etwas über dich..."
                        ><?= htmlspecialchars($userData['description'] ?? '', ENT_QUOTES) ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Änderungen speichern</button>
                    <a href="/user/" class="btn btn-secondary">Abbrechen</a>
                </form>
            </div>
            
            <div class="profile-card">
                <h2>Passwort ändern</h2>
                
                <form id="password-form" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div class="form-group">
                        <label for="current_password">Aktuelles Passwort</label>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Neues Passwort</label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            required
                            minlength="8"
                        >
                        <span class="hint">Mindestens 8 Zeichen mit 1 Großbuchstaben, 1 Kleinbuchstaben und 1 Zahl</span>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Neues Passwort bestätigen</label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Passwort ändern</button>
                </form>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <script src="/shared/assets/js/main.js"></script>
    <script src="/shared/assets/js/form-validation.js"></script>
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
                        setTimeout(() => window.location.href = '/user/', 2000);
                    }
                } else {
                    if (result.errors) {
                        result.errors.forEach(error => showMessage(error, 'error'));
                    } else {
                        showMessage(result.error, 'error');
                    }
                }
            } catch (error) {
                showMessage('Ein Fehler ist aufgetreten. Bitte versuche es erneut.', 'error');
            }
        }
        
        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(e.target, '/auth/includes/form-handlers/profile-handler.php');
        });
        
        document.getElementById('password-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(e.target, '/auth/includes/form-handlers/profile-handler.php');
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
