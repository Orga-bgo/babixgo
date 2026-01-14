# babixGO.de – Brand Guide & Design System

> **Quelle:** Dieses Dokument basiert ausschließlich auf den im Repository definierten Styles (vor allem `assets/css/style.css`) und eingebundenen Fonts (`partials/head-links.php`). Keine Werte wurden angenommen.

---

## 1. Brand-Überblick

**Markenname & Wortmarke**
- Wortmarke: **babixGO** ("babix" + "GO").
- Farbtrennung der Wortmarke: `.logo-babix` (gedämpfter Textton), `.logo-go` (Primary).【F:assets/css/style.css†L348-L355】
- Headline-Familie: **Montserrat** (für Headings und Logos).【F:assets/css/style.css†L104-L133】【F:assets/css/style.css†L184-L193】

**Brand-Basis**
- Design-Grundlage laut CSS-Kommentar: **Material Design 3 · Dark Medium Contrast**.【F:assets/css/style.css†L1-L2】
- Dunkler Gesamtauftritt: `--md-background` und `--md-surface` liegen beide bei `rgb(16 20 23)`.【F:assets/css/style.css†L30-L36】
- Cards/Container nutzen abgestufte Surface-Tokens (`--md-surface-container-low`, `--md-surface-container`, `--md-surface-container-high`).【F:assets/css/style.css†L36-L42】
- Schatten-Ebenen sind als `--shadow-1` bis `--shadow-3` definiert und werden z. B. in Section-Cards und Modals genutzt.【F:assets/css/style.css†L95-L97】【F:assets/css/style.css†L371-L384】【F:assets/css/style.css†L1034-L1041】

---

## 2. Typografie-System

**Schriften (laut Font-Import)**
- **Primär**: Inter (Gewichte 400, 500, 600).【F:partials/head-links.php†L1-L4】
- **Sekundär**: Montserrat (Gewicht 700).【F:partials/head-links.php†L1-L4】
- **Fallback**: `sans-serif` für beide Familien.【F:assets/css/style.css†L93-L100】【F:assets/css/style.css†L104-L133】

### Typografie-Variablen
- `--font-size-h1: 2rem`
- `--font-size-h2: 1.5rem`
- `--font-size-h3: 1.2rem`
- `--font-size-body: 1rem`
- `--font-size-small: 0.9rem`
- `--font-size-xs: 0.8rem`【F:assets/css/style.css†L48-L54】

### Text-Elemente (Tabelle)

> **Hinweis:** Wenn ein Element **keine eigene CSS-Regel** im Repository hat, wird dies explizit vermerkt (keine Annahmen).

| Element | Schriftart | Größe | Gewicht | Zeilenhöhe | Letter-Spacing | Textfarbe | Quelle/Kommentar |
|---|---|---|---|---|---|---|---|
| **H1** | Montserrat | `var(--font-size-h1)` | 600 | 1.2 | nicht definiert | `var(--md-primary)` | H1-Regel definiert.【F:assets/css/style.css†L104-L112】 |
| **H2** | Montserrat | `var(--font-size-h2)` | 600 | 1.3 | nicht definiert | `var(--md-secondary)` | H2-Regel definiert. Icon wird über .section-title Wrapper gesetzt.【F:assets/css/style.css†L166-L173】【F:assets/css/style.css†L201-L218】 |
| **H3** | Montserrat | `var(--font-size-h3)` | 600 | 1.4 | nicht definiert | `var(--md-secondary)` | H3-Regel definiert. Hat gradient underline via h3::after.【F:assets/css/style.css†L176-L185】【F:assets/css/style.css†L187-L199】 |
| **H4** | Montserrat | `var(--font-size-body)` | 600 | 1.4 | nicht definiert | `var(--md-primary-fixed-dim)` | H4-Regel ergänzt.【F:assets/css/style.css†L136-L143】 |
| **H5** | Montserrat | `var(--font-size-small)` | 600 | 1.4 | nicht definiert | `var(--text)` | H5-Regel ergänzt.【F:assets/css/style.css†L145-L152】 |
| **H6** | Montserrat | `var(--font-size-xs)` | 600 | 1.4 | nicht definiert | `var(--muted)` | H6-Regel ergänzt.【F:assets/css/style.css†L154-L161】 |
| **Paragraph (p)** | Inter | `var(--font-size-body)` | *inherit* | 1.6 (Body) | *nicht definiert* | `var(--text)` | Globale p-Regel ergänzt.【F:assets/css/style.css†L93-L100】【F:assets/css/style.css†L163-L171】 |
| **Small (small)** | Inter | `var(--font-size-small)` | *inherit* | *inherit* | *nicht definiert* | `var(--muted)` | Small-Element-Regel ergänzt.【F:assets/css/style.css†L173-L176】 |
| **Links (normal)** | Inter (inherit) | *inherit* | *inherit* | *inherit* | *nicht definiert* | `var(--primary)` | Basis-Linkregel.【F:assets/css/style.css†L742-L748】 |
| **Links (hover)** | Inter (inherit) | *inherit* | *inherit* | *inherit* | *nicht definiert* | `var(--accent)` | Hover-Regel für Links.【F:assets/css/style.css†L750-L753】 |
| **Links (active)** | *nicht definiert* | *nicht definiert* | *nicht definiert* | *nicht definiert* | *nicht definiert* | *nicht definiert* | Keine `:active`-Regel für Links im CSS. |
| **Buttons (Standard .btn)** | Inter | 14px | 700 | *nicht definiert* | *nicht definiert* | je Variante | Basis-Button-Regeln.【F:assets/css/style.css†L893-L907】 |
| **Buttons (Link .btn-link)** | Inter | `var(--font-size-xs)` | 500 | *nicht definiert* | *nicht definiert* | `var(--muted)` | Text-Button-Regeln.【F:assets/css/style.css†L960-L969】 |
| **Kleine Texte / Meta / Labels** | Inter | 11–14px (je Klasse) | 500–600 | 1.3–1.6 | *nicht definiert* | meist `var(--muted)` oder `var(--text)` | Beispiele: `.header-tagline` 11px, `.footer-tagline` 12px, `.footer-copy` 11px, `.quick-label` 12px, `.nav a` 13px, `.info-line` 0.9rem etc.【F:assets/css/style.css†L202-L208】【F:assets/css/style.css†L548-L559】【F:assets/css/style.css†L604-L612】【F:assets/css/style.css†L706-L713】【F:assets/css/style.css†L664-L674】【F:assets/css/style.css†L485-L506】 |

### Header-Hierarchie (Platzierungsregeln)

Die folgenden Regeln definieren, wie und wo Überschriften auf der Website platziert werden:

