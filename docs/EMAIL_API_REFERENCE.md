# E-Mail-System API-Referenz

## Übersicht

Diese Dokumentation beschreibt alle verfügbaren E-Mail-Funktionen und ihre Verwendung im BabixGO-System.

## Module

### 1. Auth-System E-Mail-Modul

**Datei:** `/babixgo.de/auth/includes/mail-helper.php`

---

#### sendEmail()

Sendet eine E-Mail über PHPMailer mit Brevo SMTP.

**Signatur:**
```php
function sendEmail($to, $subject, $message, $headers = []): bool
```

**Parameter:**
- `$to` (string, required): Empfänger-E-Mail-Adresse
- `$subject` (string, required): E-Mail-Betreff
- `$message` (string, required): E-Mail-Nachricht (HTML-formatiert)
- `$headers` (array, optional): Zusätzliche Header (aktuell nicht verwendet)

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Hauptfunktion für E-Mail-Versand. Nutzt PHPMailer mit Brevo SMTP-Konfiguration. Bei Fehler wird die Exception geloggt (wenn DEBUG_MODE aktiv).

**Verwendete Konstanten:**
- `SMTP_HOST` - SMTP-Server-Adresse
- `SMTP_PORT` - SMTP-Port (587)
- `SMTP_USER` - SMTP-Benutzername
- `SMTP_PASS` - SMTP-Passwort/API-Key
- `MAIL_FROM` - Absender-E-Mail-Adresse
- `SITE_NAME` - Website-Name für Absender

**Fehlerbehandlung:**
```php
try {
    // PHPMailer send
    return true;
} catch (Exception $e) {
    error_log("PHPMailer Error: " . $e->getMessage());
    return false;
}
```

**Logging:**
- Fehler werden in PHP error_log geschrieben
- Keine Datenbank-Logging (aktuell)

**Beispiel:**
```php
require_once __DIR__ . '/auth/includes/mail-helper.php';

$to = 'user@example.com';
$subject = 'Willkommen bei BabixGO';
$message = '<h1>Hallo!</h1><p>Willkommen auf unserer Platform.</p>';

if (sendEmail($to, $subject, $message)) {
    echo "E-Mail erfolgreich versendet!";
} else {
    echo "E-Mail-Versand fehlgeschlagen!";
}
```

---

#### sendVerificationEmail()

Sendet eine E-Mail-Verifizierungsnachricht an einen neuen Benutzer.

**Signatur:**
```php
function sendVerificationEmail($email, $username, $token): bool
```

**Parameter:**
- `$email` (string, required): Empfänger-E-Mail-Adresse (neuer Benutzer)
- `$username` (string, required): Benutzername des neuen Benutzers
- `$token` (string, required): 64-Zeichen Hex-Verifizierungstoken

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Generiert und sendet eine HTML-formatierte E-Mail zur E-Mail-Verifizierung nach der Registrierung. Der Verifizierungslink ist 24 Stunden gültig.

**E-Mail-Template:**
- **Betreff:** "E-Mail-Adresse bestätigen - BabixGO"
- **Design:** Dark Theme mit #A0D8FA Akzentfarbe
- **Call-to-Action:** "E-Mail bestätigen" Button
- **Sprache:** Deutsch

**Verifizierungs-URL:**
```
https://babixgo.de/auth/verify-email?token={token}
```

**Verwendung:**
```php
// Nach erfolgreicher Registrierung
$user = new User();
$result = $user->register($username, $email, $password);

if ($result['success']) {
    sendVerificationEmail($email, $username, $result['verification_token']);
}
```

**Template-Variablen:**
- `{{username}}` - Wird durch htmlspecialchars($username) ersetzt
- `{{verify_url}}` - Vollständiger Verifizierungslink
- Aktuelles Jahr für Copyright

**Sicherheitshinweise:**
- Token wird URL-encoded (`urlencode()`)
- Username wird HTML-escaped (`htmlspecialchars()`)
- Link enthält Hinweis auf 24h Gültigkeit

