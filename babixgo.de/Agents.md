# Agents.md  x

Projekt: **babixGO.de**
Status: **v3 – aktiv**

---

## Zweck

Diese Datei definiert **verbindliche Regeln, Strukturen und Arbeitsweisen** für das Projekt **babixGO.de**.

Sie gilt für:

* Menschen
* KI / Codex / Automatisierungen

Abweichungen sind **nicht erlaubt**, außer sie werden ausdrücklich dokumentiert.

---

## Projektüberblick

* Reine **statische Website**
* Technologien: **HTML, CSS, JavaScript**
* **Kein Build-Tooling**
* Deployment per **FTP/SFTP (Strato Webhosting)**
* Wiederverwendbare Inhalte über **serverseitige PHP-Partials**

---

## Grundprinzipien

* Keine Duplikate
* Zentrale Stellen nutzen
* Klare, wartbare Struktur
* Lieber einfach als clever

---

## Produktive Seiten

* Produktive Seiten liegen im Root oder in Inhaltsordnern
* Produktive Seiten werden als **`.php`** geführt
* Archiv-, Beispiel- oder Planungsdateien sind **nicht produktiv**
* Vorlagen liegen unter `/templates/` und dienen ausschließlich als Kopier-Basis (nicht verlinken, nicht produktiv nutzen)

---

## Verbindliche Ordnerregeln

### `/weg/`

* Enthält **nicht mehr genutzte Dateien**
* Reines Archiv
* **Nicht ändern**
* **Nicht referenzieren**
* **Nicht migrieren**

### `/add/`

* Enthält Dateien, die **evtl. in Zukunft genutzt werden**
* Keine produktiven Referenzen erlaubt
* **Wenn eine Datei aus `/add/` produktiv genutzt wird:**

  * Datei in den korrekten Zielordner verschieben
  * Danach **aus `/add/` entfernen**
* Keine Duplikate zwischen `/add/` und produktiven Ordnern

### Weitere nicht-produktive Ordner

* `/examples/` → Referenz / Beispiele
* `/to-do/` → Ideen, Aufgaben, Planung

Diese Ordner sind **nicht produktiv**.

---

## Partials (verpflichtend)

Partials liegen unter:

```
/partials/
```

Verwendete Partials:

* `head-meta.php`
* `head-links.php`
* `tracking.php`
* `cookie-banner.php` (aktiv)
* `header.php`
* `footer.php`
* `footer-scripts.php`

---

## Einbindung der Partials (verpflichtend)

### PHP-Pattern

In allen Seiten **ausschließlich** dieses Muster verwenden:

```php
<?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/DATEI.php'; ?>
```

* Keine relativen Pfade
* Kein `__DIR__`
* Keine Short-Tags

---

### Verbindliche Einbindereihenfolge

#### Im `<head>`

1. `head-meta.php`
2. Seitenindividuell:

   * `<title>`
   * `<meta name="description">`
   * `<link rel="canonical">`
3. `head-links.php`

#### Direkt nach `<body>`

4. `tracking.php`
5. `cookie-banner.php`
6. `header.php`

#### Am Seitenende (vor `</body>`)

7. `footer.php`
8. `footer-scripts.php`

---

## Regeln pro Partial

### `head-meta.php`

* Gemeinsame Meta-Basis:

  * charset
  * viewport
  * robots (default)
  * OpenGraph / Twitter Basis
* **Keine** seitenindividuellen Inhalte

---

### `head-links.php`

* `style.css`
* Favicons
* Fonts / preload / preconnect
* **Keine Meta-Tags**
* **Keine Scripts**

---

### `tracking.php`

* **Alle** Google / GA / FB Pixel Snippets
* Danach **kein Tracking-Code mehr** in Seiten oder anderen Partials

---

### `cookie-banner.php`

* Consent ist **aktiv**
* Steuert, ob Tracking ausgeführt wird
* Kein direktes Tracking außerhalb von `tracking.php`

---

### `footer-scripts.php`

* Nur globale Scripts
* `assets/js/main.js` (defer)
* **Kein Tracking**

---

### `header.php` / `footer.php`

* Layout, Navigation, Footer-Links
* **Keine Meta-Tags**
* **Keine Scripts**
* **Kein Tracking**

