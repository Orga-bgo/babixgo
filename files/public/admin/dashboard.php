<?php
/**
 * BabixGO Files - Admin Dashboard
 */

require_once __DIR__ . '/../init.php';

initSession();
requireAdmin();

$stats = getDashboardStats();
$pageTitle = 'Admin Dashboard';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#A0D8FA">
    <meta name="robots" content="noindex, nofollow">
    
    <title><?php echo e($pageTitle); ?> - <?php echo e(SITE_NAME); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/header-simple.css">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css">
    
    <!-- Google Analytics Tracking Configuration -->
    <?php include __DIR__ . '/../../includes/tracking.php'; ?>
</head>
<body>
    <?php include __DIR__ . "/../../includes/header.php"; ?>
    <!-- Header -->

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <section class="admin-section">
                <h2><span class="icon-emoji">⚙️</span> Admin Dashboard</h2>
                
                <!-- Stats -->
                <div class="admin-stats">
                    <div class="stat-card">
                        <span class="stat-value"><?php echo e($stats['downloads']); ?></span>
                        <span class="stat-label">Downloads</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?php echo e($stats['total_downloads']); ?></span>
                        <span class="stat-label">Gesamt-Downloads</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?php echo e($stats['users']); ?></span>
                        <span class="stat-label">Benutzer</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-value"><?php echo e($stats['comments']); ?></span>
                        <span class="stat-label">Kommentare</span>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="admin-grid">
                    <div class="admin-card content-card">
                        <h3>Downloads verwalten</h3>
                        <p>Upload, bearbeiten und löschen von Download-Einträgen</p>
                        <a href="/admin/manage-downloads.php" class="btn btn-primary">📥 Verwalten</a>
                    </div>
                    
                    <div class="admin-card content-card">
                        <h3>Neuer Download</h3>
                        <p>Einen neuen Download-Eintrag erstellen</p>
                        <a href="/admin/upload.php" class="btn btn-primary">➕ Upload</a>
                    </div>
                    
                    <div class="admin-card content-card">
                        <h3>User verwalten</h3>
                        <p>User-Rollen ändern, sperren oder löschen</p>
                        <a href="/admin/manage-users.php" class="btn btn-primary">👥 Verwalten</a>
                    </div>
                    
                    <div class="admin-card content-card">
                        <h3>Kommentare moderieren</h3>
                        <p>Unangemessene Kommentare überprüfen und löschen</p>
                        <a href="/admin/moderate-comments.php" class="btn btn-primary">💬 Verwalten</a>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(SITE_NAME); ?>. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <!-- Cookie Consent Banner -->
    <?php include __DIR__ . '/../../includes/cookie-banner.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/header.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/cookie-consent.js"></script>
</body>
</html>
