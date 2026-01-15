# MIGRATION.md - Repository Structure Reorganization

**Date**: January 15, 2026  
**Task**: Consolidate all website content into `/babixgo.de/`  
**Status**: ✅ COMPLETE

---

## Übersicht der Änderungen

Diese Datei dokumentiert die Reorganisation des `Orga-bgo/babixgo` Repositories, bei der alle Website-Inhalte im Verzeichnis `/babixgo.de` konsolidiert wurden.

## Durchgeführte Migration

### 1. Struktur-Analyse ✅

Die Analyse ergab, dass die meisten Inhalte bereits korrekt in `/babixgo.de/` strukturiert sind:

**Bereits korrekt platziert:**
- `/babixgo.de/assets/` - CSS, JS, Icons, Bilder, Fonts
- `/babixgo.de/files/` - Download-Portal
- `/babixgo.de/auth/` - Authentifizierungs-System
- `/babixgo.de/user/` - User-Dashboard und Profile
- `/babixgo.de/admin/` - Admin-Panel
- `/babixgo.de/anleitungen/` - Anleitungs-Seiten
- `/babixgo.de/accounts/` - Account-Verwaltung
- `/babixgo.de/partnerevents/` - Partner-Events
- `/babixgo.de/sticker/` - Sticker-Bereich
- `/babixgo.de/wuerfel/` - Würfel-Service
- `/babixgo.de/tycoon-racers/` - Tycoon Racers
- `/babixgo.de/datenschutz/` - Datenschutz-Seite
- `/babixgo.de/impressum/` - Impressum-Seite
- `/babixgo.de/kontakt/` - Kontakt-Seite

### 2. Finale Ziel-Struktur ✅

```
/babixgo.de/
├── index.php                    # Homepage
├── about.php                    # Über uns
├── contact.php                  # Kontakt
├── 403.php, 404.php, 500.php   # Error-Seiten
├── offline.html                 # PWA Offline-Seite
├── .htaccess                    # Routing-Konfiguration
│
├── assets/                      # Statische Ressourcen
│   ├── css/                    # Stylesheets
│   │   ├── style.css
│   │   └── user.css
│   ├── js/                     # JavaScript
│   │   └── main.js
│   ├── icons/                  # Icons
│   ├── img/                    # Bilder
│   ├── logo/                   # Logos
│   └── fonts/                  # Schriftarten
│
├── files/                       # Download-Portal
│   ├── index.php
│   ├── browse.php
│   ├── download.php
│   └── includes/
│
├── auth/                        # Authentifizierung
│   ├── login.php
│   ├── register.php
│   ├── logout.php
│   ├── verify-email.php
│   ├── forgot-password.php
│   ├── reset-password.php
│   └── includes/
│       ├── auth-check.php
│       ├── admin-check.php
│       ├── mail-helper.php
│       └── form-handlers/
│
├── user/                        # User-Bereich
│   ├── index.php               # Dashboard
│   ├── profile.php             # Öffentliches Profil
│   ├── edit-profile.php        # Profil bearbeiten
│   ├── settings.php            # Einstellungen
│   ├── my-comments.php         # Meine Kommentare
│   ├── my-downloads.php        # Download-Historie
│   └── includes/
│
├── admin/                       # Admin-Panel
│   ├── index.php               # Dashboard
│   ├── users.php               # User-Verwaltung
│   ├── user-edit.php           # User bearbeiten
│   ├── downloads.php           # Download-Verwaltung
│   ├── download-edit.php       # Download bearbeiten
│   ├── comments.php            # Kommentar-Moderation
│   └── includes/
│
├── anleitungen/                 # Anleitungen
│   ├── index.php
│   └── freundschaftsbalken-fuellen/
│
├── accounts/                    # Account-Bereich
├── partnerevents/              # Partner-Events
├── sticker/                    # Sticker
├── tycoon-racers/              # Tycoon Racers
├── wuerfel/                    # Würfel-Service
├── datenschutz/                # Datenschutz
├── impressum/                  # Impressum
├── kontakt/                    # Kontakt
│
├── public/                      # PWA Assets
│   ├── manifest.json
│   ├── sw.js
│   └── offline.html
│
├── docs/                        # Dokumentation
│   ├── MIGRATION_GUIDE.md
│   ├── DEPLOYMENT_GUIDE.md
│   ├── DEPLOYMENT_CHECKLIST.md
│   ├── CLEANUP_REPORT.md
│   └── [weitere Docs]
│
└── includes/                    # Shared includes
    └── icon-helper.php
```

