# E-Mail-System Dokumentation

## Übersicht

Das BabixGO E-Mail-System ist für den Versand von transaktionalen E-Mails verantwortlich, einschließlich Benutzerregistrierung, E-Mail-Verifizierung und Passwort-Wiederherstellung. Das System nutzt **PHPMailer** in Kombination mit dem **Brevo SMTP-Service** (ehemals Sendinblue).

### Kernfunktionen
- ✅ Benutzer-Verifizierungs-E-Mails
- ✅ Passwort-Reset-E-Mails
- ✅ Willkommens-E-Mails nach erfolgreicher Verifizierung
- ✅ SMTP-Authentifizierung über Brevo
- ✅ Mehrfache Fallback-Mechanismen
- ✅ HTML-E-Mail-Templates mit responsivem Design
- ✅ E-Mail-Logging in Datenbank

### Systemarchitektur

```
┌─────────────────────────────────────────────────────────────┐
│                    BabixGO Anwendung                         │
├─────────────────────────────────────────────────────────────┤
│  Auth-System              │         Files-System             │
│  /auth/includes/          │         /files/includes/         │
│  mail-helper.php          │         email.php                │
└──────────┬────────────────┴──────────────┬──────────────────┘
           │                               │
           └───────────┬───────────────────┘
                       │
           ┌───────────▼──────────────┐
           │      PHPMailer           │
           │   (^7.0 via Composer)    │
           └───────────┬──────────────┘
                       │
           ┌───────────▼──────────────┐
           │    Brevo SMTP Relay      │
           │  smtp-relay.brevo.com    │
           │        Port: 587         │
           │    TLS Verschlüsselung   │
           └──────────────────────────┘
```

## Komponenten

### 1. E-Mail-Versand-Module

#### 1.1 Auth-System E-Mail-Modul
**Datei:** `/babixgo.de/auth/includes/mail-helper.php`

**Verwendungszweck:**
- Hauptsächlich für Authentifizierungs-bezogene E-Mails
- Genutzt von: `/auth/register.php`, `/auth/forgot-password.php`, `/auth/verify-email.php`

**Funktionen:**
- `sendEmail($to, $subject, $message, $headers = [])`
- `sendVerificationEmail($email, $username, $token)`
- `sendPasswordResetEmail($email, $username, $token)`
- `sendWelcomeEmail($email, $username)`

**Konfiguration:**
```php
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? getenv('SMTP_USER'));
define('SMTP_PASS', $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY'));
define('MAIL_FROM', 'register@babixgo-mail.de');
define('SITE_NAME', 'BabixGO');
```

#### 1.2 Files-System E-Mail-Modul
**Datei:** `/babixgo.de/files/includes/email.php`

**Verwendungszweck:**
- E-Mail-Versand für Files-Subdomain
- Erweiterte Fallback-Mechanismen

**Besonderheiten:**
- **Fallback 1:** PHPMailer mit Brevo SMTP
- **Fallback 2:** Manueller SMTP-Socket-Verbindung (fsockopen)
- **Fallback 3:** PHP `mail()` Funktion

**Funktionen:**
- `sendEmail(string $to, string $subject, string $body): bool`
- `sendSmtpEmailFallback(string $to, string $subject, string $body): bool`
- `readSmtpResponse($socket): string`
- `sendVerificationEmail(string $to, string $username, string $token): bool`
- `getEmailTemplate(string $template, array $variables): string`

### 2. Konfigurationsdateien

#### 2.1 Environment-Variablen
**Datei:** `/babixgo.de/files/includes/config.php`

```php
// SMTP Configuration (Brevo)
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_KEY') ?: '');

// Email sender addresses
define('MAIL_FROM_REGISTER', 'register@babixgo-mail.de');

// Debug Mode
define('DEBUG_MODE', getenv('DEBUG_MODE') === 'true' || getenv('DEBUG_MODE') === '1');
```

#### 2.2 Erforderliche Environment-Variablen

**In `.env` oder Server-Konfiguration:**
```bash
# Brevo SMTP Credentials
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=<your-brevo-account-email>
SMTP_KEY=<your-brevo-smtp-api-key>

# Optional: Debug-Modus
DEBUG_MODE=false
```