**Beispiel:**
```php
$email = 'newuser@example.com';
$username = 'MaxMustermann';
$token = bin2hex(random_bytes(32)); // 64 Zeichen

if (sendVerificationEmail($email, $username, $token)) {
    echo json_encode(['success' => true, 'message' => 'Verifizierungs-E-Mail versendet']);
} else {
    echo json_encode(['success' => false, 'error' => 'E-Mail-Versand fehlgeschlagen']);
}
```

---

#### sendPasswordResetEmail()

Sendet eine E-Mail mit Anweisungen zum Zurücksetzen des Passworts.

**Signatur:**
```php
function sendPasswordResetEmail($email, $username, $token): bool
```

**Parameter:**
- `$email` (string, required): Empfänger-E-Mail-Adresse
- `$username` (string, required): Benutzername
- `$token` (string, required): 64-Zeichen Hex-Reset-Token

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Sendet eine E-Mail mit einem Link zum Zurücksetzen des Passworts. Der Reset-Link ist 1 Stunde gültig.

**E-Mail-Template:**
- **Betreff:** "Passwort zurücksetzen - BabixGO"
- **Design:** Dark Theme mit #e74c3c Button (Rot für Warnung)
- **Call-to-Action:** "Passwort zurücksetzen" Button
- **Sprache:** Deutsch
- **Sicherheitshinweis:** "Falls du kein neues Passwort angefordert hast, ignoriere diese E-Mail"

**Reset-URL:**
```
https://babixgo.de/auth/reset-password?token={token}
```

**Verwendung:**
```php
// In forgot-password.php
$user = new User();
$result = $user->requestPasswordReset($email);

if ($result['success']) {
    $db = Database::getInstance();
    $userData = $db->fetchOne(
        "SELECT username, email FROM users WHERE id = ?",
        [$result['user_id']]
    );
    
    sendPasswordResetEmail($userData['email'], $userData['username'], $result['reset_token']);
}
```

**Token-Handling:**
- Token wird in `users.reset_token` gespeichert
- Ablaufdatum in `users.reset_token_expires` (1 Stunde)
- Token wird nach erfolgreicher Nutzung gelöscht

**Sicherheit:**
- Token wird URL-encoded
- Username wird HTML-escaped
- Token nur 1 Stunde gültig
- Token kann nur einmal verwendet werden

**Beispiel:**
```php
$email = 'user@example.com';
$username = 'MaxMustermann';
$resetToken = bin2hex(random_bytes(32));

// Token in DB speichern mit Ablaufdatum
$db->execute(
    "UPDATE users SET reset_token = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?",
    [$resetToken, $email]
);

// E-Mail senden
if (sendPasswordResetEmail($email, $username, $resetToken)) {
    echo "Reset-E-Mail versendet";
}
```

---

#### sendWelcomeEmail()

Sendet eine Willkommens-E-Mail nach erfolgreicher E-Mail-Verifizierung.

**Signatur:**
```php
function sendWelcomeEmail($email, $username): bool
```

**Parameter:**
- `$email` (string, required): Empfänger-E-Mail-Adresse
- `$username` (string, required): Benutzername

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Sendet eine Bestätigungs-E-Mail nach erfolgreicher Verifizierung der E-Mail-Adresse. Enthält einen Link zur Login-Seite.

**E-Mail-Template:**
- **Betreff:** "Willkommen bei BabixGO!"
- **Design:** Dark Theme mit #A0D8FA Akzentfarbe
- **Call-to-Action:** "Jetzt anmelden" → https://babixgo.de/auth/login
- **Sprache:** Deutsch

**Verwendung:**
```php
// In verify-email.php
$token = $_GET['token'] ?? '';
$db = Database::getInstance();
$userData = $db->fetchOne(
    "SELECT id, username, email FROM users WHERE verification_token = ? AND is_verified = 0",
    [$token]
);

if ($userData) {
    $user = new User();
    if ($user->verifyEmail($token)) {
        sendWelcomeEmail($userData['email'], $userData['username']);
        // Redirect to login
    }
}
```

**Template-Inhalt:**
- Bestätigung der erfolgreichen Verifizierung
- Hinweis auf verfügbare Funktionen
- "Danke für die Teilnahme an der Community"

