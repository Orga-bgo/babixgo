# CLAUDE.md - Projektleitung für Claude Code

**Repository:** babixGO Monorepo
**Version:** 2.0.0
**Letzte Aktualisierung:** 2026-01-16
**Status:** Production-Ready

---

## 🎯 Projektkontext

### Repository-Übersicht

**Hauptzweck:** babixGO ist eine **Monopoly GO Service-Plattform** mit integrierter Web-Applikation.

**Zwei Hauptkomponenten:**

1. **Monopoly GO Services** (`/babixgo.de/`) - Hauptgeschäft
   - Würfel-Boost-Services (€15-55)
   - Partner-Event-Services (€6-28)
   - Fertige Accounts (€150-250)
   - Sticker-Verkauf (€2-4)
   - Freundschaftsbalken-Service (€3)

2. **Web-Applikation** (Plattform-Features)
   - User-Management (`/auth/`, `/user/`)
   - Download-Portal (`/files/`)
   - Admin-Panel (`/admin/`)
   - Kommentarsystem
   - PWA-Funktionalität

**Website-Pfad:** `./babixgo.de/`

**Technologie-Stack:**
- Backend: PHP 8.2+ (pure, keine Frameworks)
- Database: MySQL/MariaDB (Strato) + PostgreSQL (Supabase) Support
- Frontend: HTML5, Pure CSS3, Vanilla JavaScript
- PWA: Service Worker, Offline-Support
- Deployment: FTP zu Strato Webhosting
- **KEINE Build-Tools:** Direktes Deployment

---

## 🚀 Claude Code Einsatzbereiche

### 1. Fehleranalyse und -behebung

**Häufige Problembereiche:**
- Session-Management über Domains
- Download-Portal-Logik
- User-Authentifizierung
- PWA Service Worker Issues
- Partials-Einbindung

**Vorgehen:**
1. Error Logs prüfen (Browser Console + PHP Error Logs)
2. Relevante Dateien in `/babixgo.de/` identifizieren
3. Shared Partials prüfen (`/babixgo.de/shared/`)
4. Database-Verbindung testen (`.env` Credentials)

### 2. Neue Features implementieren

**Workflow:**
1. **IMMER** zuerst `babixgo.de/Agents.md` lesen (verbindliche Regeln!)
2. Design System prüfen: `babixgo.de/Docs/DESIGN_SYSTEM.md`
3. Keine Duplikate erstellen (zentrale Stellen nutzen)
4. Partials-Struktur respektieren
5. Testing Guide befolgen: `docs/website/TESTING_GUIDE.md`

**Wichtige Prinzipien:**
- Keine Inline-Styles (nur in `assets/css/style.css`)
- Keine Inline-Scripts (nur in `assets/js/main.js`)
- Partials immer mit `<?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/DATEI.php'; ?>`
- Genau eine H1 pro Seite
- Bilder immer mit `alt`-Attribut

### 3. Inhaltsänderungen und -optimierung

**Monopoly GO Services** (`/babixgo.de/angebote/`):
- Würfel-Boost: `/angebote/wuerfel/`
- Partner-Events: `/angebote/partnerevents/`
- Accounts: `/angebote/accounts/`
- Sticker: `/angebote/sticker/`
- Freundschaftsbalken: `/angebote/freundschaftsbalken-fuellen/`

**Anleitungen** (`/babixgo.de/anleitungen/`):
- Tutorials für DIY-Services
- Verlinken zu `/files/` für Tool-Downloads

### 4. Datenbank-Operationen

**Referenz:** `docs/database/QUICK_REFERENCE.md`

**Schema-Änderungen:**
1. `docs/database/SCHEMA_REQUIREMENTS.md` lesen
2. Migration-Scripts in `docs/database/MIGRATION_GUIDE.md`
3. Update `shared/create-tables.sql`

### 5. Deployment

**VOR Live-Schaltung:**
1. `docs/deployment/DEPLOYMENT_GUIDE.md` lesen
2. Checklist abarbeiten: `docs/deployment/DEPLOYMENT_CHECKLIST.md`
3. Validation durchführen: `docs/project/VALIDATION_CHECKLIST.md`

**FTP-Upload zu Strato:**
- Kein Build-Prozess
- Direkt Dateien hochladen
- `.htaccess` Konfiguration beachten

---

## 📚 Wichtige Dokumentationsreferenzen

### Bei visuellen/UI-Änderungen:
- **Design System:** `babixgo.de/Docs/DESIGN_SYSTEM.md`
- **H2-Überschriften:** `docs/website/H2_UEBERSCHRIFTEN.md`
- **Material Symbols Icons:** In DESIGN_SYSTEM.md dokumentiert

### Bei strukturellen Änderungen:
- **Verbindliche Regeln:** `babixgo.de/Agents.md` ⚠️ **PFLICHTLEKTÜRE**
- **Architektur:** `README.md` (Root)
- **Website-Struktur:** `babixgo.de/README.md`
- **Datei-Inventar:** `docs/project/INVENTORY.md`

