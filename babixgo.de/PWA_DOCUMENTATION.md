# PWA (Progressive Web App) Dokumentation

## Überblick

**babixGO.de** wurde als Progressive Web App (PWA) implementiert, um ein app-ähnliches Erlebnis auf allen Geräten zu bieten, ohne Build-Prozess oder Abhängigkeiten.

---

## Implementierung

### Architektur

Die PWA-Implementierung folgt dem **Zero-Dependency-Prinzip** des Projekts:

* **Kein Node.js** erforderlich
* **Kein Build-Prozess** notwendig
* **Reines JavaScript/PHP** ohne Frameworks
* **Direktes Deployment** per SFTP wie gewohnt

### Komponenten

#### 1. Web App Manifest (`/public/manifest.json`)

Das Manifest definiert die App-Eigenschaften:

```json
{
  "name": "babixGO - Monopoly GO Services",
  "short_name": "babixGO",
  "start_url": "/",
  "display": "standalone",
  "theme_color": "#1a1a1a",
  "background_color": "#1a1a1a"
}
```

**Eigenschaften:**
- `name`: Vollständiger App-Name
- `short_name`: Name für den Startbildschirm (max. 12 Zeichen)
- `start_url`: URL beim App-Start
- `scope`: Begrenzt Navigation innerhalb der App
- `display`: "standalone" = vollbild ohne Browser-UI
- `orientation`: "portrait-primary" = bevorzugt Hochformat
- `theme_color`: Farbe der Browser-UI (Statusleiste)
- `background_color`: Hintergrund beim Laden
- `icons`: Array von Icons in verschiedenen Größen

#### 2. Service Worker (`/public/sw.js`)

Der Service Worker ermöglicht Offline-Funktionalität und Performance-Verbesserungen.

**Caching-Strategien:**

| Asset-Typ | Strategie | Beschreibung |
|-----------|-----------|--------------|
| CSS, JS, Bilder, Fonts | **Cache-First** | Lädt aus Cache, falls vorhanden; sonst Netzwerk |
| HTML, PHP | **Network-First** | Versucht Netzwerk; falls offline, nutzt Cache |
| Offline-Fallback | **Cache-Only** | Zeigt `/offline.html` wenn keine Verbindung |

**Lifecycle:**

1. **Install**: Precache kritischer Assets (/, /offline.html, CSS, JS, Logo)
2. **Activate**: Entfernt alte Caches, übernimmt Kontrolle
3. **Fetch**: Intercepted Requests, wendet Caching-Strategien an

**Cache-Verwaltung:**

```javascript
const CACHE_NAME = 'babixgo-v1'; // Bei Änderungen Version erhöhen
```

Bei Änderung der Version:
- Alte Caches werden automatisch gelöscht
- Neue Assets werden neu gecacht
- Nutzer erhalten Updates beim nächsten Besuch

#### 3. Offline-Seite (`/offline.html`)

Standalone-HTML-Seite ohne PHP-Abhängigkeiten:

* **Inline-Styles**: Funktioniert ohne externe CSS
* **Dark Theme**: Konsistent mit dem Design-System
* **Cache-Liste**: Zeigt verfügbare gecachte Seiten
* **Auto-Reconnect**: Lädt Seite neu, sobald Verbindung besteht

#### 4. PWA Icons (`/assets/img/pwa/`)

Generiert aus dem bestehenden Logo:

* **icon-192x192.png**: Mindestgröße für PWA (Android)
* **icon-512x512.png**: Hochauflösende Version (Splash Screen)

**Purpose**: "any maskable" = funktioniert mit und ohne Maske

#### 5. PWA Integration (PHP Partials)

**`/partials/head-links.php`:**

```php
<!-- PWA Manifest -->
<link rel="manifest" href="/public/manifest.json" />
<meta name="theme-color" content="#1a1a1a" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="babixGO" />
<link rel="apple-touch-icon" href="/assets/img/pwa/icon-192x192.png" />
```

Eingebunden in **alle Seiten** über die Standard-Partial-Struktur.

#### 6. Service Worker Registration (`/assets/js/main.js`)

```javascript
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/public/sw.js')
      .then(registration => console.log('SW registered'))
      .catch(error => console.log('SW registration failed'));
  });
}
```

**Features:**
- Automatische Registrierung beim Seitenload
- Install-Prompt-Handling (`beforeinstallprompt`)
- Online/Offline-Status-Detection
- Optional: Custom Install Button Support

---

## Features

### ✅ Installierbarkeit