**GitHub Secrets (für CI/CD):**
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_USER`
- `SMTP_KEY`
- `SMTP_SENDER_REGISTRATION`

### 3. E-Mail-Templates

Das System verwendet **inline HTML-Templates** (keine separaten Template-Dateien). Templates sind direkt in den Funktionen kodiert.

#### 3.1 Verification E-Mail Template

**Template-Name:** `verification`  
**Funktion:** `sendVerificationEmail()`  
**Auslöser:** Nach erfolgreicher Benutzer-Registrierung

**Variablen:**
- `{{username}}` - Benutzername des neuen Users
- `{{verify_url}}` - Vollständiger Verifizierungs-Link
- `{{site_name}}` - "BabixGO"
- `{{site_url}}` - "https://babixgo.de"

**Design:**
- Dark Theme (#141418 Hintergrund, #2a2e32 Container)
- Primärfarbe: #A0D8FA (Hellblau)
- Call-to-Action Button: "E-Mail bestätigen"
- Gültigkeitsdauer: 24 Stunden (im Text erwähnt)
- Responsives Design

**Verwendungsstellen:**
- `/auth/includes/form-handlers/register-handler.php` (Zeile 86)
- `/admin/includes/form-handlers/register-handler.php`
- `/files/includes/auth.php` (Zeile 209)

#### 3.2 Password Reset E-Mail Template

**Template-Name:** `password_reset`  
**Funktion:** `sendPasswordResetEmail()`  
**Auslöser:** Benutzer fordert Passwort-Reset an

**Variablen:**
- `{{username}}` - Benutzername
- `{{reset_url}}` - Vollständiger Reset-Link
- Link-Gültigkeit: 1 Stunde

**Design:**
- Dark Theme
- Warnung-Button: #e74c3c (Rot)
- Call-to-Action: "Passwort zurücksetzen"
- Sicherheitshinweis: "Falls du kein neues Passwort angefordert hast, ignoriere diese E-Mail"

**Verwendungsstellen:**
- `/auth/forgot-password.php` (Zeile 29)

#### 3.3 Welcome E-Mail Template

**Template-Name:** `welcome`  
**Funktion:** `sendWelcomeEmail()`  
**Auslöser:** Nach erfolgreicher E-Mail-Verifizierung

**Variablen:**
- `{{username}}` - Benutzername

**Design:**
- Dark Theme
- Call-to-Action: "Jetzt anmelden" → https://babixgo.de/auth/login
- Willkommensnachricht

**Verwendungsstellen:**
- `/auth/verify-email.php` (Zeile 38)

### 4. Datenbank-Integration

#### 4.1 E-Mail-Logs Tabelle

**Tabelle:** `email_logs`  
**Zweck:** Logging aller versendeten E-Mails für Audit und Debugging

**Schema:**
```sql
CREATE TABLE IF NOT EXISTS email_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    email_type ENUM('verification', 'password_reset', 'welcome', 'notification', 'custom') NOT NULL,
    success BOOLEAN DEFAULT 0,
    error_message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_recipient (recipient),
    INDEX idx_email_type (email_type),
    INDEX idx_sent_at (sent_at),
    INDEX idx_success (success)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Gespeicherte Informationen:**
- User-ID (wenn verfügbar)
- Empfänger-E-Mail-Adresse
- E-Mail-Betreff
- E-Mail-Typ (verification, password_reset, welcome, etc.)
- Erfolgsstatus (Boolean)
- Fehlermeldung (bei Fehler)
- Versandzeitpunkt

**Hinweis:** Aktuell wird die `email_logs` Tabelle in der Datenbank definiert, aber die aktiven E-Mail-Funktionen loggen noch nicht automatisch in diese Tabelle. Dies könnte in Zukunft implementiert werden.

#### 4.2 User-Tabelle E-Mail-relevante Felder

```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(64) NULL,
    reset_token VARCHAR(64) NULL,
    reset_token_expires DATETIME NULL,
    email_verified BOOLEAN DEFAULT 0,
    -- ... weitere Felder
);
```