### Bei Backend-/Schnittstellenänderungen:
- **Database Quick Reference:** `docs/database/QUICK_REFERENCE.md`
- **Database Schema:** `docs/database/SCHEMA_REQUIREMENTS.md`
- **Migration Guide:** `docs/database/MIGRATION_GUIDE.md`

### Vor Live-Schaltungen:
- **Deployment Guide:** `docs/deployment/DEPLOYMENT_GUIDE.md`
- **Deployment Checklist:** `docs/deployment/DEPLOYMENT_CHECKLIST.md`
- **Security & SEO:** `docs/website/SECURITY_SEO_IMPROVEMENTS.md`

### Bei Testing:
- **Testing Guide:** `docs/website/TESTING_GUIDE.md`
- **Website Audit Report:** `docs/website/WEBSITE-AUDIT-REPORT.md`

### Bei PWA-Features:
- **PWA Documentation:** `babixgo.de/Docs/PWA_DOCUMENTATION.md`

---

## 🎨 Best Practices für dieses Repo

### Code-Style

**PHP:**
```php
// ✅ RICHTIG - Absolute Pfade mit $_SERVER['DOCUMENT_ROOT']
<?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

// ❌ FALSCH - Relative Pfade
<?php require '../partials/header.php'; ?>

// ❌ FALSCH - __DIR__
<?php require __DIR__ . '/../partials/header.php'; ?>
```

**CSS:**
```css
/* ✅ RICHTIG - Design Tokens verwenden */
color: var(--md-primary);
margin: var(--spacing-md);

/* ❌ FALSCH - Hardcoded Values */
color: #6366f1;
margin: 24px;
```

**HTML:**
```html
<!-- ✅ RICHTIG - Eine H1 pro Seite -->
<h1>Hauptüberschrift</h1>

<!-- ✅ RICHTIG - Alt-Attribute bei Bildern -->
<img src="/assets/icons/dice.svg" alt="Würfel-Symbol">

<!-- ❌ FALSCH - Mehrere H1 oder fehlendes alt -->
<h1>Erste Überschrift</h1>
<h1>Zweite Überschrift</h1> <!-- FEHLER! -->
<img src="/icon.png"> <!-- FEHLER! Kein alt -->
```

### Partials-Einbindung (VERPFLICHTEND)

**Reihenfolge in jeder .php Seite:**

```php
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/head-meta.php'; ?>

    <!-- Seitenindividuell -->
    <title>Seitentitel</title>
    <meta name="description" content="Beschreibung">
    <link rel="canonical" href="https://www.babixgo.de/seite/">

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/head-links.php'; ?>
</head>
<body>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/tracking.php'; ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/cookie-banner.php'; ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

    <main>
        <!-- Seiteninhalt -->
    </main>

    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/footer.php'; ?>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/footer-scripts.php'; ?>
</body>
</html>
```

### Ordnerregeln (STRIKT)

| Ordner | Zweck | Regel |
|--------|-------|-------|
| `/weg/` | Archiv (alt) | ❌ Nicht ändern, nicht referenzieren |
| `/add/` | Evtl. zukünftige Nutzung | ⚠️ Bei Nutzung → in korrekten Ordner verschieben |
| `/examples/` | Referenz/Beispiele | ℹ️ Nicht produktiv |
| `/to-do/` | Ideen/Planung | ℹ️ Nicht produktiv |
| `/templates/` | Kopier-Vorlagen | ℹ️ Nur als Basis, nicht verlinken |

### Testanforderungen

**Vor jedem Commit:**
- [ ] Browser-Konsole ohne Errors
- [ ] Mobile Ansicht prüfen
- [ ] Keine kaputten Links (404)
- [ ] Tracking & Consent testen
- [ ] PWA funktioniert (Service Worker)

**Vor Deployment:**
- [ ] Vollständige `DEPLOYMENT_CHECKLIST.md` abarbeiten
- [ ] Cross-Browser-Testing (Chrome, Firefox, Safari)
- [ ] Performance-Check (PageSpeed Insights)

### Commit-Nachrichten-Konventionen

```bash
# Format: <type>: <description>

# Types:
feat: Neue Feature (z.B. "feat: Add dice boost payment flow")
fix: Bugfix (z.B. "fix: Correct session sharing across domains")
docs: Dokumentation (z.B. "docs: Update DESIGN_SYSTEM.md")
style: CSS/Design (z.B. "style: Adjust button spacing")
refactor: Code-Refactoring (z.B. "refactor: Simplify download handler")
perf: Performance (z.B. "perf: Optimize service worker caching")
test: Testing (z.B. "test: Add user authentication tests")
chore: Maintenance (z.B. "chore: Clean up old files")
```

### Git-Workflow

**Branch-Strategie:**
```bash
# Hauptbranch
main / master

# Feature-Branches (von claude/* Branch entwickeln)
claude/<feature-name>-<session-id>

# Beispiel
claude/analyze-project-architecture-6DqO9
```

**Push-Requirements:**
- Branch MUSS mit `claude/` beginnen
- Branch MUSS mit Session-ID enden
- Nur mit `-u origin <branch-name>` pushen
- Bei 403 Fehler: Branch-Namen prüfen

