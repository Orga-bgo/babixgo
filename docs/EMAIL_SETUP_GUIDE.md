# E-Mail-Setup-Anleitung

## Inhaltsverzeichnis

1. [Brevo-Account erstellen](#1-brevo-account-erstellen)
2. [SMTP-Konfiguration](#2-smtp-konfiguration)
3. [Lokale Entwicklungsumgebung](#3-lokale-entwicklungsumgebung)
4. [Produktions-Deployment](#4-produktions-deployment)
5. [Testing](#5-testing)
6. [Troubleshooting](#6-troubleshooting)

---

## 1. Brevo-Account erstellen

### 1.1 Registrierung

1. **Website besuchen:** https://www.brevo.com
2. **Auf "Sign Up" klicken** (oben rechts)
3. **Account-Daten eingeben:**
   - Vorname & Nachname
   - E-Mail-Adresse
   - Passwort (mind. 8 Zeichen)
   - Land: Germany
4. **E-Mail bestätigen:** Link in Bestätigungs-E-Mail klicken
5. **Onboarding abschließen:**
   - Firmenname: "BabixGO" (oder eigener Name)
   - Website: https://babixgo.de
   - Branche auswählen
   - Ziele auswählen (z.B. "Transactional emails")

### 1.2 SMTP aktivieren

1. **Dashboard öffnen:** Nach Login auf https://app.brevo.com
2. **Settings → SMTP & API:** Im Menü links navigieren
3. **SMTP Tab auswählen**
4. **SMTP-Credentials notieren:**
   ```
   Server: smtp-relay.brevo.com
   Port: 587
   Login: <Ihre Brevo E-Mail-Adresse>
   ```

### 1.3 SMTP-API-Key generieren

1. **"Generate a new SMTP key" klicken**
2. **Name vergeben:** z.B. "BabixGO Production" oder "BabixGO Development"
3. **Key kopieren und sicher speichern!**
   ```
   xsmtpsib-abc123def456...
   ```
   ⚠️ **WICHTIG:** Key wird nur einmal angezeigt!

### 1.4 Sender-Adressen verifizieren

1. **Settings → Senders & IPs → Senders**
2. **"Add a new sender" klicken**
3. **Sender-Daten eingeben:**
   - Sender Name: "BabixGO"
   - Email: register@babixgo-mail.de
4. **Verifizierungs-E-Mail bestätigen** (an Domain-Admin)
5. **Alternative Sender hinzufügen:**
   - noreply@babixgo.de

**Hinweis:** Ohne verifizierte Sender können keine E-Mails versendet werden!

### 1.5 Brevo Free Tier Limits

| Feature | Free Tier |
|---------|-----------|
| E-Mails/Tag | 300 |
| E-Mails/Monat | 9.000 |
| Kontakte | Unbegrenzt |
| SMTP-Zugang | ✅ Ja |
| API-Zugang | ✅ Ja |
| DKIM-Signierung | ✅ Ja |
| Dedizierte IP | ❌ Nein |

**Bei Überschreitung:**
- E-Mails werden verzögert
- Oder komplett abgelehnt (550 Error)
- Upgrade auf Paid Plan erforderlich

---

## 2. SMTP-Konfiguration

### 2.1 Environment-Variablen

**Erforderliche Variablen:**

```bash
# Brevo SMTP-Server
SMTP_HOST=smtp-relay.brevo.com

# SMTP-Port (TLS)
SMTP_PORT=587

# Brevo Account E-Mail
SMTP_USER=your-brevo-email@example.com

# Brevo SMTP-API-Key
SMTP_KEY=xsmtpsib-abc123def456...

# Optional: Debug-Modus
DEBUG_MODE=false
```

### 2.2 Sender-Adressen

**In PHP-Konfiguration:**

```php
// Auth-System
define('MAIL_FROM', 'register@babixgo-mail.de');

// Files-System
define('MAIL_FROM_REGISTER', 'register@babixgo-mail.de');
```

**Wichtig:** Sender-Adressen müssen in Brevo verifiziert sein!

---

## 3. Lokale Entwicklungsumgebung

### 3.1 Voraussetzungen

**Software:**
- PHP 7.4+ oder 8.0+
- Composer
- MySQL/MariaDB oder PostgreSQL
- Git

**PHP-Extensions:**
- `openssl` - Für TLS-Verschlüsselung
- `sockets` - Für SMTP-Socket-Verbindung
- `pdo` - Für Datenbankverbindung

### 3.2 Repository klonen

```bash
# Repository klonen
git clone https://github.com/Orga-bgo/babixgo.git
cd babixgo

# In Projekt-Verzeichnis wechseln
cd babixgo.de
```

### 3.3 Dependencies installieren

```bash
# PHPMailer installieren
cd files
composer install

# Zurück zum Hauptverzeichnis
cd ..
```

**Hinweis:** Falls `composer.json` noch nicht existiert:

```bash
cd files
composer init -n
composer require phpmailer/phpmailer:^7.0
```

### 3.4 Environment-Datei erstellen

**Variante A: Root .env (empfohlen)**

```bash
# .env aus Beispiel kopieren
cp .env.example .env

# .env bearbeiten
nano .env
```

```bash
# .env Inhalt
# Database Configuration
DB_HOST=localhost
DB_NAME=babixgo_db
DB_USER=babixgo_user
DB_PASSWORT=your_password

# SMTP Configuration (Brevo)
SMTP_HOST=smtp-relay.brevo.com
SMTP_PORT=587
SMTP_USER=your-brevo-email@example.com
SMTP_KEY=xsmtpsib-your-api-key-here

# Debug Mode
DEBUG_MODE=true
```

**Variante B: PHP-Konfigurationsdatei**

```bash
# Local config erstellen
nano babixgo.de/files/includes/config.local.php
```

```php
<?php
/**
 * Lokale Konfiguration
 * NICHT in Git committen!
 */

// SMTP-Credentials setzen
putenv('SMTP_HOST=smtp-relay.brevo.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=your-email@example.com');
putenv('SMTP_KEY=xsmtpsib-your-key');
putenv('DEBUG_MODE=true');
```

**In `.gitignore` hinzufügen:**

```bash
echo "*.local.php" >> .gitignore
echo ".env" >> .gitignore
```

### 3.5 Datenbank einrichten

```bash
# MySQL/MariaDB Datenbank erstellen
mysql -u root -p

# In MySQL-Shell:
CREATE DATABASE babixgo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'babixgo_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON babixgo_db.* TO 'babixgo_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

# Tabellen erstellen
mysql -u babixgo_user -p babixgo_db < babixgo.de/shared/create-tables.sql
```

**Für PostgreSQL:**

```bash
# PostgreSQL Datenbank erstellen
sudo -u postgres psql

# In psql:
CREATE DATABASE babixgo_db;
CREATE USER babixgo_user WITH PASSWORD 'your_password';
GRANT ALL PRIVILEGES ON DATABASE babixgo_db TO babixgo_user;
\q

# Tabellen erstellen
psql -U babixgo_user -d babixgo_db -f babixgo.de/shared/create-tables-postgres.sql
```

### 3.6 Lokalen Webserver starten

**Option A: PHP Built-in Server**

```bash
cd babixgo.de
php -S localhost:8000
```

Öffne Browser: http://localhost:8000

**Option B: Apache/XAMPP**

```bash
# In Apache vhost config (z.B. /etc/apache2/sites-available/babixgo.conf)
<VirtualHost *:80>
    ServerName babixgo.local
    DocumentRoot /path/to/babixgo/babixgo.de
    
    <Directory /path/to/babixgo/babixgo.de>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/babixgo-error.log
    CustomLog ${APACHE_LOG_DIR}/babixgo-access.log combined
</VirtualHost>
```

```bash
# Site aktivieren
sudo a2ensite babixgo.conf
sudo systemctl reload apache2

# /etc/hosts bearbeiten
echo "127.0.0.1 babixgo.local" | sudo tee -a /etc/hosts
```

Öffne Browser: http://babixgo.local

### 3.7 Test-E-Mail versenden

**Test-Script erstellen:**

```bash
nano /tmp/test-email.php
```

```php
<?php
/**
 * E-Mail-Test-Script
 */

require_once __DIR__ . '/../babixgo/babixgo.de/files/includes/email.php';

echo "=== BabixGO E-Mail-System Test ===\n\n";

// Konfiguration prüfen
echo "1. Konfiguration:\n";
echo "   SMTP_HOST: " . (defined('SMTP_HOST') ? SMTP_HOST : '❌ Nicht definiert') . "\n";
echo "   SMTP_PORT: " . (defined('SMTP_PORT') ? SMTP_PORT : '❌ Nicht definiert') . "\n";
echo "   SMTP_USER: " . (defined('SMTP_USER') && !empty(SMTP_USER) ? '✅ Gesetzt' : '❌ Nicht gesetzt') . "\n";
echo "   SMTP_PASS: " . (defined('SMTP_PASS') && !empty(SMTP_PASS) ? '✅ Gesetzt' : '❌ Nicht gesetzt') . "\n\n";

// Test-E-Mail senden
echo "2. Test-E-Mail versenden...\n";
$testEmail = 'your-email@example.com'; // ÄNDERN!
$testResult = sendVerificationEmail($testEmail, 'TestUser', bin2hex(random_bytes(32)));

if ($testResult) {
    echo "   ✅ E-Mail erfolgreich versendet an: $testEmail\n";
    echo "   📧 Bitte Postfach prüfen!\n";
} else {
    echo "   ❌ E-Mail-Versand fehlgeschlagen!\n";
    echo "   💡 Logs prüfen: tail -f /var/log/php_errors.log\n";
}

echo "\n=== Test abgeschlossen ===\n";
```

```bash
# Test ausführen
php /tmp/test-email.php
```

---

## 4. Produktions-Deployment

### 4.1 Strato-Server Setup

#### 4.1.1 Via FTP/SFTP

**1. FileZilla oder WinSCP verbinden:**
```
Host: babixgo.de
Port: 22 (SFTP) oder 21 (FTP)
User: <Strato-Benutzername>
Password: <Strato-Passwort>
```

**2. Dateien hochladen:**
- Gesamtes `babixgo.de` Verzeichnis hochladen
- `.env` Datei NICHT hochladen (Sicherheitsrisiko)
- `.git` Verzeichnis NICHT hochladen

#### 4.1.2 Environment-Variablen setzen

**Option 1: config.local.php (empfohlen für Strato)**

```bash
# Via SFTP/SSH
nano /var/www/vhosts/babixgo.de/httpdocs/files/includes/config.local.php
```

```php
<?php
/**
 * Produktions-Konfiguration
 * Datei-Rechte: 640 (nur Webserver lesbar)
 */

// SMTP-Credentials (Brevo Production)
putenv('SMTP_HOST=smtp-relay.brevo.com');
putenv('SMTP_PORT=587');
putenv('SMTP_USER=production@babixgo-mail.de');
putenv('SMTP_KEY=xsmtpsib-production-key-hier');

// Production: Debug AUS!
putenv('DEBUG_MODE=false');

// Datenbank (falls nicht über .env)
putenv('DB_HOST=localhost');
putenv('DB_NAME=babixgo_production');
putenv('DB_USER=babixgo_prod_user');
putenv('DB_PASSWORT=secure_production_password');
```

**Option 2: .htaccess (falls Apache mod_env aktiviert)**

```bash
nano /var/www/vhosts/babixgo.de/httpdocs/.htaccess
```

```apache
# SMTP-Konfiguration
SetEnv SMTP_HOST "smtp-relay.brevo.com"
SetEnv SMTP_PORT "587"
SetEnv SMTP_USER "production@babixgo-mail.de"
SetEnv SMTP_KEY "xsmtpsib-your-production-key"
SetEnv DEBUG_MODE "false"
```

#### 4.1.3 Dateiberechtigungen

```bash
# Via SSH
ssh user@babixgo.de

# config.local.php nur für Webserver lesbar
chmod 640 files/includes/config.local.php
chown www-data:www-data files/includes/config.local.php

# Andere Dateien
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 600 .env  # Falls vorhanden
```

#### 4.1.4 Composer Dependencies

```bash
# Via SSH
cd /var/www/vhosts/babixgo.de/httpdocs/files
/usr/local/bin/composer install --no-dev --optimize-autoloader

# Falls Composer nicht global:
php composer.phar install --no-dev --optimize-autoloader
```

#### 4.1.5 Test-E-Mail auf Production

```bash
# Test-Script hochladen (in /tmp/ oder temporäres Verzeichnis)
nano /var/www/vhosts/babixgo.de/httpdocs/tmp/test-email-prod.php
```

```php
<?php
require_once __DIR__ . '/../files/includes/email.php';

$result = sendEmail(
    'admin@babixgo.de',
    'Production Test',
    '<h1>Server E-Mail Test</h1><p>E-Mail-System funktioniert!</p>'
);

echo $result ? 'SUCCESS' : 'FAILED';
```

```bash
# Via Browser aufrufen
https://babixgo.de/tmp/test-email-prod.php

# WICHTIG: Nach Test LÖSCHEN!
rm /var/www/vhosts/babixgo.de/httpdocs/tmp/test-email-prod.php
```

### 4.2 GitHub Actions Deployment

#### 4.2.1 GitHub Secrets konfigurieren

1. **GitHub Repository öffnen:** https://github.com/Orga-bgo/babixgo
2. **Settings → Secrets and variables → Actions**
3. **New repository secret:**

| Name | Value | Beschreibung |
|------|-------|--------------|
| `SMTP_HOST` | smtp-relay.brevo.com | SMTP-Server |
| `SMTP_PORT` | 587 | SMTP-Port |
| `SMTP_USER` | production@babixgo-mail.de | Brevo Account E-Mail |
| `SMTP_KEY` | xsmtpsib-prod-key... | Brevo SMTP API Key |
| `SMTP_SENDER_REGISTRATION` | register@babixgo-mail.de | Sender-Adresse |
| `DB_HOST` | localhost | Datenbank-Host |
| `DB_NAME` | babixgo_prod | Datenbank-Name |
| `DB_USER` | babixgo_user | DB-User |
| `DB_PASSWORD` | secure_password | DB-Passwort |

#### 4.2.2 Workflow-Datei erstellen

```bash
mkdir -p .github/workflows
nano .github/workflows/deploy.yml
```

```yaml
name: Deploy to Production

on:
  push:
    branches:
      - main

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
      - name: Checkout code
        uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: pdo, pdo_mysql, openssl
      
      - name: Install Composer dependencies
        run: |
          cd babixgo.de/files
          composer install --no-dev --optimize-autoloader
      
      - name: Create .env file
        run: |
          cat << EOF > .env
          SMTP_HOST=${{ secrets.SMTP_HOST }}
          SMTP_PORT=${{ secrets.SMTP_PORT }}
          SMTP_USER=${{ secrets.SMTP_USER }}
          SMTP_KEY=${{ secrets.SMTP_KEY }}
          DB_HOST=${{ secrets.DB_HOST }}
          DB_NAME=${{ secrets.DB_NAME }}
          DB_USER=${{ secrets.DB_USER }}
          DB_PASSWORD=${{ secrets.DB_PASSWORD }}
          DEBUG_MODE=false
          EOF
      
      - name: Deploy to server
        uses: easingthemes/ssh-deploy@main
        env:
          SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
          REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
          REMOTE_USER: ${{ secrets.REMOTE_USER }}
          TARGET: /var/www/vhosts/babixgo.de/httpdocs/
          EXCLUDE: ".git, .github, node_modules, .env.example"
```

### 4.3 Domain-Konfiguration

#### 4.3.1 DNS-Records (bei Strato oder anderem DNS-Provider)

**SPF-Record:**
```
Type: TXT
Name: @
Value: v=spf1 include:spf.brevo.com ~all
TTL: 3600
```

**DKIM-Record:**
```
1. Brevo Dashboard → Settings → Senders & IPs → Domains
2. Domain hinzufügen: babixgo.de
3. DKIM-Key erhalten
4. DNS TXT-Record erstellen:
   Type: TXT
   Name: brevo._domainkey
   Value: <Von Brevo bereitgestellter DKIM-Key>
```

**DMARC-Record:**
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=none; rua=mailto:postmaster@babixgo.de; ruf=mailto:postmaster@babixgo.de; fo=1
TTL: 3600
```

#### 4.3.2 DNS-Records überprüfen

```bash
# SPF prüfen
dig TXT babixgo.de +short

# DKIM prüfen
dig TXT brevo._domainkey.babixgo.de +short

# DMARC prüfen
dig TXT _dmarc.babixgo.de +short
```

**Online-Tools:**
- MXToolbox: https://mxtoolbox.com/SuperTool.aspx
- Mail-Tester: https://www.mail-tester.com

---

## 5. Testing

### 5.1 Unit-Tests (empfohlen für Zukunft)

**Test-Framework installieren:**

```bash
cd babixgo.de
composer require --dev phpunit/phpunit:^9.5
```

**Test-Datei erstellen:**

```bash
mkdir -p tests
nano tests/EmailTest.php
```

```php
<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../files/includes/email.php';

class EmailTest extends TestCase
{
    public function testSendEmail()
    {
        $result = sendEmail(
            'test@mailinator.com',
            'PHPUnit Test',
            '<h1>Test E-Mail</h1>'
        );
        
        $this->assertTrue($result, 'E-Mail sollte erfolgreich versendet werden');
    }
    
    public function testInvalidEmail()
    {
        $result = sendEmail(
            'invalid-email',
            'Test',
            'Body'
        );
        
        $this->assertFalse($result, 'Ungültige E-Mail sollte fehlschlagen');
    }
}
```

**Tests ausführen:**

```bash
./vendor/bin/phpunit tests/EmailTest.php
```

### 5.2 Manuelle Test-Szenarien

#### 5.2.1 Registrierungs-Flow testen

**1. Neue Registrierung:**
```
1. https://babixgo.de/auth/register öffnen
2. Formular ausfüllen:
   - Username: testuser123
   - E-Mail: your-test-email@example.com
   - Passwort: SecurePass123!
3. "Registrieren" klicken
4. Erwartung: Success-Message + E-Mail in Postfach
```

**2. E-Mail-Verifizierung:**
```
1. E-Mail öffnen (Subject: "E-Mail-Adresse bestätigen")
2. "E-Mail bestätigen" Button klicken
3. Erwartung: Redirect zu Login + Success-Message
4. Weitere E-Mail erhalten (Subject: "Willkommen bei BabixGO!")
```

**3. Login testen:**
```
1. https://babixgo.de/auth/login öffnen
2. Mit verifizierten Credentials einloggen
3. Erwartung: Erfolgreicher Login
```

#### 5.2.2 Passwort-Reset testen

**1. Reset anfordern:**
```
1. https://babixgo.de/auth/forgot-password öffnen
2. E-Mail-Adresse eingeben
3. "Send Reset Link" klicken
4. Erwartung: E-Mail in Postfach
```

**2. Passwort zurücksetzen:**
```
1. E-Mail öffnen (Subject: "Passwort zurücksetzen")
2. "Passwort zurücksetzen" klicken
3. Neues Passwort eingeben
4. Erwartung: Success + Login möglich
```

#### 5.2.3 E-Mail-Delivery testen

**Mail-Tester nutzen:**

```
1. https://www.mail-tester.com öffnen
2. Temporäre E-Mail-Adresse kopieren (z.B. test-xyz@mail-tester.com)
3. Test-E-Mail senden:
   sendEmail('test-xyz@mail-tester.com', 'Test', '<h1>Test</h1>');
4. "Then check your score" klicken
5. Erwartung: Score > 8/10
```

**Checkliste:**
- ✅ SPF-Check: PASS
- ✅ DKIM-Check: PASS
- ✅ DMARC-Check: PASS
- ✅ Spam-Score: < 2.0
- ✅ Blacklist-Check: Clean

### 5.3 Test-E-Mail-Adressen

**Temporäre E-Mail-Services:**

| Service | URL | Beschreibung |
|---------|-----|--------------|
| Mailinator | mailinator.com | Öffentliche Inbox, keine Anmeldung |
| Temp-Mail | temp-mail.org | Temporäre E-Mail für Tests |
| Guerrilla Mail | guerrillamail.com | Wegwerf-E-Mails |
| Mailtrap | mailtrap.io | E-Mail-Testing für Entwickler (SMTP Sandbox) |

**Mailtrap Setup (empfohlen für Dev):**

```bash
# In .env für Development
SMTP_HOST=smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USER=<mailtrap-username>
SMTP_KEY=<mailtrap-password>
```

Vorteile:
- Keine echten E-Mails versendet
- Alle E-Mails in Web-Interface sichtbar
- HTML & Plain-Text Vorschau
- Spam-Check integriert

---

## 6. Troubleshooting

### 6.1 Häufige Fehler

#### Problem 1: "SMTP connect() failed"

**Symptome:**
```
SMTP Error: Could not connect to SMTP server
```

**Ursachen & Lösungen:**

**1. Firewall blockiert Port 587**
```bash
# Port-Erreichbarkeit testen
telnet smtp-relay.brevo.com 587

# Oder mit nc
nc -zv smtp-relay.brevo.com 587

# Erwartung: "Connected to smtp-relay.brevo.com"
```

**Lösung:**
```bash
# UFW Firewall (Ubuntu/Debian)
sudo ufw allow out 587/tcp

# iptables
sudo iptables -A OUTPUT -p tcp --dport 587 -j ACCEPT
```

**2. Falscher Host oder Port**
```php
// In config prüfen
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";

// Sollte sein:
// SMTP_HOST: smtp-relay.brevo.com
// SMTP_PORT: 587
```

**3. TLS/SSL-Problem**
```php
// PHP OpenSSL Extension prüfen
php -m | grep openssl

// Falls nicht vorhanden:
sudo apt-get install php-openssl
sudo systemctl restart apache2
```

---

#### Problem 2: "Authentication failed"

**Symptome:**
```
SMTP Error: Could not authenticate
535 Authentication failed
```

**Lösungen:**

**1. Credentials prüfen**
```php
<?php
// test-credentials.php
echo "SMTP_USER: " . (getenv('SMTP_USER') ?: 'NOT SET') . "\n";
echo "SMTP_KEY: " . (getenv('SMTP_KEY') ? 'SET (length: ' . strlen(getenv('SMTP_KEY')) . ')' : 'NOT SET') . "\n";
```

**2. Neuen SMTP-Key generieren**
```
1. Brevo Dashboard öffnen
2. Settings → SMTP & API → SMTP
3. Alten Key löschen
4. "Generate a new SMTP key" klicken
5. Key kopieren und in .env eintragen
```

**3. Whitespace-Probleme**
```bash
# .env prüfen (keine Leerzeichen!)
# FALSCH:
SMTP_KEY = xsmtpsib-abc123

# RICHTIG:
SMTP_KEY=xsmtpsib-abc123
```

---

#### Problem 3: "Rate limit exceeded"

**Symptome:**
```
550 5.7.1 Daily sending quota exceeded
```

**Lösungen:**

**1. Brevo Account upgraden**
```
Free Tier: 300 E-Mails/Tag
Lite Plan: 20.000 E-Mails/Monat ($25/mo)
Premium: 40.000 E-Mails/Monat ($65/mo)
```

**2. E-Mail-Queue implementieren**
```php
<?php
// Queue-System (Redis/DB)
function queueEmail($to, $subject, $body) {
    $db = Database::getInstance();
    $db->execute(
        "INSERT INTO email_queue (recipient, subject, body, created_at) VALUES (?, ?, ?, NOW())",
        [$to, $subject, $body]
    );
}

// Cron-Job: Maximal 200 E-Mails/Tag versenden
// 0 */6 * * * php /path/to/process-email-queue.php
```

**3. Rate-Limiting in Code**
```php
<?php
$emailsSentToday = $redis->get('emails_sent:' . date('Y-m-d')) ?? 0;

if ($emailsSentToday >= 280) { // Puffer lassen
    throw new Exception('Tägliches E-Mail-Limit erreicht');
}

sendEmail($to, $subject, $body);
$redis->incr('emails_sent:' . date('Y-m-d'));
$redis->expire('emails_sent:' . date('Y-m-d'), 86400);
```

---

#### Problem 4: "E-Mails landen im Spam"

**Checkliste:**

**1. SPF-Record prüfen**
```bash
dig TXT babixgo.de +short

# Sollte enthalten:
"v=spf1 include:spf.brevo.com ~all"
```

**2. DKIM-Signatur prüfen**
```bash
# In Brevo Dashboard:
Settings → Senders & IPs → Domains → babixgo.de
Status: "Authenticated" (grün)

# DNS prüfen:
dig TXT brevo._domainkey.babixgo.de +short
```

**3. DMARC-Policy setzen**
```bash
# DNS TXT-Record erstellen
_dmarc.babixgo.de → v=DMARC1; p=quarantine; rua=mailto:postmaster@babixgo.de
```

**4. E-Mail-Inhalt optimieren**
```
❌ Vermeiden:
- Zu viele Ausrufezeichen!!!
- Vollständig in GROSSBUCHSTABEN
- Spam-Wörter: "FREE", "CLICK HERE", "100% GUARANTEED"
- Zu viele Links
- Anhänge (bei transaktionalen E-Mails)

✅ Best Practices:
- Klarer Absendername
- Professionelles Design
- Plain-Text Alternative
- Unsubscribe-Link (bei Marketing-E-Mails)
- Kontakt-Impressum
```

**5. Sender-Reputation aufbauen**
```
- Langsam starten (< 50 E-Mails/Tag)
- Schrittweise erhöhen
- Bounce-Rate minimieren (< 5%)
- Spam-Complaints vermeiden (< 0.1%)
```

---

#### Problem 5: "PHPMailer not found"

**Symptome:**
```
Fatal error: Class 'PHPMailer\PHPMailer\PHPMailer' not found
```

**Lösungen:**

**1. Composer installieren**
```bash
cd babixgo.de/files

# Composer installieren (falls nicht vorhanden)
curl -sS https://getcomposer.org/installer | php
php composer.phar install

# Oder global:
composer install
```

**2. Autoloader prüfen**
```php
<?php
// In email.php prüfen
var_dump(file_exists(__DIR__ . '/../vendor/autoload.php'));

// Sollte true sein
```

**3. Composer.json erstellen**
```bash
cd babixgo.de/files
cat > composer.json << 'EOF'
{
    "require": {
        "phpmailer/phpmailer": "^7.0"
    }
}
EOF

composer install
```

---

### 6.2 Debug-Modus aktivieren

**In .env:**
```bash
DEBUG_MODE=true
```

**In config.php:**
```php
define('DEBUG_MODE', true);
```

**PHPMailer Debug-Output:**
```php
$mail->SMTPDebug = 2; // 0=off, 1=client, 2=client+server, 3=+connection
$mail->Debugoutput = function($str, $level) {
    error_log("PHPMailer [$level]: $str");
};
```

**Logs ansehen:**
```bash
# PHP Error Log
tail -f /var/log/php_errors.log

# Apache Error Log
tail -f /var/log/apache2/error.log

# Strato
tail -f /logs/error_log
```

---

### 6.3 Hilfreiche Befehle

**SMTP-Verbindung testen:**
```bash
# Telnet-Test
telnet smtp-relay.brevo.com 587

# OpenSSL-Test (mit TLS)
openssl s_client -starttls smtp -connect smtp-relay.brevo.com:587
```

**PHP-Konfiguration prüfen:**
```bash
# PHP-Version
php -v

# Installierte Extensions
php -m

# Konfiguration
php -i | grep -i mail
```

**DNS-Records prüfen:**
```bash
# SPF
dig TXT babixgo.de +short

# DKIM
dig TXT brevo._domainkey.babixgo.de +short

# DMARC
dig TXT _dmarc.babixgo.de +short

# MX-Records
dig MX babixgo.de +short
```

---

## 7. Checkliste: Go-Live

### Pre-Launch

- [ ] Brevo-Account erstellt und verifiziert
- [ ] SMTP-API-Key generiert
- [ ] Sender-Adressen verifiziert (register@babixgo-mail.de)
- [ ] SPF-Record konfiguriert
- [ ] DKIM aktiviert
- [ ] DMARC-Policy gesetzt
- [ ] PHPMailer installiert (Composer)
- [ ] Environment-Variablen gesetzt
- [ ] Datenbank-Tabellen erstellt
- [ ] Test-E-Mail erfolgreich versendet

### Post-Launch

- [ ] Monitoring einrichten (E-Mail-Versand-Rate)
- [ ] E-Mail-Logs regelmäßig prüfen
- [ ] Bounce-Rate überwachen (< 5%)
- [ ] Spam-Complaints überwachen (< 0.1%)
- [ ] Brevo-Dashboard täglich prüfen
- [ ] Backup-SMTP-Provider vorbereiten (Fallback)

---

## 8. Support & Ressourcen

**Brevo Support:**
- Help Center: https://help.brevo.com
- Live Chat: Im Brevo Dashboard
- E-Mail: support@brevo.com

**PHPMailer:**
- GitHub: https://github.com/PHPMailer/PHPMailer
- Dokumentation: https://github.com/PHPMailer/PHPMailer/wiki
- Troubleshooting: https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting

**Community:**
- Stack Overflow: Tag `phpmailer`
- GitHub Issues: https://github.com/Orga-bgo/babixgo/issues

---

**Dokumentation erstellt:** 2026-01-17  
**Version:** 1.0  
**Autor:** BabixGO Development Team