**E-Mail-relevante Felder:**
- `email` - Benutzer-E-Mail-Adresse (UNIQUE)
- `is_verified` / `email_verified` - E-Mail-Verifizierungsstatus (beide Felder aus Kompatibilitätsgründen)
- `verification_token` - Token für E-Mail-Verifizierung (64 Zeichen Hex)
- `reset_token` - Token für Passwort-Reset (64 Zeichen Hex)
- `reset_token_expires` - Ablaufdatum des Reset-Tokens (1 Stunde)

### 5. SMTP-Provider Details

#### 5.1 Brevo (ehemals Sendinblue)

**Provider-Informationen:**
- **Name:** Brevo (Rebranding von Sendinblue 2023)
- **Website:** https://www.brevo.com
- **SMTP Endpoint:** `smtp-relay.brevo.com`
- **Port:** 587
- **Verschlüsselung:** TLS (STARTTLS)
- **Authentifizierung:** SMTP-API-Key

**Free Tier Limits:**
- **E-Mails pro Tag:** 300
- **Rate-Limiting:** Im Code nicht explizit implementiert (Brevo-seitig gehandhabt)
- **Hinweis:** Bei Überschreitung werden E-Mails verzögert oder abgelehnt

**API-Key Verwaltung:**
1. Brevo-Account erstellen
2. Settings → SMTP & API
3. SMTP Key generieren
4. Key als `SMTP_KEY` Environment-Variable speichern

**Sender-Adressen:**
- `register@babixgo-mail.de` - Für Registrierungs-E-Mails
- `noreply@babixgo.de` - Fallback-Sender

**Wichtig:** Sender-Adressen müssen in Brevo verifiziert sein!

### 6. E-Mail-Verwendung im System

#### 6.1 Registrierungsprozess

**Datei:** `/auth/includes/form-handlers/register-handler.php`

```php
// Zeile 80-91
$user = new User();
$result = $user->register($username, $email, $password);

if ($result['success']) {
    // Send verification email
    sendVerificationEmail($email, $username, $result['verification_token']);
    
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Please check your email...'
    ]);
}
```

**Flow:**
1. User füllt Registrierungsformular aus
2. `User->register()` erstellt Account mit `is_verified = 0`
3. Generiert 64-Zeichen Hex `verification_token`
4. `sendVerificationEmail()` sendet E-Mail mit Token-Link
5. User klickt Link → `/auth/verify-email.php?token=...`

**Fehlerbehandlung:**
- Try-catch Block vorhanden (Zeile 80-98)
- Bei E-Mail-Fehler: Registrierung erfolgreich, aber Warnung
- Bei Duplikaten: Fehler-Response mit spezifischer Meldung

#### 6.2 E-Mail-Verifizierung

**Datei:** `/auth/verify-email.php`

```php
// Zeile 27-36
$userData = $db->fetchOne($sql, [$token]);

if (!$userData) {
    header('Location: /auth/login?message=Invalid or expired...');
    exit;
}

if ($user->verifyEmail($token)) {
    sendWelcomeEmail($userData['email'], $userData['username']);
    header('Location: /auth/login?message=Email verified successfully!');
}
```

**Flow:**
1. User klickt Verifizierungs-Link aus E-Mail
2. System prüft Token gegen Datenbank
3. Bei gültigem Token: `is_verified = 1`, `verification_token = NULL`
4. `sendWelcomeEmail()` sendet Bestätigungs-E-Mail
5. Redirect zu Login mit Success-Message

#### 6.3 Passwort-Wiederherstellung

**Datei:** `/auth/forgot-password.php`

```php
// Zeile 18-30
$result = $user->requestPasswordReset($email);

if ($result['success']) {
    $userData = $db->fetchOne($sql, [$result['user_id']]);
    sendPasswordResetEmail($userData['email'], $userData['username'], $result['reset_token']);
    
    $message = 'Password reset instructions have been sent to your email.';
}
```

**Flow:**
1. User gibt E-Mail-Adresse ein
2. System generiert `reset_token` und `reset_token_expires` (1 Stunde)
3. `sendPasswordResetEmail()` sendet E-Mail mit Reset-Link
4. Security: Auch bei nicht-existierender E-Mail erfolgt neutrale Meldung
5. User klickt Link → `/auth/reset-password.php?token=...`

**Token-Validierung:**
- Token muss gültig sein
- Token darf nicht abgelaufen sein (< 1 Stunde alt)
- Nach erfolgreicher Nutzung wird Token gelöscht