---

## 🎯 Häufige Problembereiche

### 1. Session-Management

**Problem:** Sessions funktionieren nicht über alle Bereiche

**Lösung:**
- Cookie-Domain prüfen: `.babixgo.de` (mit Punkt!)
- `shared/config/session.php` checken
- HTTPS muss aktiv sein

### 2. Partials nicht gefunden

**Problem:** 404 Fehler bei Partials

**Lösung:**
```php
// Korrekte Pfade:
$_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'  // ✅
$_SERVER['DOCUMENT_ROOT'] . '/shared/partials/header.php'  // ❌ FALSCH für babixgo.de
```

**Hinweis:** `babixgo.de/` hat eigene `/partials/`, nicht in `/shared/`!

### 3. Download-Portal zeigt 403

**Problem:** Downloads nicht verfügbar

**Lösung:**
- **Wenn `/downloads/` direkt:** ✅ RICHTIG - das ist gewollt (Security)
- Downloads NUR über `/files/download.php?id=X&type=Y`
- `.htaccess` in `/downloads/` prüfen

### 4. PWA installiert sich nicht

**Problem:** "Add to Home Screen" erscheint nicht

**Lösung:**
- HTTPS aktiv? (PWA erfordert HTTPS)
- `manifest.json` valide JSON?
- Service Worker registriert? (Browser DevTools → Application)
- Icon-Pfade korrekt in manifest.json?

### 5. CSS/JS nicht geladen

**Problem:** Styles fehlen

**Lösung:**
- Cache-Busting prüfen: `?v=<?php echo BABIXGO_VERSION; ?>`
- Pfade korrekt: `/assets/css/style.css`
- Browser-Cache leeren

---

## 🔐 Sicherheits-Checkliste

**Bei Code-Änderungen immer prüfen:**

- [ ] **SQL Injection:** Nur PDO Prepared Statements
- [ ] **XSS:** Output mit `htmlspecialchars()` escapen
- [ ] **CSRF:** Token in Formularen
- [ ] **Path Traversal:** `realpath()` bei File-Operationen
- [ ] **Open Redirect:** URL-Validierung bei Redirects
- [ ] **Credentials:** Keine Klartext-Passwörter im Code
- [ ] **Environment Variables:** Secrets in `.env`, NICHT in Git

**Niemals committen:**
- `.env` mit echten Credentials
- Datenbank-Dumps mit User-Daten
- API-Keys
- SMTP-Passwörter

---

## 📖 Workflow-Beispiele

### Beispiel 1: Neues Monopoly GO Service hinzufügen

```bash
1. Agents.md lesen (Regeln verstehen)
2. Neue Seite in /angebote/ erstellen
3. Partials korrekt einbinden (siehe Template)
4. Design Tokens verwenden (DESIGN_SYSTEM.md)
5. WhatsApp CTA hinzufügen (+49-152-23842897)
6. Mobile testen
7. Commit: "feat: Add tycoon racers service page"
8. Push zu claude/<feature>-<id>
```

### Beispiel 2: Bugfix in Download-Portal

```bash
1. Fehler reproduzieren
2. Error Logs prüfen
3. Relevante Datei öffnen (z.B. /files/download.php)
4. Database.php Klasse prüfen (shared/classes/)
5. Fix implementieren
6. Testen (Download ausführen)
7. Commit: "fix: Correct download counter increment"
8. Push
```

### Beispiel 3: Design-Anpassung

```bash
1. DESIGN_SYSTEM.md öffnen
2. Bestehende Tokens prüfen
3. NUR in assets/css/style.css ändern
4. KEINE Inline-Styles
5. Mobile + Desktop testen
6. Browser-Konsole prüfen
7. Commit: "style: Adjust service card spacing"
8. Push
```

---

## 🆘 Bei Problemen

### Debug-Reihenfolge:

1. **Browser Console öffnen** (F12)
   - JavaScript Errors?
   - Network-Fehler (404, 500)?

2. **PHP Error Logs prüfen**
   - FTP: `/error_log`
   - Strato Customer Portal → Error Logs

3. **Dokumentation konsultieren:**
   - `babixgo.de/Agents.md` - Regeln
   - `README.md` - Architektur
   - Spezifische Guides in `docs/`

4. **Git History prüfen:**
   ```bash
   git log --oneline -20  # Letzte 20 Commits
   git diff HEAD~1       # Letzte Änderung
   ```

5. **Frage stellen:**
   - GitHub Issues erstellen
   - Kontext mitliefern (Error Logs, Screenshots)

---

## 📞 Kontakt & Support

**WhatsApp Business:** +49-152-23842897
**E-Mail:** info@babixgo.de
**Website:** https://www.babixgo.de/kontakt/
**GitHub:** https://github.com/Orga-bgo/babixgo

---

## 🎓 Leitsatz

> **Zentrale Stellen nutzen. Keine Duplikate. Struktur respektieren.**

---

**Viel Erfolg beim Entwickeln! 🚀**