**Desktop:**
- Install-Icon in der Adressleiste (Chrome, Edge)
- Menü → "babixGO installieren"
- Öffnet in eigenständigem Fenster

**Mobile:**
- "Add to Home Screen" (iOS Safari)
- "Install app" / "Add to Home screen" (Android)
- Icon auf dem Startbildschirm
- Vollbild-Modus ohne Browser-UI

### ✅ Offline-Funktionalität

**Was funktioniert offline:**
- Alle zuvor besuchten Seiten
- Gecachte CSS, JavaScript, Bilder
- Logo und Icons
- Offline-Fallback-Seite

**Was nicht offline funktioniert:**
- Neue, unbesuchte Seiten
- Formular-Übermittlungen
- Dynamische Inhalte vom Server
- WhatsApp/Facebook-Links (erfordern Netzwerk)

### ✅ Performance

**Verbesserungen:**
- **Schnellere Ladezeiten** durch Asset-Caching
- **Reduzierte Server-Last** (statische Assets aus Cache)
- **Bessere Mobile Experience** (app-ähnlich)
- **Sofortiges Laden** bei wiederholten Besuchen

### ✅ App-ähnliches Erlebnis

- Vollbild-Modus (kein Browser-UI)
- Eigenes App-Icon
- Theme-Farbe in Statusleiste
- Splash Screen beim Start (Android)
- Separates App-Fenster (Desktop)

---

## Browser-Support

| Browser | Installation | Service Worker | Offline | Bewertung |
|---------|--------------|----------------|---------|-----------|
| Chrome (Desktop/Android) | ✅ | ✅ | ✅ | **Volle Unterstützung** |
| Edge (Desktop/Android) | ✅ | ✅ | ✅ | **Volle Unterstützung** |
| Safari (iOS) | ✅ | ✅ | ✅ | **Volle Unterstützung** |
| Firefox (Desktop) | ⚠️ | ✅ | ✅ | Service Worker ja, Installation eingeschränkt |
| Samsung Internet | ✅ | ✅ | ✅ | **Volle Unterstützung** |

**Minimum-Support**: Alle modernen Browser mit Service Worker API

---

## Deployment

### 1. Normale Deployment-Schritte

PWA-Features sind vollständig integriert und erfordern **keine zusätzlichen Schritte**:

```bash
# Wie gewohnt per SFTP/GitHub Actions
git push origin main
# → Automatisches Deployment via .github/workflows/main.yml
```

Alle PWA-Dateien werden automatisch mit deployed:
- `/public/manifest.json`
- `/public/sw.js`
- `/offline.html`
- `/assets/img/pwa/*.png`

### 2. HTTPS erforderlich

Service Worker funktionieren **nur über HTTPS** (Ausnahme: localhost):

* ✅ Production: `https://babixgo.de` (bereits HTTPS)
* ✅ Lokal: `http://localhost:8000` (erlaubt)
* ❌ HTTP-Only: Service Worker wird nicht registriert

### 3. Cache-Invalidierung

Bei Änderungen an gecachten Dateien:

**Option A: Cache-Version erhöhen** (empfohlen)

```javascript
// In /public/sw.js
const CACHE_NAME = 'babixgo-v2'; // v1 → v2
```

**Option B: CSS-Versions-Parameter** (bereits implementiert)

```php
// /partials/version.php
define('BABIXGO_VERSION', '2.0.1'); // Version erhöhen
```

**Option C: Browser-Cache-Clearing** (Nutzer)

Nutzer können Browser-Cache manuell löschen: Settings → Clear browsing data → Cached images and files

---

## APK-Export (Trusted Web Activity)

### Warum kein Capacitor im Repository?

**Capacitor erfordert:**
- Node.js und npm
- Android SDK / Xcode
- Gradle / CocoaPods
- Build-Prozess

**Projektprinzipien:**
- ✅ Kein Node.js
- ✅ Keine Build-Tools
- ✅ Kein Dependency Management

→ **APK-Export ist nicht Teil des Core-Projekts**

### Alternative: TWA (Trusted Web Activity)

Nutzer und Fortgeschrittene können **extern** eine APK erstellen:

#### Option 1: PWABuilder (Empfohlen, No-Code)

