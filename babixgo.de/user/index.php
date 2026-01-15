<?php
/**
 * User Dashboard / Profile Overview
 * URL: babixgo.de/user/
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

$pageTitle = 'Mein Profil - BabixGO';
$pageDescription = 'Verwalte dein BabixGO Profil, Downloads und Kommentare';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require SHARED_PATH . 'partials/head-meta.php'; ?>
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php require SHARED_PATH . 'partials/head-links.php'; ?>
    <link rel="stylesheet" href="/shared/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
    <?php require SHARED_PATH . 'partials/header.php'; ?>
    
    <main class="user-dashboard">
        <div class="container">
            <h1>Willkommen, <?= htmlspecialchars($currentUsername) ?>!</h1>
            
            <div class="dashboard-grid">
                <div class="dashboard-card">
                    <h2>Profil</h2>
                    <p><strong>Benutzername:</strong> <?= htmlspecialchars($currentUsername) ?></p>
                    <p><strong>E-Mail:</strong> <?= htmlspecialchars($currentUserEmail) ?></p>
                    <p><strong>Rolle:</strong> <?= $currentUserRole === 'admin' ? 'Administrator' : 'Benutzer' ?></p>
                    <a href="/user/edit-profile" class="btn btn-primary">Profil bearbeiten</a>
                </div>
                
                <div class="dashboard-card">
                    <h2>Schnellzugriff</h2>
                    <ul class="quick-links">
                        <li><a href="/user/my-comments">Meine Kommentare</a></li>
                        <li><a href="/user/my-downloads">Meine Downloads</a></li>
                        <li><a href="/user/settings">Einstellungen</a></li>
                        <li><a href="/files/">Downloads durchsuchen</a></li>
                        <?php if ($currentUserRole === 'admin'): ?>
                            <li><a href="/admin/">Admin-Bereich</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <?php require SHARED_PATH . 'partials/footer-scripts.php'; ?>
</body>
</html>
