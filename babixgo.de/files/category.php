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
    header('Location: /files/');
    exit;
}

// Kategorie laden
$category = getCategoryBySlug($slug);

if(!$category) {
    header('HTTP/1.0 404 Not Found');
    require __DIR__ . '/../404.php';
    exit;
}

// Downloads der Kategorie laden
$downloads = getDownloadsByCategory($category['id']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?php require __DIR__ . '/../shared/partials/head-meta.php'; ?>

  <title><?= e($category['name']) ?> - babixGO Files</title>
  <meta name="description" content="<?= e($category['description']) ?> - BabixGO Files Download Portal" />
  <link rel="canonical" href="https://babixgo.de/files/kategorie/<?= e($category['slug']) ?>/" />

  <meta property="og:title" content="<?= e($category['name']) ?> - babixGO Files" />
  <meta property="og:description" content="<?= e($category['description']) ?>" />
  <meta property="og:url" content="https://babixgo.de/files/kategorie/<?= e($category['slug']) ?>/" />

  <meta name="twitter:title" content="<?= e($category['name']) ?> - babixGO Files" />
  <meta name="twitter:description" content="<?= e($category['description']) ?>" />

  <?php require __DIR__ . '/../shared/partials/head-links.php'; ?>
</head>

<body>
  <?php require __DIR__ . '/../shared/partials/tracking.php'; ?>
  <?php require __DIR__ . '/../shared/partials/cookie-banner.php'; ?>
  <?php require __DIR__ . '/../shared/partials/header.php'; ?>

  <main id="main-content">
    <div class="box">

      <!-- Breadcrumb -->
      <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="/">Home</a>
        <span class="separator">›</span>
        <a href="/files/">Files</a>
        <span class="separator">›</span>
        <span class="current"><?= e($category['name']) ?></span>
      </nav>

      <!-- Category Header -->
      <div class="section-card">
        <h1 class="welcome-title"><?php if($category['icon']): ?><?= $category['icon'] ?> <?php endif; ?><?= e($category['name']) ?></h1>
        <?php if($category['description']): ?>
        <p class="intro-text"><?= e($category['description']) ?></p>
        <?php endif; ?>
      </div>

      <!-- Downloads Section -->
      <div class="section-header">
        <h2><img src="/assets/material-symbols/download.svg" class="icon icon-service" alt="" width="48" height="48">Downloads</h2>
      </div>

      <?php if(empty($downloads)): ?>
        <div class="content-card">
          <div class="content-card-header">
            <div class="content-card-title">
              <h3>Noch keine Downloads verfügbar</h3>
            </div>
          </div>
          <p class="content-card-description">
            In dieser Kategorie sind noch keine Downloads vorhanden. Schau später wieder vorbei!
          </p>
        </div>
      <?php else: ?>
        <?php foreach($downloads as $download): ?>
        <div class="content-card">
          <div class="content-card-header">
            <div class="content-card-title">
              <h3><?= e($download['name'] ?: $download['filename']) ?></h3>
            </div>
            <?php if(isLoggedIn()): ?>
              <a href="/files/download.php?id=<?= $download['id'] ?>&type=<?= $download['filetype'] ?>" class="btn btn-link">Herunterladen</a>
            <?php else: ?>
              <a href="/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-link">Anmelden</a>
            <?php endif; ?>
          </div>
          
          <?php if(!empty($download['description'])): ?>
          <p class="content-card-description"><?= nl2br(e($download['description'])) ?></p>
          <?php endif; ?>
          
          <div class="info-line">
            <span class="info-line-label">Typ</span>
            <span class="info-line-value"><?= e($download['file_type'] ?? 'Datei') ?></span>
          </div>
          <?php if (!empty($download['file_size'])): ?>
          <div class="info-line">
            <span class="info-line-label">Größe</span>
            <span class="info-line-value"><?= e($download['file_size']) ?></span>
          </div>
          <?php endif; ?>
          <div class="info-line">
            <span class="info-line-label">Downloads</span>
            <span class="info-line-value"><?= $download['download_count'] ?></span>
          </div>
          
          <?php if(!empty($download['alternative_link'])): ?>
          <div class="u-mt-16">
            <a href="<?= e($download['alternative_link']) ?>" class="btn btn-secondary" target="_blank" rel="noopener">Alternativer Link</a>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Back Link -->
      <div class="u-mt-24">
        <a href="/files/" class="btn btn-secondary">Zurück zur Übersicht</a>
      </div>

    </div>
  </main>

  <?php require __DIR__ . '/../shared/partials/footer.php'; ?>
  <?php require __DIR__ . '/../shared/partials/scripts.php'; ?>
</body>
</html>
