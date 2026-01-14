# babixGO.de

Statische Website für **babixGO.de** – bewusst ohne Build-Prozess, Frameworks oder Abhängigkeiten.

Dieses Repository ist so aufgebaut, dass alle Dateien **direkt deploybar** sind und langfristig wartbar bleiben.

---

## Überblick

* Reine **HTML / CSS / JavaScript** Website
* Keine Build-Tools, kein Node, kein Bundler
* Wiederverwendbare Inhalte über **serverseitige PHP-Partials**
* Deployment per **FTP / SFTP (Strato Webhosting)**

Die Website ist modular organisiert, um Duplikate zu vermeiden und Änderungen zentral durchführen zu können.

---

## Wichtige Dokumente

* **Agents.md**
  Verbindliche Regeln, Strukturvorgaben und Arbeitsweisen.
  → **Pflichtlektüre vor jeder Änderung**.

* **DESIGN_SYSTEM.md**
  Brand-Guide, Token-Übersicht, Utility- & Component-Styles, Inline-Style-Policy, Audit & Governance.

* **README.md** (dieses Dokument)
  Überblick, Orientierung und Einstieg ins Projekt.

---

## Website-Analyse (Kurzfazit)

Die vollständige Analyse inkl. Tabellenübersicht liegt in:

* `website-analyse.md`

Kurzfazit:

* Fehlende H1-Struktur auf: `/anleitungen/`, `/anleitungen/freundschaftsbalken-fuellen/`, `/impressum/`, `/datenschutz/`.
* Fehlende `alt`-Attribute bei Bildern auf: `/`, `/anleitungen/`, `/anleitungen/freundschaftsbalken-fuellen/`.

---

## Kontakt-Audit (Kurzfazit)

* Kontaktseite nutzt die verpflichtenden Head-Partials und ist CSP-ready (keine Inline-Skripte).
* Formular hat Non-JS-Fallback (Redirect-Status) und JS-Enhancement (JSON).
* Backend liest DB-Zugang über ENV (`BABIXGO_DB_HOST`, `BABIXGO_DB_NAME`, `BABIXGO_DB_USER`, `BABIXGO_DB_PASS`).
* Kontakt-Backend filtert Header-CRLF und schützt mit Honeypot + Rate-Limit.
* Kontakt-spezifische Brand-Farben sind als Tokens dokumentiert.

---

## Projektstruktur (Kurzüberblick)

```
/
├─ partials/        # Zentrale PHP-Partials (Header, Footer, Meta, Tracking)
├─ assets/          # CSS, JS, Bilder, Fonts
├─ templates/       # Vorlagen für neue Seiten (nicht produktiv, nicht verlinken)
├─ add/             # Zukünftige / geplante Dateien (nicht produktiv)
├─ weg/             # Archiv alter, nicht mehr genutzter Dateien
├─ examples/        # Referenz- & Beispielseiten
├─ to-do/           # Aufgaben, Ideen, Planung
├─ index.php        # Einstiegspunkt
└─ *.php            # Weitere produktive Seiten
```

Details und verbindliche Regeln zur Struktur sind in **Agents.md** dokumentiert.

---

## Design-System Governance (Kurz)

* **Single Source of Truth:** `assets/css/style.css`
* **Keine Inline-Styles** in produktiven Templates
* **Tokens statt Hardcodes** (Farben, Spacing, Typo)
* Review-Checkliste und Inline-Inventory: siehe **DESIGN_SYSTEM.md**

---

## Aktuelle Governance-Updates (Kurz)

* Globaler **Zoom-Scale** und Layout-Scaler eingeführt (`--zoom-scale`, Layout/Spacing-Tokens).
* Mobile-Menü **Push-Down** statt Overlay, plus Menülink `/kontakt/`.
* Footer auf **Impressum/Datenschutz/AGB/Kontakt** konsolidiert.
* Inline-Skripte und JSON-LD in produktiven Templates entfernt → `assets/js/main.js` und `assets/js/structured-data/*`.
* Icon-System konsolidiert: produktive Templates nutzen ausschließlich `assets/icons/*`.
* Warning-Box (Error) als Box-Komponente dokumentiert und token-basiert umgesetzt.

Details siehe **Agents.md** (Change-Log) und **DESIGN_SYSTEM.md** (Governance/Inventare).

---

## Partials & Wiederverwendung

Gemeinsame Bestandteile der Website werden über PHP-Partials eingebunden, u. a.:

* Meta-Basis
* CSS- & Asset-Links
* Tracking (Google / Meta etc.)
* Cookie-Banner
* Header & Footer
* Globale Skripte

Dadurch gilt:

* Änderungen an Navigation, Tracking oder Meta-Daten erfolgen **an einer Stelle**
* Keine Duplikate in einzelnen Seiten

