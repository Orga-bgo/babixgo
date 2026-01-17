# CSS Analyse Report für Admin Seiten
Generiert: 2026-01-17 01:11:45

## 1. Seiten ohne eigene Styles

- **index.php** - Verwendet nur externe Stylesheets
- **users.php** - Verwendet nur externe Stylesheets
- **comments.php** - Verwendet nur externe Stylesheets
- **downloads.php** - Verwendet nur externe Stylesheets
- **user-edit.php** - Verwendet nur externe Stylesheets
- **download-edit.php** - Verwendet nur externe Stylesheets

## 2. Redundante Styles

✓ Keine duplizierten inline styles gefunden

## 3. Fehlende Design Tokens (CSS Variablen)

### Farben die als CSS Variablen definiert werden sollten:

✓ Keine hardcoded Farben in inline styles gefunden

### Größen die als CSS Variablen definiert werden sollten:

✓ Keine hardcoded Größen in inline styles gefunden

## 4. CSS-Klassen Status

### Definierte Klassen (46):

✓ Diese Klassen sind bereits in `/assets/css/style.css` definiert:

- `.actions`
- `.active`
- `.activity-card`
- `.activity-grid`
- `.admin-table`
- `.badge`
- `.badge-approved`
- `.badge-pending`
- `.badge-spam`
- `.btn`
- `.btn-danger`
- `.btn-primary`
- `.btn-secondary`
- `.btn-small`
- `.btn-success`
- `.btn-warning`
- `.bulk-actions`
- `.checkbox-group`
- `.comment-preview`
- `.container`

... und 26 weitere

### Fehlende Klassen (7):

⚠️ Diese Klassen werden verwendet, sind aber nicht definiert:

- `.comment-checkbox` - Verwendet in: comments.php
- `.filter-buttons` - Verwendet in: comments.php, downloads.php
- `.form-control` - Verwendet in: user-edit.php
- `.info-row` - Verwendet in: user-edit.php, user-edit.php, user-edit.php
- `.profile-card` - Verwendet in: downloads.php, user-edit.php, user-edit.php
- `.profile-grid` - Verwendet in: user-edit.php, download-edit.php
- `.user-checkbox` - Verwendet in: users.php

## 5. Zusammenfassung

- **Analysierte Seiten:** 6
- **Gefundene CSS-Klassen:** 53
- **Definierte Klassen:** 46
- **Fehlende Klassen:** 7
- **Inline styles:** 0
- **<style> tags:** 0
- **Style-Attribute:** 0

## 6. Meistverwendete CSS-Klassen

✓ `.btn` - Verwendet in 5 Seite(n): download-edit.php, downloads.php, comments.php
⚠️ `.info-row` - Verwendet in 2 Seite(n): user-edit.php, download-edit.php
✓ `.form-group` - Verwendet in 3 Seite(n): user-edit.php, download-edit.php, downloads.php
✓ `.btn-secondary` - Verwendet in 5 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.admin-table` - Verwendet in 5 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.empty-state` - Verwendet in 5 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.btn-small` - Verwendet in 3 Seite(n): users.php, downloads.php, comments.php
✓ `.main-nav` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.nav-container` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.logo` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.nav-menu` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.active` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.container` - Verwendet in 6 Seite(n): download-edit.php, downloads.php, comments.php
✓ `.message` - Verwendet in 3 Seite(n): user-edit.php, download-edit.php, downloads.php
⚠️ `.profile-card` - Verwendet in 3 Seite(n): user-edit.php, download-edit.php, downloads.php
✓ `.btn-danger` - Verwendet in 3 Seite(n): users.php, downloads.php, comments.php
✓ `.btn-primary` - Verwendet in 4 Seite(n): user-edit.php, download-edit.php, users.php
✓ `.stat-card` - Verwendet in 1 Seite(n): index.php
✓ `.stat-value` - Verwendet in 1 Seite(n): index.php
✓ `.stat-link` - Verwendet in 1 Seite(n): index.php

## 7. Klassen nach Kategorie

- **Layout:** 9 Klassen
- **Komponenten:** 28 Klassen
- **Utilities:** 16 Klassen