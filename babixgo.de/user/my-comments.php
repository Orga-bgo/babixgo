<?php
/**
 * My Comments Page
 * URL: babixgo.de/user/my-comments
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$user = new User();
$commentCount = $user->getUserCommentCount($_SESSION['user_id']);

$pageTitle = 'Meine Kommentare - BabixGO';
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
            <h1>Meine Kommentare</h1>
            <p>Du hast bisher <strong><?= $commentCount ?></strong> Kommentare geschrieben.</p>
            
            <?php if ($commentCount === 0): ?>
                <div class="empty-state">
                    <p>Du hast noch keine Kommentare geschrieben.</p>
                    <a href="/files/" class="btn btn-primary">Downloads durchsuchen</a>
                </div>
            <?php else: ?>
                <p>Kommentar-Übersicht wird bald verfügbar sein.</p>
            <?php endif; ?>
            
            <div class="back-link">
                <a href="/user/" class="btn btn-secondary">Zurück zum Profil</a>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <?php require SHARED_PATH . 'partials/footer-scripts.php'; ?>
</body>
</html>
