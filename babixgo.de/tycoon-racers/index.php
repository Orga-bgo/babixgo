<!DOCTYPE html>
<html lang="de">
<head>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-meta.php'; ?>

  <title>Tycoon Racers - Monopoly GO | babixGO</title>
  <meta name="description" content="Monopoly GO Tycoon Racers bei babixGO: Beende das Event auf den vorderen Plätzen mit garantierten Belohnungen. Wir stellen dein Team zusammen – du kassierst." />
  <link rel="canonical" href="https://babixgo.de/tycoon-racers/" />

  <meta property="og:title" content="Tycoon Racers - Monopoly GO | babixGO" />
  <meta property="og:description" content="Monopoly GO Tycoon Racers bei babixGO: Beende das Event auf den vorderen Plätzen. Wir stellen dein Team zusammen – du kassierst." />
  <meta property="og:url" content="https://babixgo.de/tycoon-racers/" />

  <meta name="twitter:title" content="Tycoon Racers - Monopoly GO | babixGO" />
  <meta name="twitter:description" content="Monopoly GO Tycoon Racers bei babixGO: Beende das Event auf den vorderen Plätzen. Wir stellen dein Team zusammen – du kassierst." />

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-links.php'; ?>

  <!-- Structured Data -->
  <?php
    $structured_data_files = ['organization.json'];
    require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/structured-data.php';
  ?>
</head>

<body>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/tracking.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/cookie-banner.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/header.php'; ?>

  <main id="main-content">
    <div class="box">

      <div class="section-card">
        <h1 class="welcome-title">Tycoon Racers</h1>
        <p class="intro-text">
          Preis gilt für einen Platz im Team. Die 3 weiteren Teammitglieder sind Accounts von uns.
          Sie sorgen dafür, dass du alle möglichen Rundenbelohnungen / Kisten erhältst.
        </p>

        <p class="text-muted u-mt-16">
          Weitere Infos zu Tycoon Racers folgen in Kürze!
        </p>
      </div>

      <div class="section-header">
        <h2><img src="/assets/material-symbols/info.svg" class="icon icon-info" alt="" width="48" height="48">Details & Garantie</h2>
      </div>
      <div class="section-card">
        <div class="info-line">
          <span class="info-line-label">Preis je Platz</span>
          <span class="info-line-value">45 €</span>
        </div>
        <p class="u-mt-element text-muted text-small">
          Keine Garantie auf Platz 1 – auch wenn wir diesen in über 90% der Rennen erreichen.
        </p>
        <p class="desc-small u-mt-10">
          <strong>Bitte beachten:</strong> In ungefähr einem von 25 Rennen kann es vorkommen, dass wir nicht alle Belohnungen erspielen können. In diesem Fall erhaltet ihr 15€ zurück.
        </p>
      </div>

      <div class="section-header">
        <h2><img src="/assets/material-symbols/groups.svg" class="icon icon-cta" alt="" width="48" height="48">Jetzt beitreten</h2>
      </div>
      <div class="section-card">
        <p>Du möchtest einen Platz in unserem Team? Kontaktiere uns direkt!</p>
        <div class="info-box-actions u-mt-16">
          <a href="https://wa.me/4915223842897" target="_blank" rel="noopener" class="btn btn-primary">
            Per WhatsApp anfragen
          </a>
        </div>
      </div>

    </div>
  </main>

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer-scripts.php'; ?>
</body>
</html>
