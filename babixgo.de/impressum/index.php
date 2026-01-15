<!DOCTYPE html>
<html lang="de">
<head>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-meta.php'; ?>

  <title>Impressum – babixGO</title>
  <meta name="description" content="Impressum von babixGO: Vollständige Betreiberangaben, Kontaktdaten und rechtliche Informationen gemäß § 5 TMG. Monopoly GO Services aus Menden, NRW." />
  <link rel="canonical" href="https://babixgo.de/impressum/" />

  <meta property="og:title" content="Impressum – babixGO" />
  <meta property="og:description" content="Impressum von babixGO: Betreiberangaben, Kontaktdaten und rechtliche Informationen gemäß § 5 TMG." />
  <meta property="og:url" content="https://babixgo.de/impressum/" />
  <meta property="og:image:alt" content="Impressum und Kontaktdaten von babixGO – Monopoly GO Services." />

  <meta name="twitter:title" content="Impressum – babixGO" />
  <meta name="twitter:description" content="Impressum von babixGO: Betreiberangaben, Kontaktdaten und rechtliche Informationen gemäß § 5 TMG." />

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

<!-- IMPRESSUM CONTENT -->
<main id="main-content">
  <div class="box">
    <div class="section-card">
      <h1 class="welcome-title">Impressum</h1>
      <p class="intro-text">
        Angaben gemäß § 5 TMG (Telemediengesetz)
      </p>
    </div>

    <div class="section-header">
      <h2><img src="/assets/material-symbols/account-circle.svg" class="icon icon-service" alt="" width="48" height="48">Betreiber der Website</h2>
    </div>
    <div class="section-card">
      <div class="content-card">
        <div class="info-line">
          <span class="info-line-label">Name:</span>
          <span class="info-line-value">[IHR NAME / FIRMENNAME]</span>
        </div>
        <div class="info-line">
          <span class="info-line-label">Adresse:</span>
          <span class="info-line-value">[STRASSE & HAUSNUMMER]</span>
        </div>
        <div class="info-line">
          <span class="info-line-label">PLZ/Ort:</span>
          <span class="info-line-value">[PLZ STADT]</span>
        </div>
        <div class="info-line">
          <span class="info-line-label">Land:</span>
          <span class="info-line-value">Deutschland</span>
        </div>
      </div>
    </div>

    <div class="section-header">
      <h2><img src="/assets/material-symbols/mail.svg" class="icon icon-service" alt="" width="48" height="48">Kontakt</h2>
    </div>
    <div class="section-card">
      <div class="content-card">
        <div class="info-line">
          <span class="info-line-label">E-Mail:</span>
          <span class="info-line-value"><a href="mailto:info@babixgo.de">info@babixgo.de</a></span>
        </div>
        <div class="info-line">
          <span class="info-line-label">WhatsApp:</span>
          <span class="info-line-value"><a href="https://wa.me/4915223842897" target="_blank" rel="noopener">+49 152 23842897</a></span>
        </div>
      </div>
    </div>
  </div>
</main>

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer-scripts.php'; ?>

</body>
</html>