| Element | Platzierung | Icon | Zusatz-Element | Wrapper-Klasse |
|---------|-------------|------|----------------|----------------|
| **H1** | Hero-Bereich (erste Section-Card) | Nein | – | `.welcome-title` |
| **H2** | Sektions-Titel (außerhalb der Content-Box) | Ja (immer) | Divider (border-bottom) danach | `.section-header` |
| **H3** | Innerhalb einer Box/Card | Nein | Gradient Underline (via `::after`) | In `.content-card` oder `.section-card` |

**Struktur-Muster für H2:**
```html
<div class="section-header">
  <h2><img src="/assets/icons/[icon].svg" class="icon" alt="[Beschreibung]">[Titel]</h2>
</div>
<!-- Danach folgt die Content-Card/Box -->
```

**Struktur-Muster für H3:**
```html
<div class="content-card">
  <h3>[Untertitel]</h3>
  <p>Inhalt...</p>
</div>
```

**CSS-Referenzen:**
- `.section-header`: margin-bottom: 20px, padding-bottom: 16px, border-bottom: 1px solid var(--stroke)【F:assets/css/style.css†L691-L699】
- `h3::after`: Gradient-Underline von currentColor nach transparent【F:assets/css/style.css†L187-L199】

**Ausnahmen:**
- **404.php**: Nutzt `.error-message` statt `.welcome-title` für H1 (bewusste Design-Abweichung mit Animation für Error-Hero)
- **Rechtliche Seiten** (datenschutz, impressum): H2 ohne Icons erlaubt, da formale Rechtsdokumente

---

## 3. Farb-System

### 3.1 Marken-/Theme-Farben (Material Design 3 Basis)

Die Basisfarben sind in `:root` als CSS-Variablen definiert.

| Rolle/Token | Wert (CSS) | Zweck/Verwendung |
|---|---|---|
| `--md-primary` | `rgb(185 226 255)` | Primärfarbe (u.a. Headings, Links).【F:assets/css/style.css†L4-L9】【F:assets/css/style.css†L104-L112】 |
| `--md-on-primary` | `rgb(0 41 60)` | Text auf Primary (z. B. Buttons).【F:assets/css/style.css†L4-L9】【F:assets/css/style.css†L913-L921】 |
| `--md-primary-container` | `rgb(91 151 189)` | Akzent-/Container-Variante (Hover/Pressed).【F:assets/css/style.css†L4-L9】【F:assets/css/style.css†L80-L83】 |
| `--md-primary-fixed` | `rgb(199 231 255)` | fester Primärton (derzeit nicht direkt referenziert).【F:assets/css/style.css†L7-L9】 |
| `--md-primary-fixed-dim` | `rgb(146 206 245)` | Primary Dim (u.a. H3, CTA, Primär-Token).【F:assets/css/style.css†L7-L9】【F:assets/css/style.css†L126-L133】【F:assets/css/style.css†L74-L83】 |
| `--md-secondary` | `rgb(204 223 238)` | Sekundärfarbe (Token vorhanden).【F:assets/css/style.css†L12-L15】 |
| `--md-on-secondary` | `rgb(22 40 51)` | Text auf Secondary (Token vorhanden).【F:assets/css/style.css†L12-L15】 |
| `--md-secondary-container` | `rgb(129 147 161)` | Container-Variante Secondary (Token vorhanden).【F:assets/css/style.css†L12-L15】 |
| `--md-on-secondary-container` | `rgb(0 0 0)` | Text auf Secondary-Container.【F:assets/css/style.css†L12-L15】 |
| `--md-tertiary` | `rgb(227 214 255)` | Tertiärfarbe (H2).【F:assets/css/style.css†L18-L21】【F:assets/css/style.css†L115-L123】 |
| `--md-on-tertiary` | `rgb(41 33 64)` | Text auf Tertiary (Token vorhanden).【F:assets/css/style.css†L18-L21】 |
| `--md-tertiary-container` | `rgb(75 139 177)` | Container-Variante Tertiary (Token vorhanden).【F:assets/css/style.css†L18-L21】 |
| `--md-on-tertiary-container` | `rgb(0 0 0)` | Text auf Tertiary-Container.【F:assets/css/style.css†L18-L21】 |
| `--md-error` | `rgb(255 210 204)` | Error-Token (z. B. `--error`).【F:assets/css/style.css†L24-L27】【F:assets/css/style.css†L90-L94】 |
| `--md-on-error` | `rgb(84 0 3)` | Text auf Error (z. B. `--on-error`).【F:assets/css/style.css†L24-L27】【F:assets/css/style.css†L90-L94】 |
| `--md-error-container` | `rgb(255 84 73)` | Error-Container (Token vorhanden).【F:assets/css/style.css†L24-L27】 |
| `--md-on-error-container` | `rgb(0 0 0)` | Text auf Error-Container (Token vorhanden).【F:assets/css/style.css†L24-L27】 |
| `--md-background` | `rgb(16 20 23)` | Globaler Hintergrund (`--bg`).【F:assets/css/style.css†L30-L36】【F:assets/css/style.css†L66-L72】 |
| `--md-on-background` | `rgb(223 227 231)` | Text auf Hintergrund (Token vorhanden).【F:assets/css/style.css†L30-L36】 |
| `--md-surface` | `rgb(16 20 23)` | Oberfläche (identisch mit Background).【F:assets/css/style.css†L30-L36】 |
| `--md-on-surface` | `rgb(255 255 255)` | Standard-Textfarbe (`--text`).【F:assets/css/style.css†L30-L36】【F:assets/css/style.css†L87-L89】 |
| `--md-surface-variant` | `rgb(65 72 77)` | Variant Surface (Token vorhanden).【F:assets/css/style.css†L30-L36】 |
| `--md-on-surface-variant` | `rgb(215 221 228)` | Muted-Text (`--muted`).【F:assets/css/style.css†L30-L36】【F:assets/css/style.css†L87-L89】 |
| `--md-surface-dim` | `rgb(16 20 23)` | Surface Dim (Token vorhanden).【F:assets/css/style.css†L36-L42】 |
| `--md-surface-bright` | `rgb(65 69 73)` | Surface Bright (Token vorhanden).【F:assets/css/style.css†L36-L42】 |
| `--md-surface-container-lowest` | `rgb(4 8 11)` | Surface Container Lowest (Token vorhanden).【F:assets/css/style.css†L36-L42】 |
| `--md-surface-container-low` | `rgb(26 30 34)` | Surface Container Low (`--surface-1`).【F:assets/css/style.css†L36-L42】【F:assets/css/style.css†L66-L72】 |
| `--md-surface-container` | `rgb(36 40 44)` | Card Background (`--card`).【F:assets/css/style.css†L36-L42】【F:assets/css/style.css†L66-L72】 |
| `--md-surface-container-high` | `rgb(47 51 55)` | Hover/Card-High (`--surface-2`).【F:assets/css/style.css†L36-L42】【F:assets/css/style.css†L66-L72】 |
| `--md-surface-container-highest` | `rgb(58 62 66)` | Surface Container Highest (Token vorhanden).【F:assets/css/style.css†L36-L42】 |
| `--md-outline` | `rgb(172 179 185)` | Outline (Token vorhanden).【F:assets/css/style.css†L45-L46】 |
| `--md-outline-variant` | `rgb(138 145 151)` | Stroke/Border (`--stroke`).【F:assets/css/style.css†L45-L46】【F:assets/css/style.css†L87-L90】 |
| `--md-shadow` | `rgb(0 0 0)` | Shadow-Basis (für Elevation).【F:assets/css/style.css†L45-L47】【F:assets/css/style.css†L95-L97】 |
| `--md-scrim` | `rgb(0 0 0)` | Scrim (Token vorhanden).【F:assets/css/style.css†L45-L47】 |
| `--md-surface-tint` | `rgb(146 206 245)` | Surface Tint (Token vorhanden).【F:assets/css/style.css†L45-L47】 |
| `--md-inverse-surface` | `rgb(223 227 231)` | Inverse Surface (Token vorhanden).【F:assets/css/style.css†L50-L52】 |
| `--md-inverse-on-surface` | `rgb(38 43 46)` | Inverse On Surface (Token vorhanden).【F:assets/css/style.css†L50-L52】 |
| `--md-inverse-primary` | `rgb(0 77 110)` | Inverse Primary (Token vorhanden).【F:assets/css/style.css†L50-L52】 |