### 7. Error-Handling & Debugging

#### 7.1 Fehlerbehandlung

**PHPMailer Exceptions:**
```php
try {
    $mail = new PHPMailer(true);
    // ... SMTP config
    $mail->send();
    return true;
} catch (Exception $e) {
    if (DEBUG_MODE) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
    }
    // Fallback to SMTP socket or mail()
    return sendSmtpEmailFallback($to, $subject, $body);
}
```

**Fallback-Hierarchie:**
1. **PHPMailer mit Brevo SMTP** (Standard)
2. **Manueller SMTP-Socket** (`fsockopen`, STARTTLS)
3. **PHP mail()** Funktion (letzter Ausweg)

**Häufige Fehler:**

| Fehler | Ursache | Lösung |
|--------|---------|--------|
| SMTP Authentication Failed | Falscher SMTP_USER oder SMTP_KEY | Credentials in `.env` prüfen |
| Could not connect to SMTP server | Port blockiert oder falscher Host | Firewall prüfen, Port 587 freigeben |
| Rate Limit Exceeded | > 300 E-Mails/Tag (Brevo Free) | Upgrade auf Brevo Paid Plan |
| Invalid sender address | Sender-Adresse nicht verifiziert | Sender in Brevo-Dashboard verifizieren |
| PHPMailer not found | Composer Dependencies fehlen | `composer install` ausführen |

#### 7.2 Debug-Modus

**Aktivierung:**
```bash
# In .env
DEBUG_MODE=true
```

**Debug-Output:**
```php
// PHPMailer Debug Level
$mail->SMTPDebug = DEBUG_MODE ? 2 : 0;

// Custom Debug Output Handler
$mail->Debugoutput = function($str, $level) {
    if (DEBUG_MODE) {
        error_log("PHPMailer: $str");
    }
};
```

**Debug-Levels:**
- `0` - Kein Debug-Output
- `1` - Client-Nachrichten
- `2` - Client und Server-Nachrichten (empfohlen)
- `3` - Wie 2, plus Verbindungsstatus
- `4` - Low-level Daten

**Logs finden:**
- PHP Error Log: `/var/log/php/error.log` (Server-abhängig)
- Apache Error Log: `/var/log/apache2/error.log`
- Strato: Logs im Webspace unter `/logs/` oder via FTP

### 8. Security & Compliance

#### 8.1 DSGVO-Compliance

**Aktuelle Implementierung:**
- ✅ E-Mail-Adressen nur mit Zustimmung (Opt-in bei Registrierung)
- ✅ E-Mail-Verifizierung vor Account-Aktivierung
- ❌ **Fehlend:** Datenschutzerklärung-Link in E-Mails
- ❌ **Fehlend:** Abmeldelink (aktuell nicht nötig da nur transaktionale E-Mails)
- ⚠️ **Unklar:** Speicherdauer von `email_logs` nicht definiert

**Empfehlungen:**
1. Datenschutzlink in Footer jeder E-Mail hinzufügen
2. Retention-Policy für `email_logs` definieren (z.B. 90 Tage)
3. Automatisches Löschen alter Logs via Cron-Job

#### 8.2 Security-Maßnahmen

**E-Mail-Authentifizierung:**
- ✅ **SPF-Record:** Sollte für `babixgo.de` und `babixgo-mail.de` konfiguriert sein
- ✅ **DKIM:** Brevo signiert E-Mails automatisch mit DKIM
- ⚠️ **DMARC:** Empfohlen für vollständigen Schutz

**Eingabe-Validierung:**
```php
// E-Mail-Validierung
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return ['success' => false, 'error' => 'Invalid email format'];
}

// XSS-Schutz in Templates
htmlspecialchars($username)
```

**Token-Sicherheit:**
- Tokens generiert mit `bin2hex(random_bytes(32))` (64 Zeichen)
- Kryptographisch sichere Zufallszahlen
- Tokens werden nach Verwendung gelöscht
- Reset-Tokens haben Ablaufdatum (1 Stunde)

**Rate-Limiting:**
```php
// Login Rate Limiting
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
```

**Hinweis:** E-Mail-Versand-Rate-Limiting wird aktuell nur durch Brevo selbst gehandhabt (300/Tag).