**Beispiel:**
```php
$email = 'user@example.com';
$username = 'MaxMustermann';

if (sendWelcomeEmail($email, $username)) {
    echo "Willkommens-E-Mail versendet";
}
```

---

### 2. Files-System E-Mail-Modul

**Datei:** `/babixgo.de/files/includes/email.php`

---

#### sendEmail()

Sendet eine E-Mail mit mehrfachen Fallback-Mechanismen.

**Signatur:**
```php
function sendEmail(string $to, string $subject, string $body): bool
```

**Parameter:**
- `$to` (string, required): Empfänger-E-Mail-Adresse
- `$subject` (string, required): E-Mail-Betreff
- `$body` (string, required): E-Mail-Body (HTML)

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Erweiterte E-Mail-Versand-Funktion mit dreistufigem Fallback-Mechanismus:

1. **PHPMailer mit Brevo SMTP** (primär)
2. **Manueller SMTP-Socket** mit fsockopen (Fallback 1)
3. **PHP mail() Funktion** (Fallback 2)

**Fallback-Hierarchie:**
```
┌─────────────────────────┐
│   PHPMailer + Brevo     │
│   (STARTTLS, Port 587)  │
└───────────┬─────────────┘
            │ Exception?
            ▼
┌─────────────────────────┐
│ SMTP Socket (fsockopen) │
│   Manual TLS Handshake  │
└───────────┬─────────────┘
            │ Exception?
            ▼
┌─────────────────────────┐
│   PHP mail() Funktion   │
│   (letzter Ausweg)      │
└─────────────────────────┘
```

**Verwendete Konstanten:**
- `SMTP_HOST` - SMTP-Server
- `SMTP_PORT` - SMTP-Port
- `SMTP_USER` - SMTP Username
- `SMTP_PASS` - SMTP Passwort
- `SITE_NAME` - Absender-Name
- `SITE_URL` - Website-URL
- `DEBUG_MODE` - Debug-Modus aktivieren
- `MAIL_FROM_REGISTER` - Absender-E-Mail

**PHPMailer-Konfiguration:**
```php
$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->SMTPAuth = true;
$mail->Username = SMTP_USER;
$mail->Password = SMTP_PASS;
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = SMTP_PORT;
$mail->CharSet = 'UTF-8';
$mail->SMTPDebug = DEBUG_MODE ? 2 : 0;
```

**Debug-Output:**
```php
$mail->Debugoutput = function($str, $level) {
    if (DEBUG_MODE) {
        error_log("PHPMailer: $str");
    }
};
```

**Fehlerbehandlung:**
- Bei PHPMailer-Fehler: Automatischer Fallback zu SMTP-Socket
- Bei SMTP-Socket-Fehler: Automatischer Fallback zu mail()
- Alle Fehler werden geloggt (wenn DEBUG_MODE aktiv)

**Beispiel:**
```php
require_once __DIR__ . '/files/includes/email.php';

$to = 'user@example.com';
$subject = 'Test E-Mail';
$body = '<h1>Test</h1><p>Dies ist eine Test-E-Mail.</p>';

if (sendEmail($to, $subject, $body)) {
    echo "E-Mail erfolgreich versendet (über PHPMailer, SMTP-Socket oder mail())";
} else {
    echo "Alle E-Mail-Versand-Methoden sind fehlgeschlagen";
}
```

---

#### sendSmtpEmailFallback()

Manuelle SMTP-E-Mail-Versand-Funktion via Socket-Verbindung.

**Signatur:**
```php
function sendSmtpEmailFallback(string $to, string $subject, string $body): bool
```

**Parameter:**
- `$to` (string, required): Empfänger-E-Mail-Adresse
- `$subject` (string, required): E-Mail-Betreff
- `$body` (string, required): E-Mail-Body (HTML)

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Low-level SMTP-Implementierung für Fallback, wenn PHPMailer nicht verfügbar ist. Nutzt `fsockopen()` für direkte Socket-Verbindung zum SMTP-Server.

