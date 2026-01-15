<?php
/**
 * BabixGO Files - Download Portal
 * Main page showing categories grid
 */

require_once __DIR__ . '/init.php';

initSession();

// Get all categories with download count
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-meta.php'; ?>

  <title>Downloads - babixGO Files</title>
  <meta name="description" content="Download-Portal der BabixGO Community - Kostenlose Downloads, Apps und Tools für Monopoly GO." />
  <link rel="canonical" href="https://babixgo.de/files/" />

  <meta property="og:title" content="Downloads - babixGO Files" />
  <meta property="og:description" content="Download-Portal der BabixGO Community - Kostenlose Downloads, Apps und Tools für Monopoly GO." />
  <meta property="og:url" content="https://babixgo.de/files/" />

  <meta name="twitter:title" content="Downloads - babixGO Files" />
  <meta name="twitter:description" content="Download-Portal der BabixGO Community - Kostenlose Downloads, Apps und Tools für Monopoly GO." />

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-links.php'; ?>
</head>

<body>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/tracking.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/cookie-banner.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/header.php'; ?>

  <main id="main-content">
    <div class="box">

      <!-- Hero Section -->
      <div class="section-card">
        <h1 class="welcome-title">babixGO <span class="logo-go">Files</span></h1>
        <p class="intro-text">
          Download-Portal für die BabixGO Community. Hier findest du alle Apps, Tools und Scripts.
        </p>
      </div>

      <?php if (isset($_GET['success'])): ?>
        <div class="notice-box notice-box--success u-mt-16">
          <span class="notice-box__icon">✓</span>
          <p class="notice-box__text"><?php echo e($_GET['success']); ?></p>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_GET['error'])): ?>
        <div class="notice-box notice-box--error u-mt-16">
          <span class="notice-box__icon">!</span>
          <p class="notice-box__text"><?php echo e($_GET['error']); ?></p>
        </div>
      <?php endif; ?>

      <!-- Kategorien Section -->
      <div class="section-header">
        <h2><img src="/assets/material-symbols/download.svg" class="icon icon-service" alt="" width="48" height="48">Kategorien</h2>
      </div>

      <?php if (empty($categories)): ?>
        <div class="section-card">
          <p class="text-muted">Noch keine Kategorien vorhanden.</p>
        </div>
      <?php else: ?>
        <?php foreach($categories as $category): ?>
        <div class="content-card">
          <div class="content-card-header">
            <div class="content-card-title">
              <h3><?php if($category['icon']): ?><?= $category['icon'] ?> <?php endif; ?><?= e($category['name']) ?></h3>
            </div>
            <a href="/files/kategorie/<?= e($category['slug']) ?>/" class="btn btn-link">Öffnen</a>
          </div>
          <p class="content-card-description">
            <?= e($category['description']) ?>
          </p>
          <div class="info-line">
            <span class="info-line-label">Downloads</span>
            <span class="info-line-value"><?= $category['download_count'] ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </main>

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/scripts.php'; ?>
</body>
</html>
