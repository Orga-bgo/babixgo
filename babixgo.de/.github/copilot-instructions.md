# Copilot Instructions: babixGO.de

## Project Summary
Static website for Monopoly GO services. Pure HTML/CSS/JavaScript with PHP server-side includes. NO build process or dependencies.
- **Tech:** PHP 8.3+, HTML, CSS (3,850 lines), JS (835 lines) | 32 PHP files, 171 total files
- **Deployment:** Auto SFTP to Strato on push to `main` (`.github/workflows/main.yml`)

## CRITICAL: Read These First (Violations = PR Rejection)
1. **Agents.md** - Binding rules, structure, workflows (MANDATORY)
2. **DESIGN_SYSTEM.md** - Brand tokens, components, styles
3. **README.md** - Project overview

## Development: Zero Dependencies, No Build

**Start Server:** `php -S localhost:8000` (from `/home/runner/work/bgo/bgo`)
**Validate PHP:** `php -l filename.php` OR `find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"`
**Test:** Open browser, check console (F12), test mobile view, verify partials loaded
**Deploy:** Auto on push to `main` - no manual steps

**Environment Variables (contact form only):**
```bash
BABIXGO_DB_HOST, BABIXGO_DB_NAME, BABIXGO_DB_USER, BABIXGO_DB_PASS
```

## Structure (Key Paths)
```
/partials/          # PHP includes (MUST use $_SERVER['DOCUMENT_ROOT'] . '/partials/FILE.php')
  head-meta.php     # Meta tags | head-links.php = CSS/fonts | tracking.php = ALL tracking
  cookie-banner.php, header.php, footer.php, footer-scripts.php
  csrf.php, brute-force-protection.php # Security
/assets/css/style.css  # SINGLE source (3,852 lines) - NO other global CSS
/assets/js/main.js     # Global JS (835 lines) - NO inline scripts (CSP)
/assets/material-symbols/  # H2 icons (48×48 SVG, 30+ icons)
/kontakt/admin/     # Admin (CSRF + brute-force protected, 5 attempts = 15min lockout)
/weg/, /add/        # Archive/future (DO NOT MODIFY or reference)
/templates/         # Copy templates only (NOT production)
```

**Production Files:** `.php` in root or content folders. **Non-Production:** `/weg/`, `/add/`, `/examples/`, `/to-do/`

## PHP Partials: STRICT Order & Rules

**Inclusion Pattern (ALWAYS):** `<?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/FILE.php'; ?>`
❌ NEVER: Relative paths, `__DIR__`, short tags

**Order in EVERY page:**
1. `<head>`: head-meta.php → page meta (title/description/canonical) → head-links.php
2. After `<body>`: tracking.php → cookie-banner.php → header.php
3. Before `</body>`: footer.php → footer-scripts.php

**Partial Rules:**
- head-meta.php = Common meta ONLY (NO page-specific)
- head-links.php = CSS/fonts ONLY (NO meta, NO scripts)
- tracking.php = ALL tracking (NO tracking elsewhere)
- footer-scripts.php = Global JS ONLY (NO tracking)

## HTML Requirements (Every Page)

**H1:** Exactly ONE per page in hero (`.welcome-title`). Exception: 404.php uses `.error-message`
**H2:** Section titles with icon: `<div class="section-header"><h2><img src="/assets/material-symbols/icon.svg" class="icon" alt="...">Title</h2></div>`
**H3:** Inside cards, auto gradient underline via `::after`
**Meta:** Title (unique), description (150-160 chars), canonical (all REQUIRED)
**Images:** Must have descriptive alt + width/height (CLS prevention)
**Accessibility:** Never remove focus styles, proper heading hierarchy (H1→H2→H3), labels on inputs

## CSS & Design (Material Design 3 Dark)

**Single Source:** `assets/css/style.css` (3,852 lines)
❌ NO inline styles (except technically mandatory)
❌ NO additional global CSS files
✅ USE TOKENS: `var(--md-primary)`, `var(--font-size-h1)`, `var(--space-section)`
**Fonts:** Inter (body: 400/500/600), Montserrat (headings: 700)

## Critical Mistakes to Avoid

1. ❌ Relative paths for partials → ✅ `$_SERVER['DOCUMENT_ROOT'] . '/partials/file.php'`
2. ❌ Wrong partial order → ✅ Follow strict order above
3. ❌ Inline styles/scripts → ✅ Use style.css/main.js (CSP violation)
4. ❌ Editing `/weg/` or `/add/` → ✅ Leave archived files untouched
5. ❌ New global CSS/JS files → ✅ Add to existing style.css/main.js
6. ❌ Missing/duplicate H1 → ✅ Exactly ONE H1 per page
7. ❌ Empty alt text → ✅ Descriptive alt on all images
8. ❌ Hardcoded values → ✅ Use design tokens (var(--token-name))

## Validation Checklist (Before Commit)

```bash
# 1. PHP syntax (MUST pass)
find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"

# 2. Start server & test
php -S localhost:8000 &  # Then test in browser
□ Pages load without errors
□ Browser console (F12) - NO errors
□ Mobile view works
□ Partials loaded (view source)
□ NO inline styles/scripts added
□ Images have alt + width/height
□ Exactly ONE H1 per page
□ Meta complete (title, description, canonical)
```

## Quick Reference

**Key Files:**
- `.htaccess` - Redirects, caching, security headers
- `partials/version.php` - CSS cache version (update when changing style.css)
- `kontakt/setup.sql` - DB schema (contacts, admin_users tables)
- Security: `partials/csrf.php` (1h token expiry), `partials/brute-force-protection.php` (5 attempts/15min)

**Common Errors:**
- PHP syntax: `php -l file.php` shows line number
- 404: Check `.htaccess` rewrites, verify file exists
- JS errors: Check `main.js`, no inline scripts
- Missing partial: Use `$_SERVER['DOCUMENT_ROOT']` absolute path

## Documentation Updates
- Structure/rules → `Agents.md`
- Design/visual → `DESIGN_SYSTEM.md`
- New styles → `assets/css/style.css`
- Move from `/add/` → Remove from `/add/` after
- Old files → Move to `/weg/` (never delete)

## Trust These Instructions
Created by analyzing all docs, testing server, validating 32 PHP files. Search only if incomplete or erroneous. **For architecture/structure: consult Agents.md first.**

---
**Validated:** 2026-01-14 | Automated analysis + manual testing