1. Besuche: [https://www.pwabuilder.com](https://www.pwabuilder.com)
2. Gib URL ein: `https://babixgo.de`
3. Klicke **"Start"** → PWA wird analysiert
4. Wähle **"Package for Stores"** → **Android**
5. Konfiguriere Optionen:
   - Package ID: `de.babixgo.app`
   - App Name: `babixGO`
   - Version: `1.0.0`
6. Klicke **"Generate"**
7. Lade APK herunter
8. Signiere APK (für Play Store)

**Vorteile:**
- Keine Installation erforderlich
- GUI-basiert, einfach
- Automatische TWA-Konfiguration

#### Option 2: Bubblewrap CLI (Fortgeschritten)

**Voraussetzungen:**
- Java JDK 8+
- Android SDK
- Node.js (nur für Bubblewrap, nicht im Projekt selbst)

**Installation:**

```bash
npm install -g @bubblewrap/cli
```

**APK erstellen:**

```bash
# Initialisierung
bubblewrap init --manifest https://babixgo.de/public/manifest.json

# Build (erstellt unsigned APK)
bubblewrap build

# Signieren (für Play Store)
# Keystore erstellen (einmalig)
keytool -genkey -v -keystore babixgo-release.keystore -alias babixgo -keyalg RSA -keysize 2048 -validity 10000

# APK signieren
jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore babixgo-release.keystore app-release-unsigned.apk babixgo
```

**Output:** `app-release-signed.apk` bereit für Play Store oder Sideload

#### Option 3: Android Studio (Manuell)

Für vollständige Kontrolle: TWA-Projekt manuell in Android Studio erstellen.

**Siehe:**
- [Android TWA Docs](https://developer.android.com/topic/google-play-instant/getting-started/instant-enabled-app-bundle)
- [TWA Quick Start Guide](https://chromeos.dev/en/publish/pwa-in-play)

### TWA vs. Native App

| Feature | TWA | Native App |
|---------|-----|-----------|
| Code | Web (HTML/CSS/JS) | Java/Kotlin/Swift |
| Updates | Automatisch (Website) | App Store Update |
| Größe | Klein (~1-2 MB) | Groß (10-50 MB+) |
| Entwicklung | Einfach | Komplex |
| Kosten | Gering | Hoch |
| Play Store | ✅ Ja | ✅ Ja |
| Offline | ✅ Ja (Service Worker) | ✅ Ja |

**Vorteil TWA für babixGO:**
- Eine Codebasis für Web + Android
- Updates sofort live ohne App-Store-Review
- Geringer Wartungsaufwand

---

## Wartung

### Service Worker aktualisieren

**Bei Änderungen an Caching-Logik:**

1. Öffne `/public/sw.js`
2. Erhöhe `CACHE_NAME`:
   ```javascript
   const CACHE_NAME = 'babixgo-v2'; // v1 → v2
   ```
3. Optional: Passe `PRECACHE_ASSETS` Array an
4. Speichern und deployen
5. Nutzer erhalten beim nächsten Besuch die neue Version

### Manifest aktualisieren

**Bei Änderungen an App-Eigenschaften:**

1. Öffne `/public/manifest.json`
2. Ändere gewünschte Eigenschaften (Name, Theme-Color, etc.)
3. Speichern und deployen
4. Nutzer, die App bereits installiert haben, sehen Änderungen beim nächsten Start

**Wichtig**: Icons ändern erfordert Neuinstallation der App

### Icons ersetzen

**Bei Logo-Änderung:**

1. Ersetze `/assets/logo/babixGO-logo-hell.png`
2. Generiere neue PWA-Icons:
   ```bash
   convert assets/logo/babixGO-logo-hell.png -resize 192x192 -gravity center -extent 192x192 assets/img/pwa/icon-192x192.png
   convert assets/logo/babixGO-logo-hell.png -resize 512x512 -gravity center -extent 512x512 assets/img/pwa/icon-512x512.png
   ```
3. Optional: Apple-Touch-Icon auch updaten
4. Deployen
5. **Nutzer müssen App neu installieren** für neue Icons

### Offline-Seite anpassen

**Bei Design-Änderungen:**

1. Öffne `/offline.html`
2. Ändere Inline-Styles (kein externes CSS)
3. Behalte funktionale JavaScript-Teile (Online-Listener, Cache-Liste)
4. Speichern und deployen
5. Service Worker cachet neue Version automatisch

---

## Troubleshooting

### Problem: Service Worker registriert nicht

**Symptome:**
- Keine Meldung in Console
- DevTools → Application → Service Workers leer

**Lösungen:**
1. ✅ **HTTPS prüfen**: Nur HTTPS (oder localhost) funktioniert
2. ✅ **Pfad prüfen**: `/public/sw.js` muss erreichbar sein
3. ✅ **Browser-Cache leeren**: Hard Refresh (Ctrl+Shift+R)
4. ✅ **Console prüfen**: Fehler in DevTools → Console
5. ✅ **Browser-Support**: Service Worker API verfügbar?

### Problem: PWA nicht installierbar

**Symptome:**
- Kein Install-Icon in Adressleiste
- Keine "Install"-Option im Menü

**Lösungen:**
1. ✅ **Manifest prüfen**: DevTools → Application → Manifest → Errors
2. ✅ **HTTPS prüfen**: Installation nur über HTTPS
3. ✅ **Icons prüfen**: Mindestens 192x192 Icon erforderlich
4. ✅ **Service Worker**: Muss registriert sein
5. ✅ **Bereits installiert**: App kann nur einmal installiert werden

### Problem: Offline-Seite wird nicht angezeigt

**Symptome:**
- Offline-Modus zeigt "Site can't be reached" statt `/offline.html`

**Lösungen:**
1. ✅ **Offline-Seite besuchen**: Einmal online besuchen zum Cachen
2. ✅ **Service Worker aktiv**: DevTools → SW muss "activated" sein
3. ✅ **Cache prüfen**: DevTools → Application → Cache Storage → babixgo-v1
4. ✅ **Netzwerk-Tab**: Offline-Seite muss im Cache sein

### Problem: Alte Version wird angezeigt

**Symptome:**
- Änderungen nicht sichtbar nach Deployment
- Alte CSS/JS wird geladen

**Lösungen:**
1. ✅ **Cache-Version erhöhen**: `CACHE_NAME` in sw.js erhöhen
2. ✅ **CSS-Version erhöhen**: `BABIXGO_VERSION` in partials/version.php
3. ✅ **Service Worker Update**: DevTools → SW → "Update on reload" aktivieren
4. ✅ **Hard Refresh**: Ctrl+Shift+R (überschreibt Cache)
5. ✅ **skipWaiting**: DevTools → SW → "skipWaiting" klicken

### Problem: APK-Build schlägt fehl

**Symptome:**
- PWABuilder Fehler
- Bubblewrap Build-Fehler

**Lösungen:**
1. ✅ **Manifest validieren**: [https://manifest-validator.appspot.com](https://manifest-validator.appspot.com)
2. ✅ **HTTPS erforderlich**: Manifest muss über HTTPS erreichbar sein
3. ✅ **Icons prüfen**: Alle Icons müssen verfügbar sein (200 OK)
4. ✅ **start_url prüfen**: Muss gültige URL sein
5. ✅ **Java/SDK Version**: Bubblewrap erfordert JDK 8+

---

## Best Practices

### ✅ DO

- **Cache-Version erhöhen** bei Service Worker Änderungen
- **Offline-Seite testen** regelmäßig
- **HTTPS verwenden** (bereits gegeben)
- **Icons optimieren** (minimale Dateigröße)
- **Lighthouse Audits** regelmäßig durchführen
- **Browser-Konsole prüfen** auf SW-Fehler

### ❌ DON'T

- **Service Worker nicht** in `/assets/js/` (muss in Root-Nähe sein)
- **Manifest nicht** dynamisch generieren (statisches JSON bevorzugen)
- **Keine externen Dependencies** (JavaScript-Libraries im SW)
- **Keine sensiblen Daten** im Service Worker cachen
- **Cache nicht** unbegrenzt wachsen lassen (alte Caches löschen)

---

## Lighthouse PWA Checklist

Erforderlich für PWA-Badge:

- [x] Registriert einen Service Worker
- [x] Antwortet mit 200 wenn offline
- [x] Hat ein Web App Manifest
- [x] Nutzt HTTPS (Production)
- [x] Konfiguriert Custom Splash Screen
- [x] Setzt Theme-Color
- [x] Content richtig dimensioniert für Viewport
- [x] Icons sind korrekt dimensioniert

**Aktueller Status**: ✅ Alle PWA-Kriterien erfüllt

---

## Ressourcen

**Dokumentation:**
- [MDN: Progressive Web Apps](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Google PWA Docs](https://web.dev/progressive-web-apps/)
- [Service Worker API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)

**Tools:**
- [Lighthouse](https://developers.google.com/web/tools/lighthouse) - PWA Audits
- [PWABuilder](https://www.pwabuilder.com) - APK Export
- [Manifest Validator](https://manifest-validator.appspot.com) - Manifest prüfen

**TWA:**
- [Bubblewrap](https://github.com/GoogleChromeLabs/bubblewrap) - TWA CLI
- [Android TWA Guide](https://developer.android.com/topic/google-play-instant/getting-started/instant-enabled-app-bundle)

---

**Erstellt**: 2026-01-14  
**Letzte Aktualisierung**: 2026-01-14  
**Version**: 1.0