**SMTP-Protokoll-Ablauf:**
1. **Verbindung:** `fsockopen(SMTP_HOST, SMTP_PORT)`
2. **EHLO:** Server-Begrüßung
3. **STARTTLS:** TLS-Verschlüsselung aktivieren
4. **EHLO:** Erneute Begrüßung nach TLS
5. **AUTH LOGIN:** Base64-kodierte Authentifizierung
6. **MAIL FROM:** Absender setzen
7. **RCPT TO:** Empfänger setzen
8. **DATA:** E-Mail-Inhalt senden
9. **QUIT:** Verbindung beenden

**SMTP-Kommandos:**
```
C: EHLO babixgo.de
S: 250-smtp-relay.brevo.com
C: STARTTLS
S: 220 Ready to start TLS
C: EHLO babixgo.de
S: 250-smtp-relay.brevo.com
C: AUTH LOGIN
S: 334 VXNlcm5hbWU6
C: <base64-username>
S: 334 UGFzc3dvcmQ6
C: <base64-password>
S: 235 Authentication successful
C: MAIL FROM:<sender@example.com>
S: 250 OK
C: RCPT TO:<recipient@example.com>
S: 250 OK
C: DATA
S: 354 Start mail input
C: <email content>
C: .
S: 250 OK
C: QUIT
S: 221 Bye
```

**Timeouts:**
- Socket-Verbindung: 30 Sekunden
- Stream-Timeout: 30 Sekunden

**Fallback zu mail():**
Bei Socket-Fehler wird automatisch `@mail()` aufgerufen:
```php
catch (Exception $e) {
    error_log('SMTP Fallback Error: ' . $e->getMessage());
    return @mail($to, $subject, $body, implode("\r\n", $headers));
}
```

**Beispiel (wird intern von sendEmail() aufgerufen):**
```php
// Nur nutzen wenn PHPMailer nicht verfügbar
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $result = sendSmtpEmailFallback($to, $subject, $body);
}
```

---

#### readSmtpResponse()

Liest mehrzeilige SMTP-Server-Antworten.

**Signatur:**
```php
function readSmtpResponse($socket): string
```

**Parameter:**
- `$socket` (resource, required): Geöffnete Socket-Verbindung

**Rückgabewert:**
- `string` - Vollständige SMTP-Server-Antwort

**Beschreibung:**
Hilfsfunktion zum Lesen von SMTP-Responses, die über mehrere Zeilen gehen können. Liest bis zum letzten Zeichen mit Leerzeichen an Position 3.

**SMTP-Response-Format:**
```
250-smtp-relay.brevo.com
250-SIZE 31457280
250-PIPELINING
250 STARTTLS
```

**Implementierung:**
```php
function readSmtpResponse($socket): string {
    $response = '';
    while ($line = fgets($socket, 512)) {
        $response .= $line;
        // Letztes Zeile hat Leerzeichen an Position 3
        if (substr($line, 3, 1) === ' ') {
            break;
        }
    }
    return $response;
}
```

**Verwendung:**
```php
// Nach EHLO-Kommando
fwrite($socket, "EHLO " . SITE_URL . "\r\n");
$response = readSmtpResponse($socket);
// $response enthält komplette mehrzeilige Antwort
```

---

#### sendVerificationEmail()

Sendet Verifizierungs-E-Mail (Files-System-Version).

**Signatur:**
```php
function sendVerificationEmail(string $to, string $username, string $token): bool
```

**Parameter:**
- `$to` (string, required): Empfänger-E-Mail-Adresse
- `$username` (string, required): Benutzername
- `$token` (string, required): Verifizierungstoken

**Rückgabewert:**
- `bool` - `true` bei erfolgreichem Versand, `false` bei Fehler

**Beschreibung:**
Generiert und sendet Verifizierungs-E-Mail. Nutzt `getEmailTemplate()` für Template-Rendering.

**Verifizierungs-URL:**
```php
$verifyUrl = SITE_URL . '/verify.php?token=' . urlencode($token);
```