Die genaue Einbindereihenfolge und die Regeln pro Partial sind in **Agents.md** festgelegt.

---

## Lokale Entwicklung

* Es gibt **keinen Installationsschritt**
* Seiten können über einen lokalen PHP-Server betrachtet werden (empfohlen)

Beispiel:

```
php -S localhost:8000
```

Anschließend im Browser öffnen:

```
http://localhost:8000
```

(Hintergrund: PHP-Partials müssen serverseitig interpretiert werden.)

---

## Neue Seiten anlegen

Kurzfassung:

1. Bestehende Seite kopieren
2. Inhalt anpassen
3. PHP-Partials unverändert eingebunden lassen
4. Pflicht-Meta-Tags setzen (Title, Description, Canonical)

Keine neuen globalen CSS- oder JS-Dateien anlegen.

---

## Deployment (Strato)

* Upload per **FTP / SFTP**
* Dateien direkt ins Webroot kopieren
* Kein Build, kein CI/CD
* Alte Dateien ggf. nach `/weg/` verschieben (nicht löschen)

Nach dem Upload prüfen:

* Seite lädt korrekt
* Navigation funktioniert
* Cookie-Banner & Tracking aktiv

---

## Nicht‑produktive Ordner

* `/weg/`
  Archiv alter Dateien, nicht mehr genutzt

* `/add/`
  Dateien für mögliche zukünftige Nutzung
  → Wenn Inhalte daraus produktiv werden, müssen sie korrekt einsortiert und aus `/add/` entfernt werden

* `/examples/`, `/to-do/`
  Referenz, Planung, Dokumentation

Details und verbindliche Regeln siehe **Agents.md**.

---

## Progressive Web App (PWA)

**babixGO.de** ist als Progressive Web App (PWA) verfügbar und kann auf Mobilgeräten und Desktop-Computern installiert werden.

### Was ist eine PWA?

Eine PWA kombiniert das Beste aus Websites und nativen Apps:

* **Installation auf dem Gerät** ohne App Store
* **Offline-Funktionalität** durch intelligentes Caching
* **App-ähnliches Erlebnis** im Vollbildmodus
* **Automatische Updates** bei jedem Besuch
* **Schnellere Ladezeiten** durch gecachte Ressourcen

### PWA installieren

#### Android (Chrome, Edge, Samsung Internet)

1. Öffne **https://babixgo.de** im Browser
2. Tippe auf das **Menü** (⋮) und wähle **"Zum Startbildschirm hinzufügen"** oder **"App installieren"**
3. Bestätige mit **"Hinzufügen"** oder **"Installieren"**
4. Die App erscheint auf dem Startbildschirm

#### iOS (Safari)

1. Öffne **https://babixgo.de** in Safari
2. Tippe auf das **Teilen-Symbol** (□↑)
3. Scrolle nach unten und wähle **"Zum Home-Bildschirm"**
4. Tippe auf **"Hinzufügen"**
5. Die App erscheint auf dem Home-Bildschirm

#### Desktop (Chrome, Edge)

1. Öffne **https://babixgo.de** im Browser
2. Klicke auf das **Install-Symbol** (⊕) in der Adressleiste
3. Oder: Menü → **"babixGO installieren"**
4. Bestätige mit **"Installieren"**
5. Die App öffnet sich in einem eigenen Fenster

### Offline-Nutzung

Nach der Installation sind folgende Funktionen offline verfügbar:

* Zugriff auf bereits besuchte Seiten
* Anzeige gecachter Inhalte
* Offline-Hinweisseite mit Cache-Status

### APK für Android erstellen (Trusted Web Activity)

Für fortgeschrittene Nutzer: Eine Android-APK kann über **TWA (Trusted Web Activity)** erstellt werden, **ohne** Node.js oder Build-Tools:

1. **PWABuilder** nutzen: [https://www.pwabuilder.com](https://www.pwabuilder.com)
2. URL eingeben: `https://babixgo.de`
3. PWA analysieren lassen
4. **"Package for Stores"** → **Android** auswählen
5. APK herunterladen und signieren

Alternativ: **Bubblewrap CLI** (erfordert Java, nicht Node.js):
```bash
npx @bubblewrap/cli init --manifest https://babixgo.de/public/manifest.json
npx @bubblewrap/cli build
```

Hinweis: APK-Export ist **nicht Teil des Projekt-Workflows**, da es Build-Tools erfordert. Nutzer können dies extern durchführen.

---

## Mitwirken / Änderungen

Dieses Projekt folgt klaren Regeln zur Konsistenz und Wartbarkeit.

Vor Änderungen:

* **Agents.md lesen**
* Struktur respektieren
* Zentrale Stellen nutzen

---

## Leitsatz

> **Einfach halten. Zentral pflegen. Keine Duplikate.**