**Transport-Sicherheit:**
- ✅ TLS-Verschlüsselung (STARTTLS)
- ✅ Sichere Session-Cookies (httponly, secure, samesite)
- ✅ Password-Hashing mit bcrypt (cost=12)

#### 8.3 Sicherheits-Checkliste

- [x] SPF-Record für Sender-Domain konfiguriert
- [x] DKIM-Signatur durch Brevo aktiviert
- [ ] DMARC-Policy für Domain setzen
- [x] E-Mail-Adressen validiert (filter_var)
- [ ] E-Mail-Versand Rate-Limiting implementieren
- [x] XSS-Schutz in Templates (htmlspecialchars)
- [x] CSRF-Schutz bei Formularen
- [ ] E-Mail-Log Retention Policy definieren
- [ ] Datenschutzlink in E-Mail-Templates

### 9. Testing & Qualitätssicherung

#### 9.1 Test-Dateien

**Aktueller Stand:** Keine dedizierten E-Mail-Unit-Tests gefunden.

**Empfehlung:** Tests erstellen für:
```
tests/
  ├── EmailTest.php
  │   ├── testSendVerificationEmail()
  │   ├── testSendPasswordResetEmail()
  │   ├── testSendWelcomeEmail()
  │   ├── testEmailTemplateRendering()
  │   └── testSMTPFallback()
  └── fixtures/
      └── test-email-templates.html
```

#### 9.2 Manuelle Test-Prozedur

**Test-E-Mail versenden:**

```php
<?php
// test-email.php (in /tmp/ erstellen, nicht committen)
require_once __DIR__ . '/babixgo.de/files/includes/email.php';

$testEmail = 'your-email@example.com';
$result = sendVerificationEmail($testEmail, 'TestUser', 'test-token-123');

echo $result ? 'E-Mail erfolgreich versendet!' : 'E-Mail-Versand fehlgeschlagen!';
```

**Test-Szenarien:**
1. ✅ Registrierungs-E-Mail mit gültigem Token
2. ✅ Passwort-Reset-E-Mail
3. ✅ Willkommens-E-Mail
4. ✅ Fallback-Test (PHPMailer deaktivieren)
5. ✅ Ungültige E-Mail-Adresse
6. ✅ Rate-Limit-Test (> 300 E-Mails)

**Test-E-Mail-Adressen:**
- Mailinator: `test@mailinator.com`
- Temp-Mail: `test@temp-mail.org`
- Eigene Test-Adressen

#### 9.3 Sandbox/Test-Modus

**Aktueller Stand:** Kein dedizierter Test-Modus implementiert.

**Empfehlung:**
```php
// In config.php
define('EMAIL_TEST_MODE', getenv('EMAIL_TEST_MODE') === 'true');
define('EMAIL_TEST_RECIPIENT', 'dev@babixgo.de');

// In sendEmail()
if (EMAIL_TEST_MODE) {
    $to = EMAIL_TEST_RECIPIENT;
    $subject = '[TEST] ' . $subject;
}
```

### 10. Deployment & Konfiguration

#### 10.1 Lokale Entwicklung

**1. Environment-Datei erstellen:**
```bash
cd /path/to/babixgo
cp .env.example .env
```

**2. SMTP-Credentials eintragen:**
```bash
# .env
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=your-brevo-email@example.com
SMTP_KEY=your-brevo-smtp-api-key
DEBUG_MODE=true
```

**3. PHPMailer installieren:**
```bash
cd babixgo.de/files
composer install
```

**4. Test-E-Mail versenden:**
```bash
php test-email.php
```

#### 10.2 Strato-Server Deployment

**1. Environment-Variablen setzen:**

Strato unterstützt keine `.env` Dateien direkt. Optionen:

**Option A: PHP-Konfigurationsdatei**
```php
// babixgo.de/files/includes/config.local.php
<?php
putenv('SMTP_HOST=smtp-relay.brevo.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=your-email@example.com');
putenv('SMTP_KEY=your-api-key');

// In config.php:
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
```

**Option B: .htaccess (falls möglich)**
```apache
SetEnv SMTP_HOST "smtp-relay.brevo.com"
SetEnv SMTP_PORT "587"
SetEnv SMTP_USER "your-email@example.com"
SetEnv SMTP_KEY "your-api-key"
```