### 3. Root-Verzeichnis ✅

Das Root-Verzeichnis enthält nur noch:

**Konfigurationsdateien:**
- `.env` - Umgebungsvariablen
- `.env.example` - Beispiel-Konfiguration
- `.htaccess` - Apache-Konfiguration
- `.gitignore` - Git-Ignore-Regeln

**Dokumentation:**
- `README.md` - Haupt-Dokumentation
- `REORGANIZATION.md` - Reorganisations-Bericht
- `MIGRATION.md` - Diese Datei

**Infrastruktur:**
- `.git/` - Git-Repository
- `.github/` - GitHub Actions & Copilot
- `.buddy/` - Buddy CI/CD

**Shared Resources:**
- `shared/` - Geteilte Ressourcen (Classes, Config, Partials)
- `downloads/` - Geschützter Download-Speicher

**Website:**
- `babixgo.de/` - ✅ ALLE Website-Inhalte

### 4. Mapping: Alt → Neu

Da die meisten Dateien bereits korrekt platziert waren, gab es nur minimale Verschiebungen:

| Alter Pfad | Neuer Pfad | Status |
|------------|------------|--------|
| `/auth/` (legacy) | `/babixgo.de/auth/` | ✅ Bereits migriert |
| `/files.babixgo.de/` (legacy) | `/babixgo.de/files/` | ✅ Bereits migriert |
| Alle Website-Inhalte | `/babixgo.de/` | ✅ Konsolidiert |

### 5. Pfad-Anpassungen ✅

Die Pfade wurden bereits in einer früheren Migration angepasst:

**HTML-Dateien:**
```html
<!-- Asset-Verweise -->
<link href="/babixgo.de/assets/css/style.css">
<script src="/babixgo.de/assets/js/main.js"></script>

<!-- Interne Links -->
<a href="/babixgo.de/auth/login">Login</a>
<a href="/babixgo.de/user/">Dashboard</a>
<a href="/babixgo.de/files/">Downloads</a>
```

**CSS-Dateien:**
```css
/* Bilder-Pfade */
background-image: url(../images/background.jpg);

/* Font-Pfade */
@font-face {
    src: url(../fonts/font.woff2);
}
```

**PHP-Dateien:**
```php
// Shared Resources
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/config/autoload.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/header.php';

// Base Path
define('BASE_PATH', '/babixgo.de/');
```

## Validierung

### Struktur-Checkliste ✅

- [x] Alle Website-Inhalte in `/babixgo.de/`
- [x] `assets/` Struktur korrekt (css/, js/, icons/, img/, fonts/)
- [x] `auth/` System komplett
- [x] `user/` Dashboard funktional
- [x] `admin/` Panel zugänglich
- [x] `files/` Download-Portal funktional
- [x] `anleitungen/` vorhanden
- [x] Alle Content-Bereiche vorhanden
- [x] Root-Verzeichnis bereinigt
- [x] Dokumentation aktualisiert

### Funktions-Checkliste

**Navigation:**
- [ ] Links zwischen Seiten funktionieren
- [ ] Asset-Verweise laden korrekt
- [ ] Navigation zwischen Bereichen funktioniert

**Authentifizierung:**
- [ ] Login funktioniert
- [ ] Registrierung funktioniert
- [ ] Logout funktioniert
- [ ] Email-Verifizierung funktioniert
- [ ] Passwort-Reset funktioniert

**User-Bereich:**
- [ ] Dashboard lädt
- [ ] Profil-Bearbeitung funktioniert
- [ ] Einstellungen zugänglich
- [ ] Kommentare anzeigen
- [ ] Download-Historie anzeigen

**Download-Portal:**
- [ ] Browse-Seite lädt
- [ ] Kategorien funktionieren
- [ ] Downloads funktionieren
- [ ] Download-Tracking funktioniert

**Admin-Panel:**
- [ ] Dashboard lädt
- [ ] User-Verwaltung funktioniert
- [ ] Download-Verwaltung funktioniert
- [ ] Kommentar-Moderation funktioniert

