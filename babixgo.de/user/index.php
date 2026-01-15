<?php
/**
 * User Dashboard / Profile Overview
 * URL: babixgo.de/user/
 */

define('BASE_PATH', dirname(__DIR__, 1) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once __DIR__ . '/includes/auth-check.php';

// Page configuration
$pageTitle = 'My Profile - babixGO';
$pageDescription = 'Manage your babixGO profile, downloads, and comments';
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
            <h1>Welcome, <?= htmlspecialchars($currentUsername) ?>!</h1>
            
            <div class="dashboard-grid">
                <!-- Profile Card -->
                <div class="dashboard-card">
                    <h2>Profile</h2>
                    <p><strong>Username:</strong> <?= htmlspecialchars($currentUsername) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($currentUserEmail) ?></p>
                    <p><strong>Role:</strong> <?= htmlspecialchars($currentUserRole) ?></p>
                    <a href="/user/edit-profile" class="btn btn-primary">Edit Profile</a>
                </div>
                
                <!-- Quick Links -->
                <div class="dashboard-card">
                    <h2>Quick Links</h2>
                    <ul class="quick-links">
                        <li><a href="/user/my-comments">My Comments</a></li>
                        <li><a href="/user/my-downloads">My Downloads</a></li>
                        <li><a href="/user/settings">Settings</a></li>
                        <li><a href="/files/">Browse Downloads</a></li>
                        <?php if ($currentUserRole === 'admin'): ?>
                            <li><a href="/admin/">Admin Panel</a></li>
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
