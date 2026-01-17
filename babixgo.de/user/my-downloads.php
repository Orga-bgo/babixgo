<?php
/**
 * My Downloads Page
 * URL: babixgo.de/user/my-downloads
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$pageTitle = 'Meine Downloads - BabixGO';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require SHARED_PATH . 'partials/head-meta.php'; ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php require SHARED_PATH . 'partials/head-links.php'; ?>
</head>
<body>
    <?php require SHARED_PATH . 'partials/header.php'; ?>
    
    <main class="user-content">
        <div class="container">
            <h1>Meine Downloads</h1>
            <p>Hier findest du deine Download-Historie.</p>
            
            <div class="empty-state">
                <p>Die Download-Historie wird bald verfügbar sein.</p>
                <a href="/files/" class="btn btn-primary">Zu den Downloads</a>
            </div>
            
            <div class="back-link">
                <a href="/user/" class="btn btn-secondary">Zurück zum Profil</a>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <?php require SHARED_PATH . 'partials/footer-scripts.php'; ?>
</body>
</html>