**Template-Variablen:**
- `username` - htmlspecialchars($username)
- `verify_url` - Vollständiger Link
- `site_name` - SITE_NAME Konstante
- `site_url` - SITE_URL Konstante

**Verwendung:**
```php
// In registerUser()
$verificationToken = bin2hex(random_bytes(32));
$emailSent = sendVerificationEmail($email, $username, $verificationToken);

if (!$emailSent) {
    return ['success' => true, 'message' => 'Registrierung erfolgreich, aber E-Mail konnte nicht gesendet werden'];
}
```

---

#### getEmailTemplate()

Lädt und rendert E-Mail-Templates mit Variablen.

**Signatur:**
```php
function getEmailTemplate(string $template, array $variables): string
```

**Parameter:**
- `$template` (string, required): Template-Name ('verification')
- `$variables` (array, required): Assoziatives Array mit Template-Variablen

**Rückgabewert:**
- `string` - Gerendertes HTML-Template

**Beschreibung:**
Template-Engine für E-Mail-Templates. Ersetzt `{{variable}}` Platzhalter mit tatsächlichen Werten.

**Verfügbare Templates:**
- `verification` - E-Mail-Verifizierungs-Template

**Template-Syntax:**
```html
<h1>Willkommen bei {{site_name}}!</h1>
<p>Hallo {{username}},</p>
<a href="{{verify_url}}">E-Mail bestätigen</a>
```

**Variablen-Ersetzung:**
```php
foreach ($variables as $key => $value) {
    $html = str_replace('{{' . $key . '}}', $value, $html);
}
```

**Verwendung:**
```php
$template = getEmailTemplate('verification', [
    'username' => htmlspecialchars($username),
    'verify_url' => $verifyUrl,
    'site_name' => SITE_NAME,
    'site_url' => SITE_URL
]);

sendEmail($to, $subject, $template);
```

