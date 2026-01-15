<!DOCTYPE html>
<html lang="de">
<head>
  <?php define('BABIXGO_ROBOTS_OVERRIDE', true); ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-meta.php'; ?>

  <title>404 - Seite nicht gefunden | babixGO</title>
  <meta name="description" content="Die gesuchte Seite wurde nicht gefunden. Zurück zur babixGO Startseite." />
  <link rel="canonical" href="https://babixgo.de/404.php" />
  <meta name="robots" content="noindex, nofollow" />

  <meta property="og:title" content="404 - Seite nicht gefunden | babixGO" />
  <meta property="og:description" content="Die gesuchte Seite wurde nicht gefunden. Zurück zur babixGO Startseite." />
  <meta property="og:url" content="https://babixgo.de/404.php" />
  <meta property="og:image:alt" content="Die gesuchte Seite wurde nicht gefunden. Zurück zur babixGO Startseite." />

  <meta name="twitter:title" content="404 - Seite nicht gefunden | babixGO" />
  <meta name="twitter:description" content="Die gesuchte Seite wurde nicht gefunden. Zurück zur babixGO Startseite." />

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-links.php'; ?>

  
  <!-- Structured Data (site-wide) -->
  <?php
    $structured_data_files = ['organization.json', 'website.json'];
    require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/structured-data.php';
  ?>
  
</head>

<body>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/tracking.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/cookie-banner.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/header.php'; ?>

  <main id="main-content">



    
<!-- ERROR HERO -->
<div class="error-hero">
  <div class="dice-animation" role="img" aria-label="Würfel">🎲</div>
  <p class="error-code" aria-hidden="true">404</p>
  <h1 class="error-message">Ups! Würfel ins Leere geworfen</h1>
  <p class="error-submessage">
    Die gesuchte Seite existiert nicht oder wurde verschoben.
  </p>
</div>

<!-- SUGGESTIONS -->
<section class="section-card">
  <div class="section-header">
    <h2><img src="/assets/material-symbols/help-center.svg" class="icon icon-help" alt="" width="48" height="48">Wohin möchtest du?</h2>
  </div>
  
  <div class="error-actions">
    <a href="/" class="error-btn error-btn-primary">
      <span class="emoji" role="img" aria-label="Haus">🏠</span>Zur Startseite
    </a>
    <a href="/anleitungen/" class="error-btn error-btn-secondary">
      <span class="emoji" role="img" aria-label="Bücher">📚</span>Anleitungen
    </a>
    <a href="/downloads/" class="error-btn error-btn-secondary">
      <span class="emoji" role="img" aria-label="Download">⬇️</span>Downloads
    </a>
  </div>
</section>

<!-- POPULAR PAGES -->
<section class="section-card">
  <div class="section-header">
    <h2><img src="/assets/material-symbols/star.svg" class="icon icon-service" alt="" width="48" height="48">Beliebte Seiten</h2>
  </div>
  
  <div class="line">
    <span>Partner-Events</span>
    <span class="price">
      <a href="/#partner" class="link-accent">Infos & Preise</a>
    </span>
  </div>
  
  <div class="line">
    <span>Racers</span>
    <span class="price">
      <a href="/#racers" class="link-accent">Zum Service</a>
    </span>
  </div>
  
  <div class="line">
    <span>Sticker</span>
    <span class="price">
      <a href="/#sticker" class="link-accent">Preisübersicht</a>
    </span>
  </div>
  
  <div class="line">
    <span>Freundschaftsbalken Anleitung</span>
    <span class="price">
      <a href="/anleitungen/freundschaftsbalken-fuellen/" class="link-accent">Zum Guide</a>
    </span>
  </div>
</section>

<!-- SEARCH HELP -->
<section class="section-card">
  <div class="section-header">
    <h2><img src="/assets/material-symbols/help-center.svg" class="icon icon-help" alt="" width="48" height="48">Suchst du etwas Bestimmtes?</h2>
  </div>
  
  <p class="desc">
    Falls du eine bestimmte Information suchst, schreib uns einfach:
  </p>
  
  <div class="line">
    <span>WhatsApp:</span>
    <span class="price">
      <a href="https://wa.me/4915223842897" target="_blank" rel="noopener" class="link-whatsapp">
        Nachricht senden
      </a>
    </span>
  </div>
  
  <div class="line">
    <span>Facebook:</span>
    <span class="price">
      <a href="https://www.facebook.com/share/1DC2snqois/" target="_blank" rel="noopener" class="link-facebook">
        Community
      </a>
    </span>
  </div>
</section>


  </main>

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer-scripts.php'; ?>

</body>
</html>
