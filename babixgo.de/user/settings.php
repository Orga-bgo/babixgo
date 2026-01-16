<?php
/**
 * User Settings Page
 * URL: babixgo.de/user/settings
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$pageTitle = 'Einstellungen - BabixGO';
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
            <h1>Einstellungen</h1>
            
            <div class="settings-grid">
                <div class="settings-card">
                    <h2>Konto</h2>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>E-Mail-Adresse</strong>
                            <p><?= htmlspecialchars($userData['email']) ?></p>
                        </div>
                    </div>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>Mitglied seit</strong>
                            <p><?= date('d.m.Y', strtotime($userData['created_at'])) ?></p>
                        </div>
                    </div>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>Status</strong>
                            <p><?= $userData['is_verified'] ? 'Verifiziert' : 'Nicht verifiziert' ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h2>Sicherheit</h2>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>Passwort</strong>
                            <p>Ändere dein Passwort regelmäßig für mehr Sicherheit.</p>
                        </div>
                        <a href="/user/edit-profile" class="btn btn-secondary">Passwort ändern</a>
                    </div>
                </div>
                
                <div class="settings-card">
                    <h2>Aktionen</h2>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>Profil bearbeiten</strong>
                            <p>Ändere deinen Benutzernamen und deine Beschreibung.</p>
                        </div>
                        <a href="/user/edit-profile" class="btn btn-secondary">Bearbeiten</a>
                    </div>
                    <div class="settings-item">
                        <div class="settings-info">
                            <strong>Abmelden</strong>
                            <p>Melde dich von deinem Konto ab.</p>
                        </div>
                        <a href="/auth/logout" class="btn btn-secondary">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <?php require SHARED_PATH . 'partials/footer-scripts.php'; ?>
</body>
</html>