### 3.2 Alias- und Statusfarben

| Rolle/Token | Wert (CSS) | Verwendung |
|---|---|---|
| `--primary` | `var(--md-primary-fixed-dim)` | Haupt-CTA/Buttons/Textakzente.【F:assets/css/style.css†L74-L83】 |
| `--primary-hover` | `var(--md-primary)` | Hover-States (Buttons, Links).【F:assets/css/style.css†L80-L83】 |
| `--primary-pressed` | `var(--md-primary-container)` | Pressed-State (Token vorhanden).【F:assets/css/style.css†L80-L83】 |
| `--accent` | `var(--md-primary)` | Link-/Akzentfarbe (z. B. .nav).【F:assets/css/style.css†L84-L86】【F:assets/css/style.css†L664-L674】 |
| `--cta` | `var(--md-primary-fixed-dim)` | CTA-Text/Highlight (z. B. `.more`).【F:assets/css/style.css†L84-L86】【F:assets/css/style.css†L835-L840】 |
| `--text` | `var(--md-on-surface)` | Standard-Textfarbe (Body).【F:assets/css/style.css†L87-L89】【F:assets/css/style.css†L93-L100】 |
| `--muted` | `var(--md-on-surface-variant)` | Sekundär-/Meta-Text.【F:assets/css/style.css†L87-L89】 |
| `--stroke` | `var(--md-outline-variant)` | Standard-Outline/Border.【F:assets/css/style.css†L87-L90】 |
| `--card-hover` | `var(--md-surface-container-high)` | Hover-Hintergrund für Cards/Buttons.【F:assets/css/style.css†L87-L90】【F:assets/css/style.css†L412-L417】 |
| `--success` | `rgb(126 226 184)` | Success-Token (nicht in Komponenten genutzt).【F:assets/css/style.css†L90-L94】 |
| `--warning` | `rgb(255 211 153)` | Warning-Token (noch ohne Komponente).【F:assets/css/style.css†L90-L94】 |
| `--error` | `var(--md-error)` | Error-Token (u. a. Warning-Box-Border).【F:assets/css/style.css†L90-L94】 |
| `--info` | `var(--md-primary)` | Info-Token (nicht in Komponenten genutzt).【F:assets/css/style.css†L90-L94】 |
| `--on-success` | `rgb(13 59 42)` | Text auf Success (Token vorhanden).【F:assets/css/style.css†L90-L94】 |
| `--on-error` | `var(--md-on-error)` | Text auf Error (Token vorhanden).【F:assets/css/style.css†L90-L94】 |
| `--on-warning` | `rgb(92 56 0)` | Text auf Warning (Token vorhanden).【F:assets/css/style.css†L90-L94】 |

### 3.3 Feste Farben (Hardcoded)

| Farbe | Wert (CSS) | Verwendung |
|---|---|---|
| WhatsApp | `#25D366` | Links/Buttons/Social Icons (`.link-whatsapp`, `.btn-whatsapp`, `.menu-social .whatsapp`).【F:assets/css/style.css†L344-L355】【F:assets/css/style.css†L787-L796】【F:assets/css/style.css†L1002-L1009】 |
| WhatsApp Hover | `#1fa855` | `.btn-whatsapp:hover`.【F:assets/css/style.css†L1007-L1009】 |
| Facebook | `#1877F2` | Links/Buttons/Social Icons (`.link-facebook`, `.btn-facebook`, `.menu-social .facebook`).【F:assets/css/style.css†L348-L355】【F:assets/css/style.css†L797-L804】【F:assets/css/style.css†L1011-L1019】 |
| Facebook Hover | `#0c63d4` | `.btn-facebook:hover`.【F:assets/css/style.css†L1016-L1019】 |
| Mail | `#ea4335` | `.btn-mail`.【F:assets/css/style.css†L1021-L1024】 |
| Mail Hover | `#d33426` | `.btn-mail:hover`.【F:assets/css/style.css†L1026-L1029】 |
| Weiß | `#ffffff` | Legacy `--white`.【F:assets/css/style.css†L98-L100】 |
| Shadow/Backdrop | `rgba(0, 0, 0, .1/.25/.3/.4/.65)` | Shadows & Modal/Consent Backdrop.【F:assets/css/style.css†L169-L177】【F:assets/css/style.css†L97-L97】【F:assets/css/style.css†L996-L1000】【F:assets/css/style.css†L1144-L1149】 |

---

## 4. Hintergründe & Flächen-System

