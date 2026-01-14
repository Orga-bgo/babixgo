<?php
/**
 * BabixGO Files - Category Detail Page
 * Shows downloads for a specific category
 */

require_once __DIR__ . '/init.php';

initSession();

// Slug aus URL extrahieren
$requestUri = $_SERVER['REQUEST_URI'];
$uriParts = explode('/', trim($requestUri, '/'));
$slug = end($uriParts);

// Remove query string if present
if (strpos($slug, '?') !== false) {
    $slug = substr($slug, 0, strpos($slug, '?'));
}

if(empty($slug) || $slug === 'kategorie') {
    header('Location: /index.php');
    exit;
}

// Kategorie laden
$category = getCategoryBySlug($slug);

if(!$category) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html><head><title>404 - Kategorie nicht gefunden</title></head><body><h1>404 - Kategorie nicht gefunden</h1><p><a href="/index.php">Zurück zur Startseite</a></p></body></html>';
    exit;
}

// Downloads der Kategorie laden
$downloads = getDownloadsByCategory($category['id']);
$pageTitle = $category['name'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($category['description']) ?> - BabixGO Files Download Portal">
    <meta name="theme-color" content="#A0D8FA">
    
    <title><?= e($category['name']) ?> - <?= e(SITE_NAME) ?></title>
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/header-simple.css">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css">
    
    <!-- Google Analytics Tracking Configuration -->
    <?php include __DIR__ . '/../includes/tracking.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; ?>
    
    <main class="main-content">
        <div class="container">
            
            <!-- Breadcrumb Navigation -->
            <nav class="breadcrumb">
                <a href="/index.php">Home</a>
                <span class="separator">›</span>
                <span class="current"><?= e($category['name']) ?></span>
            </nav>

            <!-- Kategorie Header -->
            <section class="category-header">
                <h1><?= e($category['name']) ?></h1>
                <?php if($category['description']): ?>
                <p class="category-intro"><?= e($category['description']) ?></p>
                <?php endif; ?>
            </section>

            <!-- Downloads Sektion -->
            <section class="downloads-section">
                <h2>
                    📥 Downloads
                </h2>
                
                <?php if(empty($downloads)): ?>
                <div class="empty-state content-card">
                    <div class="empty-state-icon">📦</div>
                    <h3>Noch keine Downloads verfügbar</h3>
                    <p>In dieser Kategorie sind noch keine Downloads vorhanden. Schau später wieder vorbei!</p>
                </div>
                <?php else: ?>
                <div class="downloads-list">
                    <?php foreach($downloads as $download): ?>
                    <div class="download-card content-card">
                        
                        <!-- Download Header -->
                        <div class="download-header">
                            <h3><?= e($download['name']) ?></h3>
                        </div>
                        
                        <!-- Download Meta -->
                        <div class="download-meta">
                            <?php if (!empty($download['file_type'])): ?>
                            <span class="meta-item">
                                📄 <strong>Typ:</strong> <?= e($download['file_type']) ?>
                            </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($download['file_size'])): ?>
                            <span class="meta-item">
                                💾 <strong>Größe:</strong> <?= e($download['file_size']) ?>
                            </span>
                            <?php endif; ?>
                            
                            <span class="meta-item">
                                ⬇️ <strong>Downloads:</strong> <?= $download['download_count'] ?>
                            </span>
                            
                            <span class="meta-item">
                                💬 <strong>Kommentare:</strong> <?= $download['comment_count'] ?>
                            </span>
                        </div>
                        
                        <!-- Beschreibung -->
                        <?php if(!empty($download['description'])): ?>
                        <div class="download-description">
                            <p><?= nl2br(e($download['description'])) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Download Actions -->
                        <?php if(isLoggedIn()): ?>
                        <div class="download-actions">
                            <a href="/download.php?id=<?= $download['id'] ?>" class="btn btn-primary btn-download">
                                ⬇️ Herunterladen
                            </a>
                            <?php if(!empty($download['alternative_link'])): ?>
                            <a href="<?= e($download['alternative_link']) ?>" class="btn btn-secondary" target="_blank" rel="noopener">
                                🔗 Alternativer Link
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="login-prompt">
                            <p>🔒 <a href="/login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>">Anmelden</a>, um Downloads zu sehen</p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
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
            <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Alle Rechte vorbehalten.</p>
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
