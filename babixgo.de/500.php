<!DOCTYPE html>
<html lang="de">
<head>
  <?php define('BABIXGO_ROBOTS_OVERRIDE', true); ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-meta.php'; ?>

  <title>500 - Interner Serverfehler | babixGO</title>
  <meta name="description" content="Ein Fehler ist aufgetreten. Bitte versuchen Sie es später erneut." />
  <link rel="canonical" href="https://babixgo.de/500.php" />
  <meta name="robots" content="noindex, nofollow" />

  <meta property="og:title" content="500 - Interner Serverfehler | babixGO" />
  <meta property="og:description" content="Ein Fehler ist aufgetreten." />
  <meta property="og:url" content="https://babixgo.de/500.php" />

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-links.php'; ?>
</head>

<body>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/tracking.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/cookie-banner.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/header.php'; ?>

  <main id="main-content" style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div style="text-align: center; max-width: 600px; padding: 2rem;">
      <h1 style="font-size: 6rem; margin: 0; color: #ef4444;">500</h1>
      <h2 style="font-size: 2rem; margin: 1rem 0; color: #e0e0e0;">Interner Serverfehler</h2>
      <p style="font-size: 1.2rem; color: #b0b0b0; margin-bottom: 2rem;">
        Ein unerwarteter Fehler ist aufgetreten. Bitte versuchen Sie es später erneut.
      </p>
      <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <a href="/" style="display: inline-block; padding: 0.75rem 1.5rem; background: #6366f1; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
          Zur Startseite
        </a>
        <a href="/kontakt/" style="display: inline-block; padding: 0.75rem 1.5rem; background: #2a2a2a; color: #6366f1; text-decoration: none; border-radius: 8px; font-weight: 600;">
          Kontakt
        </a>
      </div>
    </div>
  </main>

  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer.php'; ?>
  <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/footer-scripts.php'; ?>
</body>
</html>