| Fläche | Hintergrund | Transparenz/Gradient | Border | Shadow | Radius | Quelle |
|---|---|---|---|---|---|---|
| **Body/Main Background** | `var(--bg)` | — | — | — | — | Body-Hintergrund.【F:assets/css/style.css†L93-L100】 |
| **Header** | `var(--bg)` | — | `1px solid var(--stroke)` | `0 2px 8px rgba(0, 0, 0, .1)` | — | `.site-header`.【F:assets/css/style.css†L157-L176】 |
| **Section Card** | `var(--card)` | — | `1px solid var(--stroke)` | `var(--shadow-1)` | 16px | `.section-card`.【F:assets/css/style.css†L371-L380】 |
| **Content Card** | `var(--surface-1)` | — | `1px solid var(--stroke)` | — | 12px | `.content-card`.【F:assets/css/style.css†L406-L414】 |
| **Community Card** | Linear Gradient | `linear-gradient(135deg, rgba(146, 206, 245, .12) 0%, rgba(91, 151, 189, .08) 100%)` | — | — | erbt Card | `.community-card`.【F:assets/css/style.css†L509-L516】 |
| **Footer** | `var(--bg)` | — | `1px solid var(--stroke)` | — | — | `.footer`.【F:assets/css/style.css†L546-L553】 |
| **Modal (dialog)** | `var(--card)` | — | `1px solid var(--stroke)` | `var(--shadow-3)` | 20px | `dialog`.【F:assets/css/style.css†L1034-L1041】 |
| **Modal Header** | `var(--surface-2)` | — | `1px solid var(--stroke)` | — | — | `.modal-head`.【F:assets/css/style.css†L1052-L1060】 |
| **Info Box** | `rgba(146, 206, 245, .1)` | transparent overlay | `border-left: 4px solid var(--primary)` | — | 8px | `.info-box`.【F:assets/css/style.css†L1222-L1229】 |
| **Warning Box (Error)** | `var(--md-error-container)` | — | `border-left: 4px solid var(--error)` | — | 8px | `.warning-box--error`.【F:assets/css/style.css†L1238-L1249】 |
| **Cookie Consent** | `var(--card)` | — | `border-top: 2px solid var(--primary)` | `0 -4px 20px rgba(0, 0, 0, .4)` | — | `.cookie-consent`.【F:assets/css/style.css†L1154-L1162】 |

---

## 5. Komponenten-Design

### Buttons

**Basis `.btn`**
- Typo: Inter, 14px, 700, Inline-Flex, min-height 48px.【F:assets/css/style.css†L893-L907】
- Radius: 12px, Border: `1px solid var(--primary)`.

**Varianten**
- **Primary (`.btn-primary`)**: Background `var(--primary)`, Text `var(--on-primary)`, Hover `var(--primary-hover)` + Shadow.【F:assets/css/style.css†L913-L931】
- **Ghost/Sekundär (`.btn-ghost`, `.btn.secondary`)**: Background `var(--surface-2)`, Text `var(--text)`, Border `var(--stroke)`, Hover `var(--card-hover)` + Border-Accent.【F:assets/css/style.css†L937-L952】
- **Link (`.btn-link`)**: Transparent, Border `var(--stroke)`, Text `var(--muted)`, kleiner (XS).【F:assets/css/style.css†L960-L969】
- **Social Buttons**: WhatsApp/Facebook/Mail mit festen Farben + Hover-Ton.【F:assets/css/style.css†L1002-L1029】

### Cards & Boxen

- **Section Cards**: `--card` Hintergrund, 16px Radius, Shadow `--shadow-1`, Hover `--shadow-2`.【F:assets/css/style.css†L371-L384】
- **Content Cards**: `--surface-1` Hintergrund, 12px Radius, Hover `--surface-2` + translateY. 【F:assets/css/style.css†L406-L417】
- **Legacy `.block`**: 16px Radius, Shadow, Border + Gradient-Topline on Hover. 【F:assets/css/style.css†L789-L832】

### Notice Boxes (Hinweis-System)

Einheitliches System für Warnungen, Tipps und Erfolgsmeldungen.

| Variante | Klasse | Icon | Verwendung |
|----------|--------|------|------------|
| **Warnung** | `.notice-box--warning` | ⚠️ | Kritische Hinweise, Gefahren |
| **Tipp/Info** | `.notice-box--info` | 💡 | Empfehlungen, hilfreiche Tipps |
| **Erfolg** | `.notice-box--success` | ✅ | Bestätigungen, positive Meldungen |

**HTML-Struktur:**
```html
<div class="notice-box notice-box--warning">
  <span class="notice-box__icon">⚠️</span>
  <strong class="notice-box__title">Titel</strong>
  <p class="notice-box__text">Beschreibung über die volle Breite...</p>
</div>
```

**Farb-Tokens:**
| Variante | Hintergrund | Border | Titel-Farbe |
|----------|-------------|--------|-------------|
| Warning | `rgba(255, 84, 73, .12)` | `var(--error)` | `var(--md-error)` |
| Info | `rgba(146, 206, 245, .1)` | `var(--primary)` | `var(--primary)` |
| Success | `rgba(126, 226, 184, .12)` | `var(--success)` | `var(--success)` |

**CSS-Referenz:** 【F:assets/css/style.css†L1473-L1536】

**Legacy-Klassen (Rückwärtskompatibel):**
- `.info-box` – entspricht `.notice-box--info` (ohne Icon-Layout)
- `.warning-box--error` – entspricht `.notice-box--warning`

### Navigation

- **Header Navigation (Mobile)**: `.mobile-menu` als Push-Down unter Header mit `max-height` Transition. Links als Cards (`var(--surface-1)`, 8px Radius).【F:assets/css/style.css†L282-L334】
- **Quick Navigation**: `.quick-links` mit underline und Accent-Farbe + Underline-Color. 【F:assets/css/style.css†L688-L725】
- **Primary Navigation**: `.nav a` als Pill-Buttons (13px, 20px Radius).【F:assets/css/style.css†L664-L675】

### Modal

- **Dialog**: Card-ähnliche Oberfläche mit 20px Radius + Shadow. Backdrop: `rgba(0,0,0,.65)` + blur(8px).【F:assets/css/style.css†L1034-L1049】
- **Pills/Tabs**: `.pill` als 24px Radius, States inkl. `.active` (Primary + Shadow).【F:assets/css/style.css†L1094-L1123】

### Formulare

- **Keine dedizierten Input-/Select-/Checkbox-Styles** im CSS vorhanden.

---

## 6. Layout- & Spacing-System

**Container / Content Breite**
- `.box`: `max-width: 800px`, `width: 100%`, zentriert mit `margin: 0 auto`.【F:assets/css/style.css†L361-L366】

**Spacing Tokens**
- `--space-section: 32px`
- `--space-card: 16px`
- `--space-element: 12px`
- `--space-inline: 8px`
- `--padding-section: 28px`
- `--padding-card: 20px`
- `--padding-callout: 16px`【F:assets/css/style.css†L56-L62】

**Header-Höhe**
- `--header-height: 90px` (Mobile: 82px).【F:assets/css/style.css†L72-L73】【F:assets/css/style.css†L607-L615】

