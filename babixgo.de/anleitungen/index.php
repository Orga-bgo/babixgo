<!DOCTYPE html>
<html lang="de">
<head>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-meta.php'; ?>

  <title>Anleitungen – babixGO</title>
  <meta name="description" content="Kostenlose Monopoly GO Anleitungen bei babixGO: Freundschaftsbalken automatisch füllen, Würfel verdienen und mehr. Schritt-für-Schritt erklärt für Einsteiger." />
  <link rel="canonical" href="https://babixgo.de/anleitungen/" />

  <meta property="og:title" content="Anleitungen – babixGO" />
  <meta property="og:description" content="Kostenlose Monopoly GO Anleitungen: Freundschaftsbalken füllen, Würfel verdienen. Schritt-für-Schritt erklärt für Einsteiger." />
  <meta property="og:url" content="https://babixgo.de/anleitungen/" />
  <meta property="og:image:alt" content="Schritt-für-Schritt Anleitungen für Monopoly GO: Freundschaftsbalken füllen und mehr." />

  <meta name="twitter:title" content="Anleitungen – babixGO" />
  <meta name="twitter:description" content="Kostenlose Monopoly GO Anleitungen: Freundschaftsbalken füllen, Würfel verdienen. Schritt-für-Schritt erklärt für Einsteiger." />

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-links.php'; ?>

  
  <!-- Structured Data (site-wide) -->
  <?php
    $structured_data_files = ['organization.json', 'website.json'];
    require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/structured-data.php';
  ?>
  
</head>

<body>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/tracking.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/cookie-banner.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/header.php'; ?>

  <main id="main-content">
<section class="section-card u-mt-32">
  <h1 class="welcome-title">Anleitungen</h1>
  <p class="intro-text">
    Schritt-für-Schritt Guides für Monopoly GO.
  </p>
</section>

<section class="section-card">
  <div class="section-header">
    <h2><img src="/assets/material-symbols/menu-book.svg" class="icon icon-service" alt="" width="48" height="48">Verfügbare Anleitungen</h2>
  </div>
  
  <div class="line">
    <span>Freundschaftsbalken selber füllen</span>
    <span class="price">
      <a href="/anleitungen/freundschaftsbalken-fuellen/" class="link-accent link-plain">
        Zur Anleitung →
      </a>
    </span>
  </div>
</section>


  </main>

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer-scripts.php'; ?>

</body>
</html>