---

## Gemeinsame Assets (verpflichtend)

* CSS: `assets/css/style.css`
* JS: `assets/js/main.js`

Regeln:

* Keine zusätzlichen globalen CSS- oder JS-Dateien
* Keine Inline-Styles (außer technisch zwingend)

---

## HTML-Regeln

* Valides, semantisches HTML
* Genau **eine H1 pro Seite**
* Bilder **immer mit `alt`-Attribut**
* Pflicht-Meta pro Seite:

  * `title`
  * `description`
  * `canonical`
* Links und Buttons semantisch korrekt einsetzen

---

## Accessibility (Minimum)

* Fokus-Stile nicht entfernen
* Logische Überschriften-Hierarchie
* Formulare mit Labels
* Tastatur-Navigation möglich

---

## Qualitätssicherung vor Deployment

* Keine kaputten Links (404)
* Mobile Ansicht prüfen
* Browser-Konsole ohne Errors
* Tracking & Consent testen

---

## Deployment

* FTP / SFTP (Strato)
* Dateien direkt ins Webroot
* Kein Build-Prozess
* Kein CI/CD

---

## Änderungen & Pflege

* Struktur- oder Regeländerungen → **Agents.md anpassen**
* Produktive Übernahmen aus `/add/` → Datei dort entfernen
* Alte Dateien nur nach `/weg/` verschieben, nicht löschen

---

## Design-System / Brand Guide

* Verbindliche Design-Referenz: `DESIGN_SYSTEM.md`
* Änderungen am visuellen System ausschließlich dort dokumentieren.
* Keine Inline-Styles (nur technisch zwingend) – neue Styles zentral in `assets/css/style.css`.

### Design- & CSS-System-Audit (aktueller Stand)

* Produktive Templates enthalten **keine** Inline-Style-Attribute oder Style-Tags.
* Inline-Style-Inventar, Governance, Token-Übersicht und Migrationslisten sind in `DESIGN_SYSTEM.md` dokumentiert.
* Typografie-Defaults (H4–H6, `p`, `small`) wurden zentral ergänzt – neue Typo-Änderungen nur in `assets/css/style.css`.

---

## Website-Analyse (Stand: 2026-01-04)

Die vollständige Analyse inkl. Tabellenübersicht liegt in:

* `website-analyse.md`

Wesentliche Hinweise:

* Fehlende H1-Struktur auf: `/anleitungen/`, `/anleitungen/freundschaftsbalken-fuellen/`, `/impressum/`, `/datenschutz/` (H1-Regel beachten).
* Fehlende `alt`-Attribute bei Bildern auf: `/`, `/anleitungen/`, `/anleitungen/freundschaftsbalken-fuellen/` (Accessibility).

---

## Kontakt-Audit (Kurznotizen)

* `/kontakt/index.php` bindet nicht das verpflichtende `head-meta.php` / `head-links.php` ein und enthält Inline-Skripte inkl. doppelter `footer-scripts.php`-Einbindung.
* Fehlende H1-Überschrift und kein `<main>`-Wrapper in der Kontaktseite.
* Cookie-Banner und Tracking basieren auf Inline-JS und Inline-Handlern (CSP-Verstoß bei „kein unsafe-inline“).
* Kontaktformular ist JS-only (JSON-POST) ohne Fallback für `application/x-www-form-urlencoded`.
* `kontakt/contact.php` enthält Klartext-DB-Credentials, sehr offene CORS-Policy und keine Rate-Limits/Spam-Checks.
* Kontakt-CSS nutzt Hardcodes (z. B. `#25D366`, `#1877F2`) statt Design-Tokens.

---

## Kontakt-Umsetzung (Stand: 2026-01-04)

* `/kontakt/` nutzt die verpflichtenden Head-Partials und ist CSP-ready (keine Inline-`<script>`/`onclick`).
* Kontakt-Form-Logik, Consent und Tracking werden zentral in `assets/js/main.js` gebunden.
* Formular-Backend erwartet DB-Credentials via ENV (`BABIXGO_DB_HOST`, `BABIXGO_DB_NAME`, `BABIXGO_DB_USER`, `BABIXGO_DB_PASS`) und hat Honeypot + Rate-Limit.
* Plattformfarben (WhatsApp/Facebook) sind als Tokens in `assets/css/style.css` dokumentiert.
* Admin-UI unter `/kontakt/admin/` bleibt getrennt von öffentlichen Partials.