**Vertikale Rhythmik**
- Karten/Sections arbeiten mit `var(--space-section)` und `var(--space-card)`.
- Textblöcke nutzen meistens `var(--space-element)` als vertikalen Abstand.【F:assets/css/style.css†L371-L389】【F:assets/css/style.css†L439-L447】

---

## 7. Utility- & Komponenten-Klassen (Inline-CSS-Migration)

**Zweck & Prinzipien**
- **Tokens statt Hardcodes**: Farben, Spacing und Typografie bleiben an `var(--*)` gebunden.
- **Keine Inline-Styles**: Layout- und Typo-Regeln nur über Klassen in `assets/css/style.css`.
- **Wiederverwendbarkeit vor Einzelfall**: Kleine Abstände als Utility, wiederkehrende Muster als Komponente.

### Utility-Klassen (Auswahl)
- **Spacing**: `.u-mt-8`, `.u-mt-10`, `.u-mt-12`, `.u-mt-16`, `.u-mt-18`, `.u-mt-20`, `.u-mt-element`, `.u-my-8`, `.u-my-12`, `.u-my-14`
- **Alignment/Display**: `.u-text-center`, `.u-text-right`, `.u-inline-block`
- **Text/Links**: `.text-muted`, `.text-small`, `.text-xs`, `.link-accent`, `.link-plain`, `.link-primary-strong`
- **Notizen**: `.desc-small`, `.desc-note`
- **Helpers**: `.block--center`, `.is-hidden`

### Komponenten-Klassen (Auswahl)
- **Legal/Datenschutz**: `.legal-subheadline`, `.legal-subheadline--spaced`, `.legal-list`
- **Downloads**: `.section-card--info`, `.download-callout-*`, `.download-meta-*`, `.download-tags`, `.download-tag`, `.download-actions`
- **Kontaktseite**: `.contact-page`, `.contact-hero`, `.contact-method*`, `.form-*`, `.status-message`, `.info-note`
- **Accounts**: `.account-stats`, `.status-badge`, `.use-case-box`, `.faq-*`, `.content-card-actions`
- **Admin**: `.admin-*` (Login & Kontaktanfragen, klar gescoped)

### Beispiele (Inline → Klasse)

**Vorher (Beschreibung)**
- Legacy-Subheadline mit Inline-Spacing + Accent-Farbe (siehe Inline-Inventory in `/add/Downloads.html` und `/add/Accounts.html`).

**Nachher (Klasse)**
```html
<h3 class="legal-subheadline legal-subheadline--spaced">Haftung für Links</h3>
```

**Vorher (Beschreibung)**
- Legacy-Download-CTA mit Inline-Flex + Gap + Top-Margin (siehe Inline-Inventory in `/add/freundschaftsbalken-final.html`).

**Nachher (Klasse)**
```html
<div class="download-actions">
  <a class="btn btn-primary">Download</a>
</div>
```

### Konventionen & Review-Checkliste
- **Namensschema**: Utilities mit `u-` Prefix, Komponenten sprechend benennen.
- **Ablage**: Neue Styles immer in `assets/css/style.css` in klarer Sektion.
- **Review**: Inline-Style-Attribute und Style-Tags per ripgrep prüfen (nur Treffer in `/add` & `/weg` zulässig).
- **JS**: Inline-Style-Manipulation vermeiden (stattdessen Klassen wie `.is-hidden`).

---

## 8. Konsistenz-Analyse & Empfehlungen

**Konsistenz-Check**
- **Klares System** mit Material-ähnlichen Tokens (`--md-*`) und Alias-Variablen (`--primary`, `--card`, `--surface-*`).【F:assets/css/style.css†L1-L89】
- **Typografie-System** ist teilweise konsistent (H1–H3 klar definiert, Body/Inter global).【F:assets/css/style.css†L93-L133】
- **Einheitliche Spacing-Variablen** werden in vielen Komponenten genutzt (Section/Card).【F:assets/css/style.css†L56-L62】【F:assets/css/style.css†L371-L414】

**Doppelte oder widersprüchliche Styles**
- Es gibt **Legacy-Styles** (`.block`) parallel zu neueren Cards (`.section-card`, `.content-card`).【F:assets/css/style.css†L371-L414】【F:assets/css/style.css†L789-L832】
- Mehrere Komponenten definieren ähnliche Card-Patterns (Border + Shadow + Hover).

**Empfehlungen zur Vereinheitlichung**
1. **Card-System konsolidieren**: `.block` in `.section-card`/`.content-card` überführen oder eindeutig trennen (Legacy vs. neu).
2. **Text-Defaults für H4 & Paragraph** ergänzen (z. B. definierte Größen/Zeilenhöhen), um Typo konsistenter zu machen.
3. **State-Tokens** für `:active`/`:focus` konsistent definieren (z. B. für Links & Buttons).
4. **Form-Styles** ergänzen, falls künftig Formulare genutzt werden sollen.

---

## 9. Governance & Prinzipien (Design- & CSS-System-Audit)

**Single Source of Truth (Styles)**
- **Global CSS:** `assets/css/style.css` ist die einzige produktive Stylesheet-Quelle.
- **Tokens:** `:root` in `assets/css/style.css` ist die zentrale Token-Basis (Farben, Spacing, Typografie, Elevation).
- **Fonts:** ausschließlich über `partials/head-links.php` eingebunden.

**Governance-Regeln**
1. **Keine Inline-Styles** (Style-Attribute, Style-Tags). Ausnahmen ausschließlich technisch zwingend und dokumentieren.
2. **Keine Inline-Skripte oder Inline-Handler** (z. B. `onclick`, `onload`) – JS ausschließlich in `assets/js/*`.
3. **Tokens statt Hardcodes**: Farben, Spacing, Typografie, Schatten nur über `var(--*)`.
4. **Neue Styles nur in `assets/css/style.css`** (klarer Abschnitt, sprechende Namen).
5. **Utilities für Layout/Spacing**, Komponenten für wiederkehrende Muster (Legal, Kontakt, Downloads).
6. **Nicht-produktive Ordner (`/add`, `/weg`) werden nicht migriert**, nur dokumentiert.

### Review-Checkliste (Do / Don’t)

**Do**
- ✅ Nutzen von `var(--*)` Tokens für Farben, Abstände, Typo.
- ✅ Reuse bestehender Utility-Klassen (`.u-*`) für Spacing/Alignment.
- ✅ Komponenten-Patterns (Card/Download/Legal) bevorzugen.
- ✅ Suche nach Inline-Style-Attributen und Style-Tags (nur Treffer in `/add` & `/weg`).
- ✅ Suche nach Inline-Skripten und Inline-Handlern (nur Treffer in `/add` & `/weg`).

