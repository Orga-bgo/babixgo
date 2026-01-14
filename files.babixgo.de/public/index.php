<?php
/**
 * BabixGO Files - Download Portal
 * Main page showing categories grid
 */

require_once __DIR__ . '/init.php';

initSession();

// Get all categories with download count
$categories = getCategories();
$pageTitle = 'Home';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Download-Portal der BabixGO Community - Kostenlose Downloads und Tools">
    <meta name="theme-color" content="#A0D8FA">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo e(SITE_NAME); ?> - Download Portal">
    <meta property="og:description" content="Download-Portal der BabixGO Community">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo e(SITE_URL); ?>">
    
    <title><?php echo e($pageTitle); ?> - <?php echo e(SITE_NAME); ?></title>
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/header-simple.css">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css">
    
    <!-- Google Analytics Tracking Configuration -->
    <?php include __DIR__ . '/../includes/tracking.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            
            <!-- Hero Section -->
            <section class="hero-section">
                <h1>BabixGO <span class="logo-go">Files</span></h1>
                <p class="hero-description">Download-Portal für die BabixGO Community</p>
            </section>

            <!-- Kategorien Grid -->
            <section class="categories-section">
                <h2>
                    📁 Kategorien
                </h2>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" data-dismiss="5000">
                        <?php echo e($_GET['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-error" data-dismiss="5000">
                        <?php echo e($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="categories-grid">
                    <?php foreach($categories as $category): ?>
                    <div class="category-card">
                        <div class="category-icon">
                            <?php if($category['icon']): ?>
                                <img src="<?= e($category['icon']) ?>" alt="">
                            <?php else: ?>
                                📁
                            <?php endif; ?>
                        </div>
                        
                        <h3><?= e($category['name']) ?></h3>
                        
                        <p class="category-description">
                            <?= e($category['description']) ?>
                        </p>
                        
                        <div class="category-meta">
                            <span><?= $category['download_count'] ?> Download<?= $category['download_count'] != 1 ? 's' : '' ?></span>
                        </div>
                        
                        <a href="/kategorie/<?= e($category['slug']) ?>" class="btn btn-primary btn-category">
                            Zu den Downloads
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-links">
                <a href="https://babixgo.de">BabixGO</a>
                <a href="/impressum.php">Impressum</a>
                <a href="/datenschutz.php">Datenschutz</a>
            </div>
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(SITE_NAME); ?>. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <!-- Cookie Consent Banner -->
    <?php include __DIR__ . '/../includes/cookie-banner.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/header.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/cookie-consent.js"></script>
</body>
</html>