**2. Dateiberechtigungen setzen:**
```bash
chmod 640 config.local.php
chown www-data:www-data config.local.php
```

**3. Test-E-Mail versenden:**
Über `/files/test-email.php` aufrufen (dann löschen!)

#### 10.3 GitHub Actions CI/CD

**GitHub Secrets konfigurieren:**
1. Repository → Settings → Secrets and variables → Actions
2. New repository secret:
   - `SMTP_HOST` = smtp-relay.brevo.com
   - `SMTP_PORT` = 587
   - `SMTP_USER` = <Brevo Account E-Mail>
   - `SMTP_KEY` = <Brevo SMTP API Key>
   - `SMTP_SENDER_REGISTRATION` = register@babixgo-mail.de

**In Workflow nutzen:**
```yaml
# .github/workflows/deploy.yml
env:
  SMTP_HOST: ${{ secrets.SMTP_HOST }}
  SMTP_PORT: ${{ secrets.SMTP_PORT }}
  SMTP_USER: ${{ secrets.SMTP_USER }}
  SMTP_KEY: ${{ secrets.SMTP_KEY }}
```

### 11. Troubleshooting

#### 11.1 SMTP Connection Failed

**Symptome:**
```
Could not connect to SMTP server: Connection refused (111)
```

**Lösungen:**
1. **Firewall:** Port 587 ausgehend freigeben
2. **Host:** `smtp-relay.brevo.com` korrekt?
3. **Netzwerk:** Server hat Internet-Zugang?
4. **Alternative:** Port 465 mit SSL versuchen

#### 11.2 Authentication Failed

**Symptome:**
```
SMTP Error: Could not authenticate
```

**Lösungen:**
1. **Credentials:** SMTP_USER und SMTP_KEY in `.env` prüfen
2. **API-Key:** Neuen SMTP-Key in Brevo generieren
3. **Account:** Brevo-Account aktiv und verifiziert?
4. **Logs:** `DEBUG_MODE=true` setzen und Logs prüfen

#### 11.3 Rate Limit Exceeded

**Symptome:**
```
550 5.7.1 Daily sending quota exceeded
```

**Lösungen:**
1. **Free Tier:** Auf Paid Plan upgraden
2. **Queue:** E-Mail-Versand über Job-Queue verteilen
3. **Limit:** Tägliche E-Mails auf < 300 beschränken
4. **Monitoring:** E-Mail-Counter implementieren

#### 11.4 Emails landen im Spam

**Lösungen:**
1. **SPF:** SPF-Record für Domain korrekt?
   ```
   v=spf1 include:spf.brevo.com ~all
   ```
2. **DKIM:** Brevo DKIM-Signatur aktiviert?
3. **DMARC:** DMARC-Policy setzen
   ```
   v=DMARC1; p=none; rua=mailto:postmaster@babixgo.de
   ```
4. **Inhalt:** Spam-Wörter vermeiden
5. **Sender:** Verifizierte Sender-Adresse nutzen

#### 11.5 PHPMailer not found

**Symptome:**
```
Class 'PHPMailer\PHPMailer\PHPMailer' not found
```

**Lösungen:**
```bash
cd babixgo.de/files
composer require phpmailer/phpmailer
# oder
composer install
```

### 12. Changelog

| Version | Datum | Änderungen |
|---------|-------|------------|
| 1.0 | 2026-01-17 | Initiale Dokumentation erstellt |

### 13. Weitere Ressourcen

**Brevo Dokumentation:**
- SMTP Setup: https://help.brevo.com/hc/en-us/articles/209467485
- API Keys: https://app.brevo.com/settings/keys/smtp

**PHPMailer:**
- GitHub: https://github.com/PHPMailer/PHPMailer
- Dokumentation: https://github.com/PHPMailer/PHPMailer/wiki

**E-Mail-Testing:**
- Mailtrap: https://mailtrap.io
- Mailinator: https://www.mailinator.com
- Mail-Tester: https://www.mail-tester.com

---

**Dokumentation erstellt:** 2026-01-17  
**Autor:** BabixGO Development Team  
**Version:** 1.0