**Don’t**
- ❌ Inline-Styles oder Inline-Style-Blöcke.
- ❌ Inline-Skripte oder Inline-Handler.
- ❌ Hardcoded Farben/Spacing in neuen Komponenten.
- ❌ Neue globale CSS-Dateien.
- ❌ Styles in Templates oder Partials definieren.

---

## 10. Inline Style Inventory (systematisch)

**Legende Schweregrad**
- **S1**: Kritisch (produktiv, markenrelevant oder sicherheits-/CSP-relevant)
- **S2**: Hoch (produktiv, wiederkehrend oder stark sichtbar)
- **S3**: Mittel (produktiv, lokal begrenzt)
- **S4**: Niedrig (nicht produktiv/Archiv oder rein optional)

| Datei-Pfad | Zeile(n) / Snippet | Art | Betroffene Properties | Wiederverwendbarkeit | Empfohlene Klasse(n) | Schweregrad (Begründung) | Status |
|---|---|---|---|---|---|---|---|
| `weg/error_404_page.php` | `13–121` (Style-Tag-Block) | Style-Tag | Layout, Typo, Animation, Farben, Buttons | systematisch | In `assets/css/style.css` migrieren (z. B. `.error-*`) | **S4** – Archiv, nicht produktiv | Archiv |
| `weg/error_404_page.php` | `173, 180, 187, 194` (Link-Accent-Attribute) | Attribut | `color` | mehrfach | `.link-accent` | **S4** – Archiv, nicht produktiv | Archiv |
| `weg/cookie_consent.html` | `4–80` (Style-Tag-Block) | Style-Tag | Layout, Typo, Animation | systematisch | In `assets/css/style.css` migrieren | **S4** – Archiv, nicht produktiv | Archiv |
| `add/freundschaftsbalken-final.html` | `30` (`margin-top`) | Attribut | `margin-top` | einmalig | `.u-mt-12` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `55, 154` (Link-Accent) | Attribut | `color`, `font-weight` | mehrfach | `.link-primary-strong` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `95` (`margin-top`) | Attribut | `margin-top` | einmalig | `.u-mt-10` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `240, 244` (`margin:8px 0`) | Attribut | `margin` | mehrfach | `.u-my-8` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `254` (`margin:12px 0`) | Attribut | `margin` | einmalig | `.u-my-12` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `265` (`text-align:center`) | Attribut | `text-align` | einmalig | `.block--center` / `.u-text-center` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `268` (`margin-top`, `display`) | Attribut | `margin-top`, `display` | einmalig | `.u-mt-8` + `.u-inline-block` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/freundschaftsbalken-final.html` | `40, 63, 116, 192` (`onerror="this.style.display='none'"`) | JS | `display` | mehrfach | `.is-hidden` per JS-Klasse | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Downloads.html` | `28` (Tracking-Noscript-Attribut) | Attribut | `display` | einmalig | `.is-hidden` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Downloads.html` | `41–427` (Style-Tag-Block) | Style-Tag | Layout, Typo, Cards, Buttons | systematisch | In `assets/css/style.css` migrieren | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Downloads.html` | `454` (Support-Link Accent) | Attribut | `color`, `font-weight` | einmalig | `.link-primary-strong` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Downloads.html` | `458–463` (CTA-Block) | Attribut | `text-align`, `margin-top`, `color`, `font-size`, `margin-bottom` | einmalig | `.block--center` + Typo-Utilities | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Accounts.html` | `28` (Tracking-Noscript-Attribut) | Attribut | `display` | einmalig | `.is-hidden` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Accounts.html` | `41–377` (Style-Tag-Block) | Style-Tag | Layout, Typo, Cards, Buttons | systematisch | In `assets/css/style.css` migrieren | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Accounts.html` | `399` (H2 Inline-Styles) | Attribut | `color`, `font-size`, `margin-bottom` | einmalig | `.section-header` + H2 Defaults | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Accounts.html` | `518` (`margin-top`) | Attribut | `margin-top` | einmalig | `.u-mt-12` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/Accounts.html` | `533` (Link-Accent) | Attribut | `color`, `font-weight` | einmalig | `.link-primary-strong` | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/headerFooter.txt` | `1–120` (Style-Tag-Block) | Style-Tag | Tokens, Header/Footer, Layout | systematisch | In `assets/css/style.css` migrieren | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |
| `add/style-friendbar.txt` | `1–200` (Style-Tag-Block) | Style-Tag | Layout, Typo, Cards | systematisch | In `assets/css/style.css` migrieren | **S4** – Nicht produktiv | Nicht produktiv (`/add`) |

**Produktiv-Status:** In produktiven Templates wurden **keine** Inline-Styles gefunden (Stand der Analyse).

---

## 11. Inline Script/Handler Inventory (systematisch)

**Legende Schweregrad**
- **S1**: Kritisch (produktiv, CSP-relevant)
- **S2**: Hoch (produktiv, wiederkehrend)
- **S3**: Mittel (produktiv, lokal begrenzt)
- **S4**: Niedrig (nicht produktiv/Archiv)

| Datei-Pfad | Zeile(n) / Snippet | Art | Zweck | Migration | Schweregrad (Begründung) | Status |
|---|---|---|---|---|---|---|
| `index.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `downloads/index.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `anleitungen/index.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `impressum/index.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `datenschutz/index.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `404.php` | `Structured Data (site-wide)` | Inline-JSON-LD | Schema.org-Org + WebSite | → `assets/js/structured-data/organization.json`, `website.json` | **S1** – CSP/Inline Script | Migriert |
| `accounts/index.php` | `Structured Data` | Inline-JSON-LD | Schema.org-Org | → `assets/js/structured-data/organization.json` | **S1** – CSP/Inline Script | Migriert |
| `anleitungen/freundschaftsbalken-fuellen/index.php` | `Structured Data (page-specific)` | Inline-JSON-LD | Schema.org-HowTo | → `assets/js/structured-data/howto-freundschaftsbalken.json` | **S1** – CSP/Inline Script | Migriert |
| `index.php` | `Modal Logic + Tab-Switching` | Inline-Script | Dialogs + Tabs | → `assets/js/main.js` | **S1** – CSP/Inline Script | Migriert |
| `accounts/index.php` | `FAQ Accordion` | Inline-Script | FAQ Toggle | → `assets/js/main.js` | **S1** – CSP/Inline Script | Migriert |

**Produktiv-Status:** In produktiven Templates wurden **keine** Inline-Skripte oder Inline-Handler gefunden (Stand der Analyse).

---

## 12. Migration: Inline → Global CSS

### CSS Additions (umgesetzt)
- **Typografie-Defaults ergänzt:** `h4`, `h5`, `h6`, `p`, `small` im globalen Stylesheet.【F:assets/css/style.css†L136-L176】
- **Zoom-Scale Tokens:** `--zoom-scale`, Layout-/Spacing-Scaler und Box-Padding zentral ergänzt.【F:assets/css/style.css†L57-L90】
- **Homepage-Updates:** `.services-section`, `.contact-card` Styles ergänzt (ohne Redesign).【F:assets/css/style.css†L451-L488】