---

## Änderungslog (Governance & UI)

**2026-01-05 – Governance-Refresh & UI-Scale**
- Globaler Zoom-Scale eingeführt (`--zoom-scale`, Layout- & Spacing-Scaler) und Containerbreiten angepasst.
- Header-Menü um `/kontakt/` ergänzt und Mobile-Menü auf Push-Down-Verhalten umgestellt.
- Footer: WhatsApp/Facebook entfernt, Kontakt-Link ergänzt.
- Startseite: Kontaktbox auf WhatsApp-Link `/kontakt/` reduziert, Services-Wrapper entfernt, Button-Text „Mehr Infos“.
- Icons: Inline-SVGs in produktiven Templates durch `assets/icons/*` ersetzt; neue Icons `whatsapp.svg`, `facebook.svg`, `mail.svg`.
- Inline-Skripte/JSON-LD in produktiven Templates entfernt und in `assets/js/main.js` sowie `assets/js/structured-data/*` ausgelagert.
- Governance-Dokumentation (`DESIGN_SYSTEM.md`) aktualisiert (Inventare, Tokens, CSP-Empfehlung).

**2026-01-11 – Replit-Setup & Audit-Fixes**
- Replit-Environment mit PHP 8.4 eingerichtet.
- Projekt-Audit gemäß `website-analyse.md` abgeschlossen:
  - Fehlende H1-Struktur auf `/anleitungen/`, `/anleitungen/freundschaftsbalken-fuellen/`, `/impressum/` und `/datenschutz/` verifiziert und sichergestellt.
  - Alt-Attribute für Icons auf allen produktiven Seiten ergänzt/präzisiert für bessere Accessibility und SEO.
  - Mobile Menu Alt-Texte für Social Icons ergänzt.

**2026-01-12 – Material Symbols & SEO-Audit**
- Material Symbols Icon-System für h2-Überschriften implementiert (30+ Icons, 7 Farbkategorien).
- Sitemap.xml mit lastmod-Datum aktualisiert.
- FAQ-Schema (Schema.org) für `/partnerevents/` und `/wuerfel/` hinzugefügt.
- Umfassender Website-Audit-Report erstellt (`WEBSITE-AUDIT-REPORT.md`).
- Meta-Descriptions auf 150-160 Zeichen optimiert.
- WhatsApp-Klick-Tracking implementiert (GA4 + FB Pixel).

**2026-01-12 – PageSpeed Optimierung**
- robots.txt bereinigt und AI-Bots blockiert (GPTBot, CCBot, etc.).
- Critical CSS inline implementiert (`partials/critical-css.php`).
- CSS/Fonts async Loading via preload-Pattern.
- Resource Hints (dns-prefetch, preconnect) hinzugefügt.

**2026-01-14 – Progressive Web App (PWA) Integration**
- PWA-Funktionalität ohne Build-Prozess implementiert (Zero-Dependency-Prinzip).
- Web App Manifest erstellt (`/public/manifest.json`) mit App-Metadaten und Icons.
- Service Worker implementiert (`/public/sw.js`) mit Cache-First (Assets) und Network-First (HTML) Strategien.
- Offline-Fallback-Seite erstellt (`/offline.html`) mit Dark Theme und Cache-Liste.
- PWA-Icons generiert (192x192, 512x512) aus bestehendem Logo.
- PWA-Meta-Tags zu `partials/head-links.php` hinzugefügt (Manifest, Apple PWA Support).
- Service Worker Registrierung in `assets/js/main.js` implementiert mit Install-Prompt und Online/Offline-Detection.
- Umfassende PWA-Dokumentation erstellt (`PWA_DOCUMENTATION.md`).
- PWA-Testing-Guide zu `TESTING_GUIDE.md` hinzugefügt.
- README.md erweitert mit PWA-Installations-Anleitung und TWA-APK-Export-Optionen.

---

## Leitsatz

> **Zentrale Stellen nutzen. Keine Duplikate. Struktur respektieren.**
