<?php
/**
 * Kontaktseite - babixGO
 * DSGVO-konform mit Datenschutz-Checkbox
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/head-meta.php'; ?>

  <title>Kontakt – Wir helfen dir gerne | babixGO</title>
  <meta name="description" content="Kontaktiere babixGO per WhatsApp, Facebook oder E-Mail. Schneller Support für alle Monopoly GO Services – Sticker, Würfel, Events und Accounts." />
  <link rel="canonical" href="https://babixgo.de/kontakt/" />

  <meta property="og:title" content="Kontakt – Wir helfen dir gerne | babixGO" />
  <meta property="og:description" content="Kontaktiere babixGO per WhatsApp, Facebook oder E-Mail. Schneller Support für Monopoly GO Services – wir antworten zügig." />
  <meta property="og:url" content="https://babixgo.de/kontakt/" />
  <meta property="og:image:alt" content="Kontakt – Wir helfen dir gerne | babixGO" />

  <meta name="twitter:title" content="Kontakt – Wir helfen dir gerne | babixGO" />
  <meta name="twitter:description" content="Kontaktiere babixGO per WhatsApp, Facebook oder E-Mail. Schneller Support für Monopoly GO Services – wir antworten zügig." />

  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/head-links.php'; ?>
</head>

<body>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/tracking.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/cookie-banner.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

  <main id="main-content" class="contact-page">
    <?php
    $statusMessage = '';
    $statusClass = '';
    $status = $_GET['status'] ?? '';
    $messages = [
      'success' => 'Nachricht erfolgreich gesendet! Wir melden uns bald bei dir.',
      'error' => 'Fehler beim Senden. Bitte versuche es erneut oder kontaktiere uns direkt per WhatsApp.',
      'validation' => 'Bitte fülle alle Pflichtfelder korrekt aus.',
      'rate' => 'Zu viele Anfragen. Bitte versuche es später erneut.',
      'config' => 'Serverkonfiguration fehlt. Bitte versuche es später erneut.'
    ];

    if (isset($messages[$status])) {
      $statusMessage = $messages[$status];
      $statusClass = $status === 'success' ? 'success' : 'error';
    }
    ?>
    <div class="contact-hero">
      <h1 class="welcome-title">Kontakt</h1>
      <p class="intro-text">Über folgende Wege kannst du uns erreichen</p>
    </div>

    <div class="contact-methods">
      <a href="https://wa.me/4915223842897" target="_blank" rel="noopener noreferrer" class="contact-method">
        <div class="contact-icon whatsapp">
          <img src="/assets/icons/whatsapp.svg" class="contact-icon-image" alt="WhatsApp Icon" width="24" height="24">
        </div>
        <div class="contact-info">
          <div class="contact-title">WhatsApp</div>
          <div class="contact-subtitle">Hier geht's direkt zum Chat</div>
        </div>
      </a>

      <a href="https://www.facebook.com/share/1DC2snqois/" target="_blank" rel="noopener noreferrer" class="contact-method">
        <div class="contact-icon facebook">
          <img src="/assets/icons/facebook.svg" class="contact-icon-image" alt="Facebook Icon" width="40" height="40">
        </div>
        <div class="contact-info">
          <div class="contact-title">Facebook</div>
          <div class="contact-subtitle">Besuche uns auf Facebook</div>
        </div>
      </a>

      <a href="mailto:info@babixgo.de" class="contact-method">
        <div class="contact-icon email">
          <img src="/assets/icons/mail.svg" class="contact-icon-image" alt="E-Mail Icon" width="24" height="24">
        </div>
        <div class="contact-info">
          <div class="contact-title">E-Mail</div>
          <div class="contact-subtitle">info@babixgo.de</div>
        </div>
      </a>
    </div>

    <div class="section-card form-section">
      <div class="section-header">
        <h2><img src="/assets/material-symbols/mail.svg" class="icon icon-service" alt="" width="48" height="48">Kontaktformular</h2>
      </div>

      <div id="statusMessage" class="status-message <?php echo $statusClass; ?><?php echo $statusMessage ? ' show' : ''; ?>" role="status" aria-live="polite" aria-atomic="true">
        <?php echo htmlspecialchars($statusMessage); ?>
      </div>

      <form id="contactForm" action="/kontakt/contact.php" method="post">
        <div class="is-hidden" aria-hidden="true">
          <label for="website">Website</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off" />
        </div>
        <input type="hidden" name="form_time" value="<?php echo time(); ?>" />
        <div class="form-group">
          <label class="form-label" for="name">
            Name <span class="required">*</span>
          </label>
          <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-input" 
            placeholder="Dein Name"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="email">
            E-Mail <span class="required">*</span>
          </label>
          <input 
            type="email" 
            id="email" 
            name="email" 
            class="form-input" 
            placeholder="deine@email.de"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="whatsapp">
            WhatsApp-Nummer
          </label>
          <input 
            type="tel" 
            id="whatsapp" 
            name="whatsapp" 
            class="form-input" 
            placeholder="+49 123 456789"
          />
          <div class="form-hint">Optional – falls du per WhatsApp kontaktiert werden möchtest</div>
        </div>

        <div class="form-group">
          <label class="form-label" for="message">
            Nachricht <span class="required">*</span>
          </label>
          <textarea 
            id="message" 
            name="message" 
            class="form-textarea" 
            placeholder="Schreib uns deine Nachricht..."
            required
          ></textarea>
        </div>

        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" id="privacy" name="privacy" value="1" required>
            <span>Ich habe die <a href="/datenschutz/" target="_blank" rel="noopener noreferrer">Datenschutzerklärung</a> zur Kenntnis genommen. Ich stimme zu, dass meine Angaben zur Kontaktaufnahme und Zuordnung für eventuelle Rückfragen gespeichert werden. <span class="required">*</span></span>
          </label>
        </div>

        <button type="submit" class="form-submit" id="submitBtn">
          Nachricht senden
        </button>
      </form>
    </div>

    <div class="info-note">
      <strong><span class="emoji" role="img" aria-label="Glühbirne">💡</span>Schneller Support:</strong> Für dringende Anfragen empfehlen wir WhatsApp – 
      dort antworten wir meist innerhalb weniger Minuten!
    </div>
  </main>
  
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/footer.php'; ?>
  <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/footer-scripts.php'; ?>
</body>
</html>