### JS Migrations (umgesetzt)
- **Modal/Tab-Handling:** von `index.php` nach `assets/js/main.js` verschoben.【F:assets/js/main.js†L118-L207】
- **FAQ-Akkordeon:** von `accounts/index.php` nach `assets/js/main.js` verschoben.【F:assets/js/main.js†L209-L230】
- **Menü-ARIA-Update:** `aria-hidden` für Mobile-Menü zentral gesetzt.【F:assets/js/main.js†L243-L273】

### Template Replacements (umgesetzt)
- **Inline-JSON-LD** in produktiven Seiten durch externe JSON-Dateien ersetzt (`assets/js/structured-data/*`).【F:index.php†L19-L22】【F:downloads/index.php†L20-L21】
- **Inline-Skripte** (Modal/FAQ) entfernt und in `assets/js/main.js` zentralisiert.【F:index.php†L200-L361】【F:accounts/index.php†L322-L326】
- **Icons:** Inline-SVGs in produktiven Templates auf `assets/icons/*` umgestellt.【F:index.php†L45-L185】【F:downloads/index.php†L47-L187】【F:accounts/index.php†L71-L248】
- **Footer-Links:** Social-Links entfernt, Kontakt-Link ergänzt.【F:partials/footer.php†L8-L16】

---

## 13. Design Tokens Übersicht (offiziell)

### Farben
- **Brand/Primary:** `--md-primary`, `--md-primary-fixed-dim`, `--primary`, `--accent`
- **Text:** `--text`, `--muted`, `--md-on-surface`
- **Surface:** `--bg`, `--card`, `--surface-1`, `--surface-2`
- **Status:** `--success`, `--warning`, `--error`, `--info`
- **Plattform/Brand:** `--brand-whatsapp`, `--brand-whatsapp-bg`, `--brand-facebook`, `--brand-facebook-bg`

### Typografie
- **Fonts:** `Inter`, `Montserrat`
- **Sizes:** `--font-size-h1`, `--font-size-h2`, `--font-size-h3`, `--font-size-body`, `--font-size-small`, `--font-size-xs`

### Spacing
- `--space-section`, `--space-card`, `--space-element`, `--space-inline`
- `--padding-section`, `--padding-card`, `--padding-callout`

### Layout / Scale
- `--zoom-scale`
- `--layout-max-width`
- `--box-padding-top`, `--box-padding-inline`, `--box-padding-bottom`
- `--header-height`

### Border-Radius (aus bestehenden Werten abgeleitet)
- `radius-lg` → **16px** (Cards, Sections)
- `radius-md` → **12px** (Buttons, Cards)
- `radius-sm` → **8px** (Inputs, Badges)
- `radius-xs` → **6px** (Selects, Tags)
- `radius-xl` → **20px** (Modals)

### Shadows / Elevation
- `--shadow-1`, `--shadow-2`, `--shadow-3`

**Optional: Token-JSON (Dokumentation)**
```json
{
  "color": {
    "primary": "var(--md-primary-fixed-dim)",
    "accent": "var(--md-primary)",
    "text": "var(--text)",
    "muted": "var(--muted)",
    "surface": {
      "base": "var(--bg)",
      "card": "var(--card)",
      "raised": "var(--surface-2)"
    },
    "status": {
      "success": "var(--success)",
      "warning": "var(--warning)",
      "error": "var(--error)",
      "info": "var(--info)"
    }
  },
  "typography": {
    "font": {
      "body": "Inter",
      "heading": "Montserrat"
    },
    "size": {
      "h1": "var(--font-size-h1)",
      "h2": "var(--font-size-h2)",
      "h3": "var(--font-size-h3)",
      "body": "var(--font-size-body)",
      "small": "var(--font-size-small)",
      "xs": "var(--font-size-xs)"
    }
  },
  "spacing": {
    "section": "var(--space-section)",
    "card": "var(--space-card)",
    "element": "var(--space-element)",
    "inline": "var(--space-inline)"
  },
  "radius": {
    "lg": "16px",
    "md": "12px",
    "sm": "8px",
    "xs": "6px",
    "xl": "20px"
  },
  "shadow": {
    "1": "var(--shadow-1)",
    "2": "var(--shadow-2)",
    "3": "var(--shadow-3)"
  }
}
```

---

## 14. Typografie-Vervollständigung (Umsetzung)

**Ergänzt**
- `h4`, `h5`, `h6` als konsistente Heading-Stufen (Montserrat).
- `p` als definierter Body-Text mit konsistentem Margin.
- `small` für Meta-Texte mit `var(--muted)` Farbe.【F:assets/css/style.css†L136-L176】

---

## 15. Admin-UI-Strategie

**Entscheidung:** Admin bleibt **bewusst brand-neutral**, jedoch **mit primären Brand-Akzenten**.

**Begründung (Codebasis):**
- Admin-Styles sind vollständig gescoped (`.admin-*`), nutzen aber Hardcodes statt Tokens.
- Brand-Akzentfarbe ist sichtbar (z. B. `#92CEF5` entspricht `--md-primary-fixed-dim`).【F:assets/css/style.css†L1845-L2043】

**Empfehlung (future):**
- Harte Farben sukzessive auf Tokens umstellen (`--primary`, `--bg`, `--surface-1`), ohne Admin-UI zu redesignen.

---

## 16. Brand Deviations Report (Priorisierung nach Markenwirkung)

| Seite / Komponente | Abweichung | Ursache | Schweregrad | Empfehlung |
|---|---|---|---|---|
| `impressum/`, `datenschutz/`, `anleitungen/` | Legacy-Card `.block` statt `.section-card`/`.content-card` | Historische Klasse, parallel zum neuen Card-System | **S2** | `.block` konsolidieren oder visuell angleichen, um Markenbild zu vereinheitlichen |
| Admin (`kontakt/admin/*`) | Hardcoded Farben (kein Token-Einsatz) | Admin-Styles isoliert, nicht tokenisiert | **S3** | Tokens schrittweise übernehmen, ohne visuelles Redesign |
| Social Buttons (`.btn-whatsapp`, `.btn-facebook`, `.btn-mail`) | Feste Plattformfarben statt Brand-Token | Plattform-Branding erforderlich | **S4** | Beibehalten, optional in Token-Wrapper dokumentieren |
| Plattform-Icons (`assets/icons/whatsapp.svg`, `assets/icons/facebook.svg`) | Plattformfarben in SVG gesetzt | Marken-Branding erforderlich | **S4** | Beibehalten, Ausnahme dokumentiert |

