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
        <div class="download-card">
          <div class="card-header">
            <h3 class="filename"><?= e($download['filename'] ?? $download['name']) ?></h3>
            <?php if (!empty($download['version'])): ?>
              <span class="version">Version <?= e($download['version']) ?></span>
            <?php endif; ?>
          </div>
          
          <div class="card-meta">
            <?php if (!empty($download['filesize'])): ?>
              <span class="filesize"><?= formatFileSize($download['filesize']) ?></span>
              <span class="separator">•</span>
            <?php elseif (!empty($download['file_size'])): ?>
              <span class="filesize"><?= e($download['file_size']) ?></span>
              <span class="separator">•</span>
            <?php endif; ?>
            <span class="filetype"><?= strtoupper(e($download['filetype'] ?? $download['file_type'] ?? 'Datei')) ?></span>
          </div>
          
          <?php if(!empty($download['description'])): ?>
          <p class="description"><?= nl2br(e($download['description'])) ?></p>
          <?php endif; ?>
          
          <div class="card-dates">
            <span>Erstellt am: <?= date('d.m.Y', strtotime($download['created_at'])) ?></span>
            <span>Update am: <?= date('d.m.Y', strtotime($download['updated_at'])) ?></span>
          </div>
          
          <div class="card-actions">
            <?php if(isLoggedIn()): ?>
              <a href="/files/download.php?id=<?= $download['id'] ?>" class="btn btn-primary">
                <span class="icon">↓</span>
                Direkt Download
              </a>
            <?php else: ?>
              <a href="/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary">
                <span class="icon">↓</span>
                Anmelden zum Download
              </a>
            <?php endif; ?>
            
            <?php if (!empty($download['alternative_link'])): ?>
              <a href="<?= e($download['alternative_link']) ?>" 
                 class="btn btn-secondary" 
                 target="_blank" 
                 rel="noopener noreferrer">
                <span class="icon">🔗</span>
                Alternativer Link
              </a>
            <?php endif; ?>
          </div>
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