**Template-Design:**
- Dark Theme (#141418 Hintergrund)
- Container: #2a2e32
- Primärfarbe: #A0D8FA
- Responsive Layout (max-width: 600px)
- Inline CSS für maximale Kompatibilität

**Beispiel:**
```php
$html = getEmailTemplate('verification', [
    'username' => 'MaxMustermann',
    'verify_url' => 'https://babixgo.de/verify?token=abc123',
    'site_name' => 'BabixGO',
    'site_url' => 'https://babixgo.de'
]);

echo $html; // Vollständiges HTML-E-Mail
```

---

## User-Klassen-Methoden mit E-Mail-Funktionalität

### User::register()

Registriert einen neuen Benutzer und generiert Verifizierungstoken.

**Signatur:**
```php
public function register(string $username, string $email, string $password): array
```

**Parameter:**
- `$username` (string, required): Benutzername (3-50 Zeichen)
- `$email` (string, required): E-Mail-Adresse
- `$password` (string, required): Klartext-Passwort (wird gehasht)

**Rückgabewert:**
```php
[
    'success' => bool,
    'user_id' => int|null,
    'verification_token' => string|null,
    'error' => string|null
]
```

**Beschreibung:**
Erstellt neuen Benutzer-Account mit `is_verified = 0` und generiert 64-Zeichen Hex-Token.

**Token-Generierung:**
```php
$verificationToken = bin2hex(random_bytes(32)); // 64 Zeichen Hex
```

**Verwendung:**
```php
$user = new User();
$result = $user->register('MaxMustermann', 'max@example.com', 'SecurePass123');

if ($result['success']) {
    sendVerificationEmail($email, $username, $result['verification_token']);
    echo "Registrierung erfolgreich! Bitte E-Mail bestätigen.";
} else {
    echo "Fehler: " . $result['error'];
}
```

---

### User::verifyEmail() / verifyEmail()

Verifiziert E-Mail-Adresse mit Token.

**Signatur:**
```php
// In files/includes/auth.php
function verifyEmail(string $token): bool
```

**Parameter:**
- `$token` (string, required): 64-Zeichen Verifizierungstoken

**Rückgabewert:**
- `bool` - `true` bei erfolgreicher Verifizierung, `false` bei Fehler

**Beschreibung:**
Prüft Token gegen Datenbank und setzt `email_verified = 1`.

**SQL-Ablauf:**
```sql
-- 1. Token prüfen
SELECT id FROM users WHERE verification_token = ? AND email_verified = 0

-- 2. Verifizieren
UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?
```

**Verwendung:**
```php
$token = $_GET['token'] ?? '';

if (verifyEmail($token)) {
    // E-Mail erfolgreich verifiziert
    $userData = fetchOne("SELECT email, username FROM users WHERE verification_token IS NULL AND email_verified = 1");
    sendWelcomeEmail($userData['email'], $userData['username']);
    header('Location: /auth/login?message=Email verified successfully');
} else {
    header('Location: /auth/login?message=Invalid or expired token&type=error');
}
```

---

## Konstanten

### SMTP-Konfiguration

```php
// Auth-System
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? getenv('SMTP_USER'));
define('SMTP_PASS', $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY'));
define('MAIL_FROM', 'register@babixgo-mail.de');

// Files-System
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_KEY') ?: '');
define('MAIL_FROM_REGISTER', 'register@babixgo-mail.de');
```

### Website-Konfiguration

```php
define('SITE_NAME', 'BabixGO');
define('SITE_URL', 'https://babixgo.de');
// oder für Files-System:
define('SITE_URL', getenv('SITE_URL') ?: 'https://files.babixgo.de');
```

### Debug-Modus

```php
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');
```

---

## E-Mail-Typen in Datenbank

```php
ENUM('verification', 'password_reset', 'welcome', 'notification', 'custom')
```

**Verwendung in email_logs:**
- `verification` - E-Mail-Verifizierung
- `password_reset` - Passwort-Wiederherstellung
- `welcome` - Willkommens-E-Mail
- `notification` - System-Benachrichtigungen (noch nicht implementiert)
- `custom` - Benutzerdefinierte E-Mails

---

## Error-Codes und Exceptions

### PHPMailer Exceptions

```php
use PHPMailer\PHPMailer\Exception;

try {
    $mail->send();
} catch (Exception $e) {
    // $e->getMessage() - Fehlerbeschreibung
    // $mail->ErrorInfo - SMTP-spezifische Fehlerinfo
}
```

**Häufige Fehler:**
- `SMTP Error: Could not authenticate` - Falsche Credentials
- `SMTP connect() failed` - Verbindungsfehler
- `Invalid address` - Ungültige E-Mail-Adresse
- `Could not instantiate mail function` - mail() nicht verfügbar

### SMTP Response Codes

| Code | Bedeutung |
|------|-----------|
| 220 | Service ready |
| 235 | Authentication successful |
| 250 | Requested action completed |
| 354 | Start mail input |
| 421 | Service not available |
| 450 | Mailbox unavailable |
| 550 | Mailbox unavailable / Rate limit |
| 554 | Transaction failed |

---

## Code-Beispiele

### Vollständiger Registrierungs-Flow

```php
<?php
require_once __DIR__ . '/shared/config/database.php';
require_once __DIR__ . '/shared/config/autoload.php';
require_once __DIR__ . '/auth/includes/mail-helper.php';

// 1. Formular-Daten validieren
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];

// 2. Benutzer registrieren
$user = new User();
$result = $user->register($username, $email, $password);

if (!$result['success']) {
    die(json_encode(['error' => $result['error']]));
}

// 3. Verifizierungs-E-Mail senden
$emailSent = sendVerificationEmail(
    $email,
    $username,
    $result['verification_token']
);

// 4. Response
if ($emailSent) {
    echo json_encode([
        'success' => true,
        'message' => 'Registrierung erfolgreich! Bitte E-Mail bestätigen.'
    ]);
} else {
    echo json_encode([
        'success' => true,
        'message' => 'Registrierung erfolgreich, aber E-Mail konnte nicht gesendet werden.',
        'warning' => true
    ]);
}
```

### Custom E-Mail mit Template

```php
<?php
require_once __DIR__ . '/files/includes/email.php';

// Custom Template definieren
function getCustomTemplate(array $variables): string {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>{{title}}</h1>
            <p>{{message}}</p>
            <p>Mit freundlichen Grüßen,<br>{{site_name}}</p>
        </div>
    </body>
    </html>';
    
    foreach ($variables as $key => $value) {
        $html = str_replace('{{' . $key . '}}', $value, $html);
    }
    
    return $html;
}

// E-Mail senden
$template = getCustomTemplate([
    'title' => 'Wichtige Mitteilung',
    'message' => 'Hallo, dies ist eine Test-Nachricht.',
    'site_name' => 'BabixGO'
]);

sendEmail('user@example.com', 'Wichtige Mitteilung', $template);
```

### Test-E-Mail-Versand-Script

```php
<?php
/**
 * test-email.php - E-Mail-Versand testen
 * ACHTUNG: Nach Test löschen oder in /tmp/ verschieben!
 */

require_once __DIR__ . '/babixgo.de/files/includes/email.php';

// Test-Parameter
$testEmail = 'your-email@example.com';
$testUsername = 'TestUser';
$testToken = bin2hex(random_bytes(32));

// Test 1: PHPMailer verfügbar?
echo "Test 1: PHPMailer verfügbar? ";
echo class_exists('PHPMailer\PHPMailer\PHPMailer') ? "✓ Ja\n" : "✗ Nein\n";

// Test 2: SMTP-Credentials gesetzt?
echo "\nTest 2: SMTP-Konfiguration:\n";
echo "  SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : '✗ Nicht definiert') . "\n";
echo "  SMTP_PORT: " . (defined('SMTP_PORT') ? SMTP_PORT : '✗ Nicht definiert') . "\n";
echo "  SMTP_USER: " . (defined('SMTP_USER') && SMTP_USER ? '✓ Gesetzt' : '✗ Nicht gesetzt') . "\n";
echo "  SMTP_PASS: " . (defined('SMTP_PASS') && SMTP_PASS ? '✓ Gesetzt' : '✗ Nicht gesetzt') . "\n";

// Test 3: Verifizierungs-E-Mail senden
echo "\nTest 3: Verifizierungs-E-Mail senden...\n";
$result = sendVerificationEmail($testEmail, $testUsername, $testToken);
echo $result ? "✓ E-Mail erfolgreich versendet!\n" : "✗ E-Mail-Versand fehlgeschlagen!\n";

// Test 4: Simple E-Mail
echo "\nTest 4: Simple Test-E-Mail...\n";
$simpleResult = sendEmail($testEmail, 'BabixGO Test', '<h1>Test erfolgreich!</h1>');
echo $simpleResult ? "✓ Erfolgreich\n" : "✗ Fehlgeschlagen\n";

echo "\n✓ Tests abgeschlossen. Bitte Test-E-Mails in " . $testEmail . " prüfen.\n";
```

---

## Best Practices

### 1. Fehlerbehandlung

```php
// IMMER try-catch nutzen
try {
    $result = sendVerificationEmail($email, $username, $token);
    if (!$result) {
        // Loggen, aber Benutzer nicht blockieren
        error_log("E-Mail-Versand fehlgeschlagen für: $email");
    }
} catch (Exception $e) {
    error_log("E-Mail Exception: " . $e->getMessage());
}
```

### 2. Input-Validierung

```php
// E-Mail validieren
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Exception('Ungültige E-Mail-Adresse');
}

// HTML-Escaping
$safeUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
```

### 3. Token-Sicherheit

```php
// Kryptographisch sicherer Token
$token = bin2hex(random_bytes(32)); // 64 Zeichen

// NIEMALS md5() oder sha1() für Tokens nutzen!
```

### 4. Rate-Limiting

```php
// E-Mail-Versand limitieren (empfohlen)
$lastEmail = $_SESSION['last_email_sent'] ?? 0;
if (time() - $lastEmail < 60) {
    throw new Exception('Bitte warte 1 Minute vor erneutem E-Mail-Versand');
}
$_SESSION['last_email_sent'] = time();
```

---

**Dokumentation erstellt:** 2026-01-17  
**Version:** 1.0  
**Autor:** BabixGO Development Team