---

## 17. CSP-Empfehlung (nach Inline-Migration)

**Status:** Produktive Templates enthalten aktuell **keine** Inline-Styles oder Inline-Skripte.

**Empfehlung:**
1. **Voraussetzungen**: Sicherstellen, dass keine Inline-Styles oder Inline-Skripte in produktiven Pfaden (`/`, Inhaltsordner, `/partials`) verbleiben.
2. **Zielzustand**: `style-src` ohne `'unsafe-inline'` (nur `'self'` + Fonts/CDN).
3. **Script-Regel**: `script-src` ohne `'unsafe-inline'`, stattdessen `'self'` + erlaubte Tracking-Quellen (falls aktiviert).
4. **Reihenfolge**: CSP im **Report-Only** testen → Reports auswerten → Enforce aktivieren.

**Hinweis:** Aktuelle CSP-Beispiele liegen nur in `/weg/` und sind nicht produktiv.

---

## 18. Design-Dokumentation aktualisiert

Dieses Dokument enthält jetzt:
- Governance & Review-Checkliste
- Inline Style Inventory
- Inline Script/Handler Inventory
- Migrations-Listen (CSS Additions / Template Replacements)
- Explizite Token-Übersicht
- Admin-Strategie & Brand Deviations Report
- CSP-Empfehlung

---

## 19. Quellenverzeichnis (Dateien)

- `assets/css/style.css`
- `partials/head-links.php`
- `partials/header.php`
- `partials/footer.php`
- `anleitungen/freundschaftsbalken-fuellen/index.php`

---

## Slider-Komponente

Die Slider-Komponente bietet eine moderne, barrierefreie Bildergalerie mit erweiterten Funktionen.

### HTML-Struktur

```html
<div class="slider-container" data-autoplay="5000" data-loop="true">
  <div class="slider-track">
    <div class="slider-item">
      <img src="/path/to/image.jpg" alt="Description">
    </div>
    <!-- Weitere Slides -->
  </div>
  
  <button type="button" class="slider-btn slider-btn-prev" aria-label="Vorheriges Bild">‹</button>
  <button type="button" class="slider-btn slider-btn-next" aria-label="Nächstes Bild">›</button>
</div>
```

### Optionen

- `data-autoplay="5000"` - Aktiviert Autoplay mit 5 Sekunden Intervall (in Millisekunden)
- `data-loop="true"` - Endlos-Loop aktivieren (Standard: true)
- `data-loop="false"` - Loop deaktivieren (Buttons werden am Anfang/Ende deaktiviert)

### Lazy Loading

Für bessere Performance können Bilder mit Lazy Loading geladen werden:

```html
<div class="slider-item">
  <img data-src="/path/to/image.jpg" alt="Description">
</div>
```

Das JavaScript lädt das aktuelle und nächste Bild automatisch.

### Caption/Beschreibung (Optional)

```html
<div class="slider-caption">
  <p class="slider-caption-title">Titel</p>
  <p class="slider-caption-text">Beschreibung</p>
</div>
```

### Features

#### Automatisch generierte Elemente

Das JavaScript erstellt automatisch folgende Elemente:

- **Counter Badge** (z.B. "1 / 5") - Zeigt aktuelle Position
- **Progress Bar** - Visueller Fortschritt oben
- **Dots Navigation** - Wenn `.slider-dots` Container vorhanden
- **Autoplay Indicator** - Play/Pause Button (nur bei Autoplay)

#### Interaktivität

- ✅ **Navigation Buttons** - Erscheinen bei Hover (Desktop) oder sind immer sichtbar (Touch)
- ✅ **Keyboard-Navigation** - Pfeiltasten, Home, End
- ✅ **Touch/Swipe** - Gestensteuerung auf Mobilgeräten
- ✅ **Autoplay** - Optional mit Pause bei Hover
- ✅ **Screen Reader Support** - ARIA-Labels und Live-Announcements
- ✅ **Lazy Loading** - Bilder werden bei Bedarf geladen

#### Design

- Modern mit Material Design 3 Tokens
- Runde Buttons mit Backdrop-Filter
- Animierte Dots mit aktiver Anzeige
- Smooth Transitions mit cubic-bezier
- Loading States mit Shimmer-Effekt
- Responsive Design

### CSS-Klassen

| Klasse | Beschreibung |
|--------|--------------|
| `.slider-container` | Haupt-Container |
| `.slider-track` | Flex-Container für Slides |
| `.slider-item` | Einzelner Slide |
| `.slider-btn` | Navigation Button (Basis) |
| `.slider-btn-prev` | Vorheriger Button |
| `.slider-btn-next` | Nächster Button |
| `.slider-dots` | Container für Dots (optional) |
| `.slider-dot` | Einzelner Dot |
| `.slider-counter` | Counter Badge (automatisch) |
| `.slider-progress` | Progress Bar Container (automatisch) |
| `.slider-progress-bar` | Progress Bar Füllung (automatisch) |
| `.slider-autoplay-indicator` | Autoplay Button (automatisch) |
| `.slider-caption` | Caption Container |
| `.slider-caption-title` | Caption Titel |
| `.slider-caption-text` | Caption Text |

### Beispiele

#### Basis-Slider

```html
<div class="slider-container">
  <div class="slider-track">
    <div class="slider-item">
      <img src="/img1.jpg" alt="Bild 1">
    </div>
    <div class="slider-item">
      <img src="/img2.jpg" alt="Bild 2">
    </div>
  </div>
  <button type="button" class="slider-btn slider-btn-prev">‹</button>
  <button type="button" class="slider-btn slider-btn-next">›</button>
</div>
```

#### Mit Autoplay

```html
<div class="slider-container" data-autoplay="5000" data-loop="true">
  <!-- ... slider content ... -->
</div>
```

#### Mit Caption

```html
<div class="slider-container">
  <!-- ... slides und buttons ... -->
  <div class="slider-caption">
    <p class="slider-caption-title">Erfolgreiche Partner Events</p>
    <p class="slider-caption-text">Screenshots von abgeschlossenen Events mit 80.000 Punkten</p>
  </div>
</div>
```

### Barrierefreiheit

- **ARIA-Labels** auf allen interaktiven Elementen
- **aria-expanded** auf Buttons
- **Live Regions** für Screen Reader Announcements
- **Keyboard Navigation** vollständig unterstützt
- **Focus Styles** für Tastaturnutzung
- **Touch-freundliche** Button-Größen

### Browser-Kompatibilität

- Moderne Browser (Chrome, Firefox, Safari, Edge)
- Mobile Browser (iOS Safari, Chrome Mobile)
- Verwendet standardisierte Web APIs
- Keine externen Abhängigkeiten

---