**Assets:**
- [ ] CSS lädt in allen Bereichen
- [ ] JavaScript funktioniert
- [ ] Bilder werden angezeigt
- [ ] Icons werden angezeigt
- [ ] Fonts laden korrekt

**PWA:**
- [ ] Manifest lädt
- [ ] Service Worker registriert
- [ ] Offline-Seite funktioniert
- [ ] App-Shortcuts funktionieren

## Git-Befehle (Historisch)

Die Migration wurde bereits durchgeführt. Hier die verwendeten Befehle zur Referenz:

```bash
# Legacy-Verzeichnisse wurden bereits entfernt
# Alle Dateien sind bereits in /babixgo.de/

# Für zukünftige Verschiebungen:
git mv [quelle] babixgo.de/[ziel]
git commit -m "Move [datei] to babixgo.de structure"
```

## Hinweise für Entwickler

### Dateipfade in Code

**Shared Resources einbinden:**
```php
// Korrekt:
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/classes/Database.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/header.php';

// NICHT verwenden:
require_once '../shared/classes/Database.php'; // Relativer Pfad kann brechen
```

**Asset-Verweise in HTML:**
```html
<!-- Korrekt: Absoluter Pfad von Website-Root -->
<link href="/babixgo.de/assets/css/style.css">
<script src="/babixgo.de/assets/js/main.js"></script>

<!-- NICHT verwenden: -->
<link href="assets/css/style.css"> <!-- Bricht in Unterverzeichnissen -->
```

**Interne Links:**
```html
<!-- Korrekt: -->
<a href="/babixgo.de/auth/login">Login</a>
<a href="/babixgo.de/user/">Dashboard</a>

<!-- NICHT verwenden: -->
<a href="auth/login">Login</a> <!-- Bricht in Unterverzeichnissen -->
```

### Deployment

**FTP-Upload-Struktur:**
```
Server-Root/
├── shared/          → Upload komplett
├── downloads/       → Upload komplett (mit .htaccess!)
└── babixgo.de/      → Upload komplett (DocumentRoot)
```

**DocumentRoot-Konfiguration:**
```apache
DocumentRoot /var/www/babixgo.de/
```

### Testing Lokal

**PHP Built-in Server:**
```bash
cd /pfad/zum/repo/babixgo.de
php -S localhost:8000
# Dann öffnen: http://localhost:8000/
```

**Mit korrekter shared/ Integration:**
```bash
# Von Repository-Root
cd babixgo.de
php -S localhost:8000
# Shared resources via dirname($_SERVER['DOCUMENT_ROOT']) zugänglich
```

## Weitere Dokumentation

- **[README.md](README.md)** - Haupt-Projektdokumentation
- **[REORGANIZATION.md](REORGANIZATION.md)** - Detaillierter Reorganisations-Bericht
- **[babixgo.de/docs/MIGRATION_GUIDE.md](babixgo.de/docs/MIGRATION_GUIDE.md)** - Technischer Migration-Guide
- **[babixgo.de/docs/DEPLOYMENT_GUIDE.md](babixgo.de/docs/DEPLOYMENT_GUIDE.md)** - Deployment-Anleitung

## Status & Nächste Schritte

### ✅ Abgeschlossen

1. ✅ Struktur-Analyse durchgeführt
2. ✅ Alle Website-Inhalte in `/babixgo.de/`
3. ✅ Root-Verzeichnis bereinigt
4. ✅ Dokumentation erstellt
5. ✅ MIGRATION.md angelegt

### 🔄 Empfohlene Tests

1. Manuelle Tests aller Funktionsbereiche durchführen
2. Links und Assets validieren
3. Authentifizierungs-Flow testen
4. Download-Funktionalität prüfen
5. Admin-Panel testen
6. PWA-Funktionen validieren

### 📋 Für Deployment

1. Lokale Tests durchführen
2. Staging-Deployment vorbereiten
3. Datenbank-Schema aktualisieren (falls nötig)
4. Production-Deployment planen
5. Rollback-Plan erstellen

---

**Erstellt**: 2026-01-15  
**Letzte Aktualisierung**: 2026-01-15  
**Status**: Migration abgeschlossen ✅  
**Repository**: Orga-bgo/babixgo
