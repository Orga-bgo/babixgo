<!DOCTYPE html>
<html lang="de">
<head>
  <?php define('BABIXGO_ROBOTS_OVERRIDE', true); ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/head-meta.php'; ?>

  <title>Downloads – Alle Tools & Apps | babixGO</title>
  <meta name="description" content="Downloadbereich für babixGO: vPhoneOS, Aurora Store und babixGO App. Alles was du für Monopoly GO Freundschaftsbalken brauchst." />
  <meta name="robots" content="index, follow" />
  <link rel="canonical" href="https://babixgo.de/downloads/" />

  <meta property="og:title" content="Downloads – Alle Tools & Apps | babixGO" />
  <meta property="og:description" content="Downloadbereich für babixGO: vPhoneOS, Aurora Store und babixGO App." />
  <meta property="og:url" content="https://babixgo.de/downloads/" />
  <meta property="og:image:alt" content="Alle Downloads für babixGO-Anleitungen" />

  <meta name="twitter:title" content="Downloads – Alle Tools & Apps | babixGO" />
  <meta name="twitter:description" content="Downloadbereich für babixGO: vPhoneOS, Aurora Store und babixGO App." />

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
    <div class="box">
      
      <!-- Hero Card -->
      <div class="section-card">
        <h1 class="welcome-title">Downloads</h1>
        <p class="intro-text">
          Alle Apps und Tools für unsere Anleitungen – sicher und direkt von uns bereitgestellt.
        </p>
      </div>

      <!-- Info Card -->
      <div class="section-card section-card--info">
        <div class="download-callout-header">
          <h3 class="download-callout-title">Wichtiger Hinweis</h3>
        </div>
        <p class="download-callout-text">
          Alle Downloads sind sicher und geprüft. Bei Fragen steht unser 
          <a href="https://wa.me/4915223842897" target="_blank" rel="noopener" class="link-primary-strong">Support</a> bereit.
        </p>
      </div>

      <!-- Downloads Section -->
      <div class="section-header">
        <h2><img src="/assets/material-symbols/download.svg" class="icon icon-info" alt="" width="48" height="48">Verfügbare Downloads</h2>
      </div>
      <div class="section-card">

        <h3 id="vphone">vPhoneOS</h3>
        <p>
          Erstellt virtuelle Android-Systeme, die als abgeschottetes Betriebssystem direkt auf einem Android-Smartphone laufen. Es erzeugt eine sichere, isolierte Umgebung, in der Apps separat ausgeführt werden können – ideal für Datenschutz, das Testen von Software oder die Nutzung zweiter Accounts.
        </p>
        <p>
          <strong>Braucht ihr für:</strong> Freundschaftsbalken (Android)
        </p>
        <div class="download-actions u-mt-12">
          <a class="btn btn-primary" href="https://files.babixgo.de/apk/vphoneOS-v4-12-8.apks" rel="nofollow" download>
            Direkt Download
          </a>
          <a class="btn btn-ghost" href="https://play.google.com/store/apps/details?id=com.vphonegaga.titan" target="_blank" rel="noopener">
            Play Store
          </a>
        </div>

        <h3 id="aurora" class="u-mt-32">Aurora Store</h3>
        <p>
          Der Aurora Store ist eine quelloffene, inoffizielle Alternative zum Google Play Store. Sie erlaubt es, Apps aus dem Play-Store-Katalog herunterzuladen und zu aktualisieren, ohne dafür ein Google-Konto auf dem Gerät verwenden zu müssen.
        </p>
        <p>
          <strong>Braucht ihr für:</strong> Freundschaftsbalken (Android); Freundschaftsbalken (Windows)
        </p>
        <div class="download-actions u-mt-12">
          <a class="btn btn-primary" href="https://files.babixgo.de/apk/aurora-store-v71.apk" rel="nofollow" download>
            APK Download
          </a>
          <a class="btn btn-ghost" href="https://auroraoss.com" target="_blank" rel="noopener">
            Offizielle Website
          </a>
        </div>

        <h3 id="babixgo" class="u-mt-32">babixGO</h3>
        <p>
          Kleine App um euch das Leben leichter zu machen. Führt einfache Shell-Befehle aus, um Freunde hinzuzufügen.
        </p>
        <p>
          <strong>Braucht ihr für:</strong> Freundschaftsbalken (Android); Freundschaftsbalken (Windows)
        </p>
        <div class="download-actions u-mt-12">
          <a class="btn btn-primary" href="https://files.babixgo.de/apk/babixgo-fbar.apk" rel="nofollow" download>
            APK Download
          </a>
        </div>

      </div>

      <!-- CTA Card -->
      <div class="section-card community-card">
        <div class="section-header">
          <h2><img src="/assets/material-symbols/done-all.svg" class="icon icon-service" alt="" width="48" height="48">Alle Downloads fertig?</h2>
        </div>
        <p class="community-text">
          Perfekt! Folge jetzt unserer Schritt-für-Schritt Anleitung, um deinen Freundschaftsbalken zu füllen.
        </p>
        <div class="community-actions">
          <a href="/anleitungen/freundschaftsbalken-fuellen/" class="btn btn-primary btn-large">
            Zur Freundschaftsbalken-Anleitung
          </a>
        </div>
      </div>

    </div>
  </main>

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/shared/partials/footer-scripts.php'; ?>

</body>
</html>
