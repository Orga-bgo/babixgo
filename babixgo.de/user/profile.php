<?php
/**
 * User Profile Page
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$userData = $user->getUserById($_SESSION['user_id']);

$commentCount = $user->getUserCommentCount($_SESSION['user_id']);

$friendshipUrl = 'https://babixgo.de/friend/' . ($userData['friendship_link'] ?? '');

$pageTitle = 'Mein Profil - BabixGO';
$currentPage = 'profile';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require SHARED_PATH . 'partials/head-meta.php'; ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php require SHARED_PATH . 'partials/head-links.php'; ?>
    <link rel="stylesheet" href="/shared/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
    <?php require SHARED_PATH . 'partials/header.php'; ?>
    
    <main class="user-content">
        <div class="container">
            <div class="profile-header">
                <h1>Willkommen, <?= htmlspecialchars($userData['username']) ?>!</h1>
                <p class="profile-subtitle">Mitglied seit <?= date('d.m.Y', strtotime($userData['created_at'])) ?></p>
            </div>
            
            <div class="profile-grid">
                <div class="profile-card">
                    <h2>Kontoinformationen</h2>
                    <div class="info-row">
                        <label>Benutzername:</label>
                        <span><?= htmlspecialchars($userData['username']) ?></span>
                    </div>
                    <div class="info-row">
                        <label>E-Mail:</label>
                        <span><?= htmlspecialchars($userData['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <label>Rolle:</label>
                        <span class="badge badge-<?= $userData['role'] ?>"><?= $userData['role'] === 'admin' ? 'Administrator' : 'Benutzer' ?></span>
                    </div>
                    <div class="info-row">
                        <label>Status:</label>
                        <span class="badge badge-<?= $userData['is_verified'] ? 'success' : 'warning' ?>">
                            <?= $userData['is_verified'] ? 'Verifiziert' : 'Nicht verifiziert' ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <label>Beschreibung:</label>
                        <p><?= $userData['description'] ? htmlspecialchars($userData['description']) : '<em>Keine Beschreibung</em>' ?></p>
                    </div>
                    <a href="/user/edit-profile" class="btn btn-primary">Profil bearbeiten</a>
                </div>
                
                <?php if (!empty($userData['friendship_link'])): ?>
                <div class="profile-card">
                    <h2>Freundschafts-Link</h2>
                    <p>Teile diesen Link mit deinen Freunden:</p>
                    <div class="friendship-link-box">
                        <input type="text" id="friendship-link" value="<?= htmlspecialchars($friendshipUrl) ?>" readonly>
                        <button onclick="copyFriendshipLink()" class="btn btn-secondary">Kopieren</button>
                    </div>
                    <p class="hint">Dein Code: <strong><?= htmlspecialchars($userData['friendship_link']) ?></strong></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-card">
                <h2>Kommentar-Aktivität</h2>
                <p>Anzahl Kommentare: <strong><?= $commentCount ?></strong></p>
                <a href="/user/my-comments" class="btn btn-secondary">Alle Kommentare anzeigen</a>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <script src="/shared/assets/js/main.js"></script>
    <script>
        function copyFriendshipLink() {
            const input = document.getElementById('friendship-link');
            input.select();
            navigator.clipboard.writeText(input.value);
            
            const btn = event.target;
            const originalText = btn.textContent;
            btn.textContent = 'Kopiert!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }
    </script>
</body>
</html>
