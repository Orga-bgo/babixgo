<?php
/**
 * User Settings Page
 * URL: babixgo.de/user/settings
 */

define('BASE_PATH', dirname(__DIR__, 1) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

// Page configuration
$pageTitle = 'Settings - babixGO';
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
            <h1>Account Settings</h1>
            <p>Manage your account preferences and security settings.</p>
            <!-- TODO: Implement settings interface -->
        </div>
    </main>
    
    <?php require SHARED_PATH . 'partials/footer.php'; ?>
    <?php require SHARED_PATH . 'partials/footer-scripts.php'; ?>
</body>
</html>
