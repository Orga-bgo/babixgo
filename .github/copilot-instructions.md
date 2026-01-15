# GitHub Copilot Instructions for babixGO Platform (Monorepo)

This monorepo contains the complete babixGO platform with multiple domains sharing common resources.

## Critical Documents

Before making any changes, **read these files**:

- **This file** - Mandatory rules, structure, and workflows for this monorepo
- **DESIGN_SYSTEM.md** (root) - Brand guide, design tokens, component styles, and governance
- **README.md** (root) - Project overview and orientation

## Monorepo Overview

- **Technology**: Pure HTML, CSS, JavaScript with server-side PHP
- **No Build Tools**: No npm, webpack, bundlers, or compilation steps
- **Deployment**: Direct FTP/SFTP upload to Strato webhosting
- **Architecture**: Single-Domain with path-based routing (Updated 2026-01-15)

---

## CRITICAL: Single-Domain Architecture (Updated 2026-01-15)

**babixGO now uses a SINGLE-DOMAIN architecture** where all functionality is accessible under `babixgo.de/*`:

### Architecture Overview

- ❌ **NO subdomains** (auth.babixgo.de, files.babixgo.de are OBSOLETE and redirect to new paths)
- ✅ **All under babixgo.de** with path-based routing

### URL Structure

**Authentication:**
```
babixgo.de/auth/login         # Login page
babixgo.de/auth/register      # Registration
babixgo.de/auth/logout        # Logout
babixgo.de/auth/verify-email  # Email verification
babixgo.de/auth/forgot-password
babixgo.de/auth/reset-password
```

**User Area (NEW):**
```
babixgo.de/user/              # User dashboard
babixgo.de/user/profile       # Public profile
babixgo.de/user/edit-profile  # Edit profile
babixgo.de/user/settings      # Account settings
babixgo.de/user/my-comments   # User's comments
babixgo.de/user/my-downloads  # Download history
```

**Download Portal:**
```
babixgo.de/files/             # Download overview
babixgo.de/files/browse       # Browse downloads
babixgo.de/files/category/apk # Category view
babixgo.de/files/download/123/apk # Download handler
```

**Admin Panel:**
```
babixgo.de/admin/             # Admin dashboard
babixgo.de/admin/users        # User management
babixgo.de/admin/downloads    # Download management
babixgo.de/admin/comments     # Comment moderation
```

### Directory Structure

```
/babixgo.de/
├── .htaccess                 # Unified routing configuration
├── auth/                     # Authentication system
│   ├── login.php
│   ├── register.php
│   └── includes/
│       ├── auth-check.php
│       └── form-handlers/
├── user/                     # User area (NEW)
│   ├── index.php            # Dashboard
│   ├── profile.php
│   ├── edit-profile.php
│   └── includes/
│       └── auth-check.php
├── files/                    # Download portal
│   ├── index.php
│   ├── download.php
│   └── includes/
├── admin/                    # Admin panel
│   ├── .htaccess            # Additional protection
│   ├── index.php
│   └── includes/
│       └── admin-check.php
└── assets/
    └── css/
        └── user.css         # User area styles
```

### Path Constants (MANDATORY)

**All files MUST use:**

```php
define('BASE_PATH', dirname(__DIR__, 2) . '/');  // Points to /babixgo/
define('SHARED_PATH', BASE_PATH . 'shared/');

// Load shared resources
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Include partials
require_once SHARED_PATH . 'partials/header.php';
require_once SHARED_PATH . 'partials/footer.php';
```

### Access Control

**User Area Protection:**
```php
// At top of all /user/*.php files
require_once __DIR__ . '/includes/auth-check.php';
// Redirects to /auth/login if not logged in
// Sets: $currentUserId, $currentUsername, $currentUserEmail, $currentUserRole
```

**Admin Area Protection:**
```php
// At top of all /admin/*.php files
require_once __DIR__ . '/includes/admin-check.php';
// Requires login AND admin role
// Shows 403 if not admin
```

### Routing Rules (.htaccess)

**Download Handler:**
```apache
# /files/download/123/apk → files/download.php?id=123&type=apk
RewriteRule ^files/download/([0-9]+)/(apk|exe|scripts)$ files/download.php?id=$1&type=$2 [L,QSA]
```

**Category Routing:**
```apache
# /files/category/apk → files/category.php?type=apk
RewriteRule ^files/category/(apk|exe|scripts)$ files/category.php?type=$1 [L,QSA]
```

**Clean URLs:**
```apache
# Remove .php extension for auth, user, files, admin
RewriteCond %{REQUEST_URI} ^/(auth|user|files|admin)/
RewriteRule ^(.+)$ $1.php [L]
```

### Migration from Multi-Domain

**Old URLs (OBSOLETE):**
- ❌ `auth.babixgo.de/login.php`
- ❌ `files.babixgo.de/category.php?type=apk`

**New URLs (CURRENT):**
- ✅ `babixgo.de/auth/login`
- ✅ `babixgo.de/files/category/apk`

**Backward Compatibility:**
- Old subdomains redirect with 301 to new paths
- Session cookie domain unchanged (`.babixgo.de`)
- Login state persists across migration

### Critical Rules (Single-Domain)

**DO NOT:**
- ❌ Create subdomain-specific code (auth.babixgo.de, files.babixgo.de)
- ❌ Use absolute domain URLs in internal links
- ❌ Hardcode `/auth/public/` or `/files.babixgo.de/public/` paths
- ❌ Create separate session systems per section

**DO:**
- ✅ Use path-based routing (`/auth/`, `/user/`, `/files/`, `/admin/`)
- ✅ Use relative paths for internal navigation
- ✅ Keep all sections under `/babixgo.de/`
- ✅ Use shared session across all sections

### Database Schema Updates

**New tables added for single-domain:**

```sql
-- Email logging
CREATE TABLE email_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    email_type ENUM('verification', 'password_reset', 'welcome', 'notification', 'custom'),
    success BOOLEAN DEFAULT 0,
    error_message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ...
);

-- Remember me sessions
CREATE TABLE user_sessions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    ...
);

-- Activity tracking
CREATE TABLE user_activity (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type ENUM('login', 'logout', 'register', 'profile_update', ...),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ...
);
```

### PWA Configuration

**Manifest updates:**
- All icon paths use `/shared/assets/icons/`
- Shortcuts point to `/files/`, `/user/`, `/auth/login`
- Screenshots for app store listings
- Categories: productivity, utilities, downloads

**Service Worker:**
- Unified caching for all sections
- No cache for `/admin/` (fresh data)
- No cache for auth POST requests
- Cache-first for static assets
- Network-first for HTML

---

## Monorepo Cleanup Status (COMPLETED)

**Last Cleanup**: January 14, 2026  
**Current Version**: 1.0.15  
**Status**: ✅ PRODUCTION READY

### Cleanup Summary

The monorepo underwent a comprehensive cleanup to eliminate duplicates, standardize structure, and establish single sources of truth. **All future development must follow this clean structure.**

### What Was Cleaned (Historical - v1.0.15)

✅ **Partials Consolidation**:
- Removed 13 duplicate partial files from `babixgo.de/partials/`
- Consolidated all partials into `/shared/partials/` (v1.0.15)
- Updated 124 references across 16 page files
- Fixed internal partial references to use `__DIR__` instead of `DOCUMENT_ROOT`

✅ **Single-Domain Migration (v2.0.0)**:
- Migrated from multi-domain (auth.babixgo.de, files.babixgo.de) to single-domain
- All functionality now under `/babixgo.de/` with path-based routing
- Created `/auth/`, `/user/`, `/files/`, `/admin/` sections
- Unified `.htaccess` with clean URL routing
- Enhanced PWA with shortcuts and better caching

✅ **Documentation**:
- Created `MIGRATION_GUIDE.md` (13.7KB migration reference)
- Created `DEPLOYMENT_CHECKLIST.md` (13.6KB deployment guide)
- Updated `README.md` with single-domain structure
- Added comprehensive documentation for v2.0.0

### Critical Rules (Current v2.0.0)

**DO NOT**:
- ❌ Create duplicate partials in domain directories
- ❌ Use old subdomain references (auth.babixgo.de, files.babixgo.de)
- ❌ Use `$_SERVER['DOCUMENT_ROOT'] . '/partials/'` (old pattern)
- ❌ Create separate auth or files at domain root
- ❌ Duplicate shared resources in section folders

**DO**:
- ✅ Use `dirname(__DIR__, 2) . '/'` for BASE_PATH in all sections
- ✅ Use `SHARED_PATH . 'partials/'` for partials
- ✅ Reference shared assets from `/shared/assets/`
- ✅ Use path-based routing (/auth/, /user/, /files/, /admin/)
- ✅ Follow the structure below exactly

### Current Structure (v2.0.0 - Single-Domain)

**Use this structure as the authoritative reference:**

```
/
├── shared/                      # ✅ Single source of truth
│   ├── assets/
│   │   ├── css/
│   │   │   ├── main.css        # Global styles
│   │   │   ├── style.css       # Additional styles
│   │   │   └── admin.css       # Admin panel styles
│   │   ├── js/
│   │   │   └── main.js         # Global scripts
│   │   ├── icons/              # Shared icons (PWA)
│   │   ├── images/             # Shared images
│   │   └── logo/               # Logo assets
│   │
│   ├── classes/                 # PHP classes
│   │   ├── Database.php        # Database wrapper
│   │   ├── User.php            # User management
│   │   ├── Session.php         # Session handling
│   │   ├── Download.php        # Download management
│   │   └── Comment.php         # Comment management
│   │
│   ├── config/                  # Configuration files
│   │   ├── database.php        # Database config
│   │   ├── session.php         # Session config
│   │   ├── autoload.php        # Class autoloader
│   │   └── email.php           # Email config (references email.local.php)
│   │
│   ├── partials/                # ✅ Consolidated partials
│   │   ├── head-meta.php       # Meta tags
│   │   ├── head-links.php      # CSS/Font links
│   │   ├── header.php          # Site header with user menu
│   │   ├── footer.php          # Site footer
│   │   ├── footer-scripts.php  # Footer scripts
│   │   ├── nav.php             # Navigation
│   │   ├── tracking.php        # Analytics
│   │   ├── cookie-banner.php   # Cookie consent
│   │   ├── critical-css.php    # Critical CSS
│   │   ├── csrf.php            # CSRF protection
│   │   ├── structured-data.php # Schema.org data
│   │   ├── brute-force-protection.php
│   │   └── version.php         # BABIXGO_VERSION
│   │
│   └── create-tables.sql        # Database schema with all tables
│
├── downloads/                   # ✅ Protected download storage
│   ├── .htaccess               # Access denied (CRITICAL)
│   ├── apk/                    # Android APK files
│   ├── exe/                    # Windows executables
│   └── scripts/                # Script files
│       ├── bash/
│       ├── python/
│       └── powershell/
│
└── babixgo.de/                  # ✅ UNIFIED SINGLE DOMAIN
    ├── .htaccess               # Unified routing configuration
    ├── index.php               # Homepage
    ├── 404.php, 403.php, 500.php # Error pages
│   ├── .htaccess               # Web server config
    │
    ├── assets/                 # Domain-specific assets
    │   ├── css/
    │   │   ├── style.css       # Main site styles
    │   │   └── user.css        # User area styles (NEW)
    │   ├── js/
    │   ├── icons/
    │   ├── img/
    │   └── logo/
    │
    ├── public/                 # PWA assets
    │   ├── manifest.json       # Updated with all icons & shortcuts
    │   ├── sw.js               # Unified service worker
    │   └── offline.html        # Offline fallback
    │
    ├── auth/                   # ✅ Authentication (babixgo.de/auth/*)
    │   ├── login.php
    │   ├── register.php
    │   ├── logout.php
    │   ├── verify-email.php
    │   ├── forgot-password.php
    │   ├── reset-password.php
    │   └── includes/
    │       ├── auth-check.php
    │       ├── admin-check.php
    │       └── form-handlers/
    │
    ├── user/                   # ✅ User Area (babixgo.de/user/*) - NEW
    │   ├── index.php           # Dashboard
    │   ├── profile.php         # Public profile
    │   ├── edit-profile.php    # Edit profile
    │   ├── settings.php        # Account settings
    │   ├── my-comments.php     # User's comments
    │   ├── my-downloads.php    # Download history
    │   └── includes/
    │       └── auth-check.php
    │
    ├── files/                  # ✅ Download Portal (babixgo.de/files/*)
    │   ├── index.php           # Download listing
    │   ├── browse.php          # Browse downloads
    │   ├── category.php        # Category view
    │   ├── download.php        # Secure download handler
    │   └── includes/
    │       └── [helper files]
    │
    ├── admin/                  # ✅ Admin Panel (babixgo.de/admin/*)
    │   ├── .htaccess           # Additional protection
    │   ├── index.php           # Admin dashboard
    │   ├── users.php           # User management
    │   ├── user-edit.php       # Edit user
    │   ├── downloads.php       # Download management
    │   ├── download-edit.php   # Edit download
    │   ├── comments.php        # Comment moderation
    │   └── includes/
    │       ├── admin-check.php
    │       └── handlers/
    │
    └── [existing content]/     # Existing site content
        ├── accounts/
        ├── anleitungen/
        ├── wuerfel/
        ├── sticker/
        └── ...
```

### URL Structure (Single-Domain v2.0.0)

| Section | URL Pattern | Status |
|---------|-------------|--------|
| **Main Site** | `babixgo.de/` | ✅ Active |
| **Authentication** | `babixgo.de/auth/*` | ✅ Active |
| **User Area** | `babixgo.de/user/*` | ✅ NEW |
| **Download Portal** | `babixgo.de/files/*` | ✅ Active |
| **Admin Panel** | `babixgo.de/admin/*` | ✅ Active |

**Old subdomains (OBSOLETE):**
- ❌ `auth.babixgo.de` → 301 redirects to `babixgo.de/auth/`
- ❌ `files.babixgo.de` → 301 redirects to `babixgo.de/files/`

### File Reference Patterns (v2.0.0)

**Correct patterns to use:**

```php
// In /auth/*.php, /user/*.php, /files/*.php, /admin/*.php
define('BASE_PATH', dirname(__DIR__, 2) . '/');  // Points to /babixgo/
define('SHARED_PATH', BASE_PATH . 'shared/');

// Shared partials
<?php require SHARED_PATH . 'partials/header.php'; ?>

// Shared assets in HTML
<link rel="stylesheet" href="/shared/assets/css/main.css">
<script src="/shared/assets/js/main.js"></script>

// Shared classes
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/classes/Database.php';

// Path constants (recommended)
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
require_once SHARED_PATH . 'config/database.php';
```

**Deprecated patterns (DO NOT USE):**

```php
// ❌ OLD - Do not use
<?php require $_SERVER['DOCUMENT_ROOT'] . '/partials/header.php'; ?>

// ❌ OLD - babixgo.de/partials/ no longer exists
<?php require __DIR__ . '/partials/header.php'; ?>

// ❌ OLD - /files/ renamed to /files.babixgo.de/
<?php require '/files/public/index.php'; ?>
```

### Verification Checklist

Use this checklist when reviewing code or making changes:

- [ ] No duplicate partials in domain directories
- [ ] All partials referenced from `/shared/partials/`
- [ ] Using `dirname($_SERVER['DOCUMENT_ROOT'])` for shared access
- [ ] No files in `/auth/` root except `.htaccess`
- [ ] PWA files present in all domain `public/` directories
- [ ] Domain-specific assets minimal (extend shared, don't duplicate)
- [ ] SMTP credentials not committed (use email.local.php)
- [ ] Download files in `/downloads/` with `.htaccess` protection

### Cleanup Documentation Reference

For detailed information about the cleanup:

- **CLEANUP_REPORT.md**: Complete change log with before/after comparison
- **DEPLOYMENT_GUIDE.md**: Step-by-step deployment instructions
- **README.md**: Updated structure documentation
- **Git commits**: 8e17664 (partials), c9c6d47 (auth), f11619f (files), 384aeef (SMTP)

---

## Repository Structure

```
/
├── shared/                          # SHARED across ALL domains
│   ├── assets/
│   │   ├── css/
│   │   │   └── main.css            # Single source of truth for global styles
│   │   ├── js/
│   │   │   └── main.js             # Global scripts
│   │   └── icons/                  # All SVG icons (shared)
│   ├── classes/                     # PHP classes (Database, User, Session, etc.)
│   ├── config/                      # Configuration files (database, session)
│   └── partials/                    # PHP partials (header, footer, nav)
│
├── downloads/                       # Download files (NOT publicly accessible)
│   ├── apk/
│   ├── scripts/
│   └── exe/
│
├── babixgo.de/                      # Main website
│   ├── public/                      # Document Root (web-accessible)
│   │   ├── index.php
│   │   ├── .htaccess
│   │   └── assets/                  # Domain-specific assets (if needed)
│   ├── includes/
│   └── templates/
│
├── files.babixgo.de/                # Download portal
│   ├── public/                      # Document Root
│   │   ├── index.php
│   │   ├── download.php
│   │   ├── .htaccess
│   │   └── assets/                  # Domain-specific assets (if needed)
│   └── includes/
│
└── auth.babixgo.de/                 # Authentication & Admin
    ├── public/                      # Document Root
    │   ├── index.php
    │   ├── login.php
    │   ├── register.php
    │   ├── .htaccess
    │   ├── assets/                  # Auth-specific assets
    │   │   ├── css/
    │   │   │   ├── auth.css        # Auth-specific styles only
    │   │   │   └── admin.css       # Admin-specific styles only
    │   │   └── js/
    │   │       ├── form-validation.js
    │   │       └── admin.js
    │   └── admin/                   # Admin panel
    │       ├── index.php
    │       ├── users.php
    │       ├── downloads.php
    │       └── comments.php
    └── includes/
```

## Core Principles

1. **Shared First** - Use `/shared/` for all common resources
2. **No Duplicates** - Never duplicate shared content in domain folders
3. **Document Root = public/** - All domains point to their `public/` folder
4. **Security** - Sensitive files stay outside `public/`, downloads managed via PHP
5. **Maintain Structure** - Respect the monorepo organization
6. **Simple Over Clever** - Prefer straightforward, maintainable solutions

## Shared Resources (MANDATORY USE)

### Shared Assets

**ALL domains MUST include shared assets:**

```html
<!-- In ALL domain templates -->
<link rel="stylesheet" href="/shared/assets/css/main.css">
<link rel="stylesheet" href="/assets/css/[domain-specific].css">

<script src="/shared/assets/js/main.js"></script>
<script src="/assets/js/[domain-specific].js"></script>
```

**Rules:**
- `/shared/assets/css/main.css` - Single source of truth for global styles
- `/shared/assets/js/main.js` - Global scripts and utilities
- Domain-specific CSS/JS should **extend** (not override) shared styles
- NO additional global CSS or JS files allowed in shared/

### Shared PHP Classes

**Location**: `/shared/classes/`

```php
<?php
// At the top of every domain entry point (index.php, etc.)
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('DOWNLOADS_PATH', BASE_PATH . 'downloads/');

// Autoloader for shared classes
spl_autoload_register(function ($class) {
    $file = SHARED_PATH . 'classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load shared configs
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
```

**Never use:**
- Relative paths for shared resources
- `__DIR__` for cross-domain includes
- Short PHP tags (`<?`)

### Shared PHP Partials

**Location**: `/shared/partials/`

**MUST be included in ALL domains using:**

```php
<?php require SHARED_PATH . 'partials/header.php'; ?>
```

#### Required Inclusion Order (ALL Domains)

**In `<head>`:**
1. Shared config loading (database, session)
2. Page-specific: `<title>`, `<meta name="description">`, `<link rel="canonical">`
3. CSS: `/shared/assets/css/main.css` first, then domain-specific CSS

**After `<body>`:**
4. `<?php require SHARED_PATH . 'partials/header.php'; ?>`

**Before `</body>`:**
5. `<?php require SHARED_PATH . 'partials/footer.php'; ?>`
6. Scripts: `/shared/assets/js/main.js` first, then domain-specific JS

### Shared Partials Available

- **header.php** - Site-wide navigation with cross-domain links
- **footer.php** - Site-wide footer with links
- **nav.php** - Navigation component
- (Add more as created)

**Rules:**
- Partials handle cross-domain navigation automatically
- Session state (logged-in user) is shared via cookie domain `.babixgo.de`
- Partials use Material Design tokens from `main.css`

## Cross-Domain Session Management

**Location**: `/shared/config/session.php`

```php
<?php
// Session configuration (shared across all domains)
ini_set('session.cookie_domain', '.babixgo.de');
ini_set('session.cookie_path', '/');
ini_set('session.cookie_secure', '1');      // HTTPS only
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

session_name('BABIXGO_SESSION');
session_start();
```

**Important:**
- All domains share the same session via `.babixgo.de` cookie domain
- Login on `auth.babixgo.de` → user is logged in on all domains
- Check `$_SESSION['user_id']` to detect logged-in state

## HTML Standards (ALL Domains)

- **Valid, semantic HTML5**
- **Exactly ONE H1 per page**
- **Images always have `alt` attributes**
- **Required meta per page**: title, description, canonical
- **Proper use of links vs buttons** (semantic correctness)

### Heading Hierarchy Rules

| Element | Location | Icon | Wrapper Class |
|---------|----------|------|---------------|
| **H1** | Hero section (first section-card) | No | `.welcome-title` |
| **H2** | Section titles (outside content box) | Yes (always) | `.section-header` |
| **H3** | Inside box/card | No (gradient underline via `::after`) | In `.content-card` or `.section-card` |

**H2 Structure Pattern:**
```html
<div class="section-header">
  <h2><img src="/shared/assets/icons/[icon].svg" class="icon" alt="[Description]">[Title]</h2>
</div>
```

## Design System (MANDATORY)

**Single Source of Truth**: `/shared/assets/css/main.css`

- **Read DESIGN_SYSTEM.md** before any visual changes
- **NO inline styles** in production (only if technically mandatory)
- **Use design tokens** instead of hardcoded values
- **Design basis**: Material Design 3 Dark Medium Contrast
- **Fonts**: Inter (400, 500, 600) for body, Montserrat (700) for headings

### CSS Architecture

```css
/* In /shared/assets/css/main.css */
:root {
  /* Typography tokens */
  --font-size-h1: 2rem;
  --font-size-h2: 1.5rem;
  --font-size-h3: 1.2rem;
  --font-size-body: 1rem;
  
  /* Color tokens (Material Design) */
  --md-primary: #...;
  --md-secondary: #...;
  --md-surface: #...;
  --md-surface-container-low: #...;
  
  /* Spacing tokens */
  --spacing-xs: 0.5rem;
  --spacing-sm: 1rem;
  --spacing-md: 1.5rem;
  --spacing-lg: 2rem;
}
```

**Domain-Specific CSS:**
- Should **extend** shared styles, not override
- Use same design tokens from `main.css`
- Keep domain-specific styles minimal

### Example: auth.css

```css
/* auth.babixgo.de/public/assets/css/auth.css */
/* Extends /shared/assets/css/main.css */

.auth-container {
  max-width: 400px;
  margin: var(--spacing-lg) auto;
}

.form-card {
  background: var(--md-surface-container-low);
  padding: var(--spacing-lg);
  border-radius: var(--radius-md);
}

/* DO NOT redefine tokens that exist in main.css */
```

## Security Requirements (CRITICAL)

### File Access Control

**Downloads folder protection** (`/downloads/.htaccess`):
```apache
Order Deny,Allow
Deny from all
```

**Only PHP scripts in `files.babixgo.de/public/` can serve downloads**

### Password Security
- Use `password_hash()` with `PASSWORD_DEFAULT`
- Use `password_verify()` for login
- Never store plain text passwords

### SQL Injection Prevention
- Use PDO prepared statements exclusively
- Never concatenate user input in queries

### XSS Prevention
- Use `htmlspecialchars()` for all output
- `ENT_QUOTES` flag for attribute contexts

### CSRF Protection
- Generate CSRF token per session
- Include in all forms as hidden field
- Validate on form submission

## Domain-Specific Guidelines

### babixgo.de (Main Site)
- Primary marketing/content website
- Uses shared header/footer for navigation
- Displays logged-in user info from shared session
- Comment system integrates with auth.babixgo.de

### files.babixgo.de (Download Portal)
- Download management via `download.php` handler
- Never serve files directly via URL
- All downloads logged in database
- Optional: Require login for premium downloads
- File validation before serving

### auth.babixgo.de (Auth & Admin)
- Handles all authentication (register, login, logout)
- Email verification system
- Password reset functionality
- User profile management
- **Admin panel** for:
  - User management
  - Download management (upload, edit, delete)
  - Comment moderation
- HTTPS enforced via .htaccess
- Additional security headers

## Database Schema

**Shared database** for all domains:

### users
- id, username, email, password_hash
- description, friendship_link
- is_verified, verification_token
- role (user, admin)
- created_at, updated_at

### comments
- id, user_id, domain, content_id, comment
- status (approved, pending, spam)
- created_at

### downloads
- id, filename, filepath, filetype
- filesize, version, description
- download_count, active
- created_at, updated_at

### download_logs
- id, file_id, user_id
- ip_address, user_agent
- downloaded_at

## Accessibility Requirements (ALL Domains)

- Do not remove focus styles
- Maintain logical heading hierarchy
- Forms must have labels
- Keyboard navigation must work
- ARIA labels where needed
- Semantic HTML5 elements

## Quality Assurance Before Deployment

- [ ] No broken internal links
- [ ] All shared partials included correctly
- [ ] Shared assets (CSS/JS) loading properly
- [ ] Cross-domain session works (login persists)
- [ ] Mobile responsive on all domains
- [ ] Browser console has no errors
- [ ] Database queries use prepared statements
- [ ] File uploads validated (if applicable)
- [ ] HTTPS enforced on all domains

## Coding Standards (ALL Domains)

- Follow existing code style and patterns
- Use PHP for server-side logic and includes
- Keep JavaScript in appropriate asset files
- No CSP violations (no inline scripts)
- Icons in `/shared/assets/icons/`, not inline SVG
- Consistent indentation (4 spaces or 2 spaces, but be consistent)
- Meaningful variable names
- Comments for complex logic

## Development Workflow

1. **Read this file** before making changes
2. **Check DESIGN_SYSTEM.md** for design tokens
3. **Use shared resources** - never duplicate
4. **Test locally**: `php -S localhost:8000` in domain's `public/` folder
5. **Test cross-domain**: Verify session sharing works
6. **Validate HTML** and check mobile view
7. **Ensure no console errors** before deployment
8. **Test on all three domains** if changes affect shared resources

## Deployment Process

**FTP Structure:**
```
/var/www/websites/
├── shared/          → Upload to /shared/
├── downloads/       → Upload to /downloads/
├── babixgo.de/      → Upload to /babixgo.de/
├── files/           → Upload to /files/ (points to files.babixgo.de)
└── auth/            → Upload to /auth/ (points to auth.babixgo.de)
```

**Deployment order:**
1. Upload `/shared/` changes first
2. Upload domain-specific changes
3. Test each domain after upload
4. Verify cross-domain functionality

## File Upload Handling (files.babixgo.de)

```php
// In files.babixgo.de/public/download.php
$allowed_types = [
    'apk' => ['application/vnd.android.package-archive'],
    'exe' => ['application/x-msdownload'],
    'scripts' => ['text/plain', 'text/x-python', 'application/x-sh']
];

// Validate file size (max 500MB)
// Validate MIME type with finfo_file()
// Generate safe filename
// Move to DOWNLOADS_PATH . $filetype . '/'
// Store metadata in database
```

## Important Notes

- This is a **monorepo with shared resources**
- Deployment is manual via FTP/SFTP to Strato
- No build process or compilation
- All domains share one database
- Sessions shared via cookie domain `.babixgo.de`
- Downloads managed via PHP, never direct access
- Database credentials from `/shared/config/database.php`
- Never commit secrets or sensitive data

## Design Tokens and Components

Refer to **DESIGN_SYSTEM.md** (root) for:
- Complete color token system
- Typography scales and usage
- Component patterns (buttons, cards, forms, etc.)
- Spacing and layout tokens
- Shadow and elevation system
- Icon usage guidelines

## Common Pitfalls to Avoid

❌ **Don't:**
- Duplicate CSS/JS from shared/ into domain folders
- Use relative paths for shared resources
- Override shared design tokens without reason
- Create separate session systems per domain
- Serve downloads directly via URL
- Hardcode colors, fonts, or spacing
- Skip CSRF protection on forms
- Use inline styles in production

✅ **Do:**
- Use shared resources via absolute paths
- Extend shared styles in domain-specific CSS
- Follow the design system strictly
- Use shared partials for header/footer
- Validate and sanitize all inputs
- Test cross-domain session functionality
- Use design tokens for consistency
- Keep domain-specific code minimal

## Change Management

- Structural changes → **update this file**
- Visual/design changes → **update DESIGN_SYSTEM.md**
- New shared components → add to `/shared/`
- New domain features → add to appropriate domain folder
- Database schema changes → document in migration notes
- Security updates → test on all domains

## Testing Checklist

Before merging any PR:

- [ ] Shared resources accessible from all domains
- [ ] No duplicate code between shared/ and domains
- [ ] Design system tokens used correctly
- [ ] Cross-domain session works
- [ ] Mobile responsive on all domains
- [ ] No console errors
- [ ] SQL injection protected
- [ ] XSS escaped properly
- [ ] CSRF tokens validated
- [ ] File uploads secured (if applicable)
- [ ] HTML validates
- [ ] Accessibility maintained

---

**Remember**: Shared first. No duplicates. Security always. Read DESIGN_SYSTEM.md. Test everywhere.

# Ergänzung für GitHub Copilot Instructions

Füge diesen Abschnitt am Ende der Copilot Instructions hinzu:

---

## Manual Steps Documentation (CRITICAL)

**If your implementation requires ANY manual steps** (database setup, file permissions, configuration, etc.), you **MUST document them explicitly and completely**.

### Required Documentation Format

Create a `SETUP.md` or `DEPLOYMENT.md` file with the following structure:

```markdown
# Manual Setup Steps for [Feature/Domain Name]

## Prerequisites
- List exact requirements (PHP version, database access, etc.)
- List required access (FTP credentials, database credentials, etc.)
- List tools needed (FTP client, phpMyAdmin, etc.)

## Step-by-Step Instructions

### Step 1: [Action Name]
**Location**: [Exact path or location]
**Action**: [Exactly what to do]
**Command/Code**: 
```
[Exact command or code to execute]
```
**Expected Result**: [What should happen after this step]
**Troubleshooting**: [Common issues and solutions]

### Step 2: [Next Action]
[Continue same format...]

## Verification Steps
1. How to verify each step worked correctly
2. Expected outputs or indicators of success
3. What to check if something doesn't work

## Rollback Instructions
If something goes wrong, how to undo changes

## Support Information
Where to find help if steps fail
```

---

## Environment Variables & GitHub Secrets (MANDATORY)

**CRITICAL**: Never commit sensitive credentials to the repository. All SMTP and sensitive configuration must use environment variables or GitHub Secrets.

### GitHub Repository Secrets Configuration

The following secrets are configured in the GitHub repository and **MUST** be used for email functionality:

#### Available Secrets

```
SMTP_HOST                    # SMTP server hostname (e.g., smtp-relay.brevo.com)
SMTP_PORT                    # SMTP port (587 for TLS, 465 for SSL)
SMTP_USER                    # SMTP username (Brevo login email)
SMTP_KEY                     # SMTP password/API key (NOT the login password!)
SMTP_SENDER_REGISTRATION     # Sender email for registration emails (e.g., noreply@babixgo.de)
```

### Accessing Secrets in PHP

**IMPORTANT**: GitHub Secrets are NOT directly accessible in PHP on the server. You need to create a deployment process that converts secrets to environment variables or a config file.

#### Option 1: Environment Variables (Recommended for Production)

**Setup on Strato** (via .htaccess or php.ini):

**File**: `/auth/public/.htaccess`

```apache
# Environment variables for SMTP (set these manually on server)
SetEnv SMTP_HOST "smtp-relay.brevo.com"
SetEnv SMTP_PORT "587"
SetEnv SMTP_USER "your-email@example.com"
SetEnv SMTP_KEY "your-smtp-key-here"
SetEnv SMTP_SENDER_REGISTRATION "noreply@babixgo.de"
```

**Usage in PHP**:

```php
<?php
// In /shared/config/email.php

// SMTP Settings from environment variables
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USER'));
define('SMTP_PASSWORD', getenv('SMTP_KEY'));
define('MAIL_FROM', getenv('SMTP_SENDER_REGISTRATION') ?: 'noreply@babixgo.de');

// Validate required credentials
if (!SMTP_USERNAME || !SMTP_PASSWORD) {
    error_log('CRITICAL: SMTP credentials not configured');
    throw new Exception('Email system not configured');
}
```

#### Option 2: Local Config File (Easier for Strato, but needs manual setup)

**File**: `/shared/config/email.local.php` (NEVER commit to Git!)

```php
<?php
/**
 * Local Email Configuration
 * This file contains sensitive credentials and should NEVER be committed to Git
 * 
 * Values should match GitHub Secrets:
 * - SMTP_HOST
 * - SMTP_PORT
 * - SMTP_USER
 * - SMTP_KEY
 * - SMTP_SENDER_REGISTRATION
 */

define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'your-brevo-email@example.com'); // SMTP_USER secret
define('SMTP_PASSWORD', 'your-smtp-key-here');           // SMTP_KEY secret
define('MAIL_FROM', 'noreply@babixgo.de');               // SMTP_SENDER_REGISTRATION secret
define('MAIL_FROM_NAME', 'BabixGo Platform');
define('MAIL_REPLY_TO', 'support@babixgo.de');

// Additional settings
define('MAIL_CHARSET', 'UTF-8');
define('MAIL_SMTP_DEBUG', 0); // 0=off, 2=verbose (for debugging)
```

**Update**: `/shared/config/email.php`

```php
<?php
/**
 * Email Configuration Loader
 * Loads credentials from local config file or environment variables
 */

// Try to load local config first (for Strato hosting)
$localConfig = __DIR__ . '/email.local.php';
if (file_exists($localConfig)) {
    require_once $localConfig;
} else {
    // Fallback to environment variables
    define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp-relay.brevo.com');
    define('SMTP_PORT', (int)getenv('SMTP_PORT') ?: 587);
    define('SMTP_SECURE', 'tls');
    define('SMTP_USERNAME', getenv('SMTP_USER'));
    define('SMTP_PASSWORD', getenv('SMTP_KEY'));
    define('MAIL_FROM', getenv('SMTP_SENDER_REGISTRATION') ?: 'noreply@babixgo.de');
    define('MAIL_FROM_NAME', 'BabixGo Platform');
    define('MAIL_REPLY_TO', 'support@babixgo.de');
    define('MAIL_CHARSET', 'UTF-8');
    define('MAIL_SMTP_DEBUG', 0);
}

// Validate required credentials exist
if (!defined('SMTP_USERNAME') || !defined('SMTP_PASSWORD') || 
    empty(SMTP_USERNAME) || empty(SMTP_PASSWORD)) {
    error_log('CRITICAL: SMTP credentials not configured. Check email.local.php or environment variables.');
    throw new Exception('Email system not properly configured');
}

// Rate Limiting (Brevo: 300/day)
define('MAIL_RATE_LIMIT', 300);
define('MAIL_RATE_PERIOD', 86400); // 24 hours
```

### .gitignore Configuration

**CRITICAL**: Add to `/.gitignore`:

```gitignore
# Email credentials (NEVER commit these!)
/shared/config/email.local.php
/shared/config/database.local.php
/shared/config/*.local.php

# Environment files
.env
.env.local
.env.*.local

# Sensitive configuration
**/config/local.php
**/config/*.local.php

# SMTP test files (temporary)
**/test-email.php
**/email-test.php
```

### Manual Setup Instructions for Deployment

When deploying to Strato, the following manual steps are required:

#### Step 1: Create Local Config File on Server

**Location**: Via FTP/SFTP at `/shared/config/email.local.php`

**Action**: Create file with the following content (replace with actual secrets)

```php
<?php
/**
 * SMTP Configuration for babixgo.de
 * 
 * ⚠️ SECURITY: This file contains sensitive credentials
 * - NEVER commit to Git
 * - Set file permissions to 640 (rw-r-----)
 * - Only readable by web server user
 */

// Brevo SMTP Configuration
// Values from GitHub Secrets
define('SMTP_HOST', 'smtp-relay.brevo.com');        // SMTP_HOST secret
define('SMTP_PORT', 587);                            // SMTP_PORT secret
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'YOUR_BREVO_EMAIL');        // SMTP_USER secret
define('SMTP_PASSWORD', 'YOUR_BREVO_SMTP_KEY');     // SMTP_KEY secret

// Sender Configuration
define('MAIL_FROM', 'noreply@babixgo.de');          // SMTP_SENDER_REGISTRATION secret
define('MAIL_FROM_NAME', 'BabixGo Platform');
define('MAIL_REPLY_TO', 'support@babixgo.de');

// Email Settings
define('MAIL_CHARSET', 'UTF-8');
define('MAIL_SMTP_DEBUG', 0); // Set to 2 for debugging, 0 for production

// Rate Limiting
define('MAIL_RATE_LIMIT', 300);      // Brevo free tier: 300 emails/day
define('MAIL_RATE_PERIOD', 86400);   // 24 hours
```

**How to get values from GitHub Secrets:**
1. Go to GitHub repository
2. Settings → Secrets and variables → Actions
3. Copy values from:
   - `SMTP_HOST` → Already set correctly
   - `SMTP_USER` → Replace `YOUR_BREVO_EMAIL`
   - `SMTP_KEY` → Replace `YOUR_BREVO_SMTP_KEY`
   - `SMTP_SENDER_REGISTRATION` → Already set correctly

#### Step 2: Set File Permissions

**Via FTP client** (FileZilla, WinSCP):

1. Right-click on `/shared/config/email.local.php`
2. File permissions → `640` (rw-r-----)
3. Owner: www-data (or web server user)
4. Group: www-data

**Via SSH** (if available):

```bash
chmod 640 /var/www/websites/shared/config/email.local.php
chown www-data:www-data /var/www/websites/shared/config/email.local.php
```

**Expected Result**: File is readable by PHP but not accessible via web browser

#### Step 3: Verify Configuration

**Create temporary test file**: `/auth/public/verify-smtp-config.php`

```php
<?php
/**
 * SMTP Configuration Verification
 * ⚠️ DELETE THIS FILE AFTER VERIFICATION!
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

try {
    require_once SHARED_PATH . 'config/email.php';
    
    echo "<h1>SMTP Configuration Check</h1>";
    
    echo "<h2>✅ Configuration Loaded Successfully</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
    
    $checks = [
        'SMTP_HOST' => SMTP_HOST,
        'SMTP_PORT' => SMTP_PORT,
        'SMTP_USERNAME' => SMTP_USERNAME ? '***SET***' : '❌ NOT SET',
        'SMTP_PASSWORD' => SMTP_PASSWORD ? '***SET***' : '❌ NOT SET',
        'MAIL_FROM' => MAIL_FROM,
    ];
    
    foreach ($checks as $key => $value) {
        $status = (strpos($value, 'NOT SET') === false) ? '✅' : '❌';
        echo "<tr><td>{$key}</td><td>{$value}</td><td>{$status}</td></tr>";
    }
    
    echo "</table>";
    
    if (!SMTP_USERNAME || !SMTP_PASSWORD) {
        echo "<h2 style='color: red;'>❌ CRITICAL: Missing credentials!</h2>";
        echo "<p>Check /shared/config/email.local.php exists and contains correct values.</p>";
    } else {
        echo "<h2 style='color: green;'>✅ All credentials configured</h2>";
        echo "<p>You can now test sending an email.</p>";
    }
    
} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ Configuration Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr><p><strong>⚠️ SECURITY WARNING: DELETE THIS FILE IMMEDIATELY AFTER VERIFICATION!</strong></p>";
?>
```

**Access**: https://auth.babixgo.de/verify-smtp-config.php

**Expected Output**: All settings show ✅ with credentials masked

**After verification**: DELETE this file immediately!

#### Step 4: Test Email Sending

**Create temporary test file**: `/auth/public/test-smtp-send.php`

```php
<?php
/**
 * SMTP Send Test
 * ⚠️ DELETE THIS FILE AFTER TESTING!
 */

define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/email.php';
require_once SHARED_PATH . 'classes/Email.php';

// ⚠️ CHANGE THIS TO YOUR EMAIL!
$testRecipient = 'your-email@example.com';

$emailSender = new Email($db);

try {
    $success = $emailSender->send(
        $testRecipient,
        'SMTP Test - BabixGo',
        '<h1>✅ SMTP funktioniert!</h1><p>Diese Testmail wurde erfolgreich von BabixGo versendet.</p>',
        'SMTP funktioniert! Diese Testmail wurde erfolgreich von BabixGo versendet.'
    );
    
    if ($success) {
        echo "<h1 style='color: green;'>✅ Email erfolgreich versendet!</h1>";
        echo "<p>Überprüfen Sie Ihr Postfach: <strong>{$testRecipient}</strong></p>";
        echo "<p>Falls die Email nicht ankommt, prüfen Sie den Spam-Ordner.</p>";
    } else {
        echo "<h1 style='color: red;'>❌ Email-Versand fehlgeschlagen</h1>";
        echo "<p>Überprüfen Sie die Logs für Details.</p>";
    }
    
} catch (Exception $e) {
    echo "<h1 style='color: red;'>❌ Fehler beim Email-Versand</h1>";
    echo "<p><strong>Fehler:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Mögliche Ursachen:</strong></p>";
    echo "<ul>";
    echo "<li>SMTP Credentials falsch</li>";
    echo "<li>Brevo Account nicht aktiviert</li>";
    echo "<li>Firewall blockiert Port 587</li>";
    echo "<li>Rate Limit überschritten</li>";
    echo "</ul>";
}

echo "<hr><p><strong>⚠️ SECURITY WARNING: DELETE THIS FILE IMMEDIATELY AFTER TESTING!</strong></p>";
?>
```

**Access**: https://auth.babixgo.de/test-smtp-send.php

**Expected Result**: 
- ✅ Success message displayed
- Email received at test recipient
- Email may be in spam folder initially (until domain verified)

**After testing**: DELETE this file immediately!

### Integration in Registration Flow

**File**: `/auth/public/includes/form-handlers/register-handler.php`

```php
<?php
/**
 * User Registration Handler
 * Sends verification email using SMTP configuration
 */

// Load dependencies
require_once SHARED_PATH . 'classes/Email.php';

// ... registration logic ...

// After user created successfully
try {
    // Generate verification token
    $verificationToken = bin2hex(random_bytes(32));
    
    // Store token in database
    $stmt = $db->prepare("
        UPDATE users 
        SET verification_token = ?, is_verified = 0 
        WHERE id = ?
    ");
    $stmt->execute([$verificationToken, $userId]);
    
    // Send verification email
    $emailSender = new Email($db);
    $emailSender->sendVerificationEmail(
        $userEmail,      // Recipient
        $username,       // Username for personalization
        $verificationToken
    );
    
    // Success message
    $_SESSION['success'] = 'Registrierung erfolgreich! Bitte überprüfen Sie Ihre Emails zur Bestätigung.';
    header('Location: /login.php?registered=1');
    exit;
    
} catch (Exception $e) {
    // Log error but don't expose details to user
    error_log('Registration email failed: ' . $e->getMessage());
    
    // User-friendly message
    $_SESSION['warning'] = 'Account wurde erstellt, aber die Bestätigungs-Email konnte nicht versendet werden. Bitte kontaktieren Sie den Support.';
    
    // Optional: Mark user for manual verification or retry
    $stmt = $db->prepare("UPDATE users SET is_verified = 0 WHERE id = ?");
    $stmt->execute([$userId]);
    
    header('Location: /login.php?email_failed=1');
    exit;
}
```

### Email Class Usage Examples

```php
<?php
// Initialize with database connection
$emailSender = new Email($db);

// Send verification email
$emailSender->sendVerificationEmail(
    'user@example.com',
    'Username',
    'verification-token-here'
);

// Send password reset email
$emailSender->sendPasswordResetEmail(
    'user@example.com',
    'Username',
    'reset-token-here'
);

// Send welcome email (after verification)
$emailSender->sendWelcomeEmail(
    'user@example.com',
    'Username'
);

// Send custom email
$emailSender->send(
    'recipient@example.com',
    'Subject Line',
    '<h1>HTML Content</h1>',
    'Plain text fallback'
);
```

### Security Checklist for SMTP Configuration

- [ ] `email.local.php` added to `.gitignore`
- [ ] `email.local.php` has correct file permissions (640)
- [ ] SMTP credentials use GitHub Secrets values
- [ ] No credentials in Git history
- [ ] Test files deleted after verification
- [ ] Error messages don't expose credentials
- [ ] Email logs don't contain passwords
- [ ] Rate limiting implemented
- [ ] SMTP debug mode set to 0 in production
- [ ] Domain verified in Brevo (for better deliverability)

### Troubleshooting SMTP Issues

#### Issue: "Configuration not found"
**Solution**: 
- Verify `/shared/config/email.local.php` exists
- Check file permissions (must be readable by web server)
- Verify path constants are correct

#### Issue: "Authentication failed"
**Solution**:
- Verify SMTP_USER is your Brevo login email
- Verify SMTP_KEY is the SMTP key from Brevo dashboard (NOT login password!)
- Check Brevo account is active

#### Issue: "Connection timeout"
**Solution**:
- Verify SMTP_PORT is 587 (TLS) or 465 (SSL)
- Check if Strato firewall blocks outgoing SMTP
- Try alternative port (2525 if Brevo supports it)

#### Issue: Emails go to spam
**Solution**:
- Verify domain in Brevo dashboard
- Add SPF record to DNS: `v=spf1 include:spf.brevo.com ~all`
- Enable DKIM in Brevo
- Warm up sender reputation (start with low volume)

### Deployment Checklist

When deploying email functionality:

1. [ ] Create `/shared/config/email.local.php` on server
2. [ ] Copy values from GitHub Secrets
3. [ ] Set file permissions to 640
4. [ ] Verify configuration with verify-smtp-config.php
5. [ ] Test sending with test-smtp-send.php
6. [ ] Delete test files immediately
7. [ ] Monitor first 10 registrations for email delivery
8. [ ] Check email_logs table for failures
9. [ ] Verify emails don't go to spam
10. [ ] Set up monitoring/alerts for email failures

---

## Summary for SMTP Configuration

**When implementing registration/email features:**

1. ✅ **NEVER hardcode SMTP credentials** in any file committed to Git
2. ✅ **USE environment variables** or `email.local.php` for credentials
3. ✅ **REFERENCE GitHub Secrets** in documentation for deployment
4. ✅ **PROVIDE manual setup instructions** for creating `email.local.php`
5. ✅ **INCLUDE verification scripts** (but mark as temporary/delete after use)
6. ✅ **IMPLEMENT error handling** that doesn't expose credentials
7. ✅ **ADD rate limiting** to prevent abuse
8. ✅ **LOG email failures** for monitoring
9. ✅ **USE Email class** from `/shared/classes/Email.php`
10. ✅ **TEST thoroughly** before marking feature complete

**GitHub Secrets to reference in setup docs:**
- `SMTP_HOST` - Brevo SMTP server
- `SMTP_PORT` - 587 for TLS
- `SMTP_USER` - Brevo account email
- `SMTP_KEY` - Brevo SMTP API key
- `SMTP_SENDER_REGISTRATION` - noreply@babixgo.de

---

## SMTP Configuration Status

**Last Updated**: January 15, 2026  
**Commit**: 384aeef  
**Status**: ✅ DOCUMENTED - Ready for Implementation

### GitHub Secrets Configuration (Verified)

The following secrets are configured in the repository and documented:

| Secret Name | Purpose | Status | Location in Code |
|-------------|---------|--------|------------------|
| `SMTP_HOST` | Brevo SMTP server | ✅ Documented | `email.local.php` or env |
| `SMTP_PORT` | SMTP port (587/465) | ✅ Documented | `email.local.php` or env |
| `SMTP_USER` | Brevo account email | ✅ Documented | `email.local.php` or env |
| `SMTP_KEY` | Brevo SMTP API key | ✅ Documented | `email.local.php` or env |
| `SMTP_SENDER_REGISTRATION` | Registration sender email | ✅ Documented | `email.local.php` or env |

### Implementation Checklist

When implementing email functionality (e.g., in auth.babixgo.de), follow this checklist:

#### Pre-Implementation (Planning)
- [ ] Read SMTP section in copilot-instructions.md completely
- [ ] Verify GitHub Secrets are still valid
- [ ] Confirm Brevo account is active
- [ ] Check Brevo free tier limits (300 emails/day)

#### Code Implementation
- [ ] Create `/shared/config/email.php` (config loader)
- [ ] Create `/shared/config/email.local.php.example` (template for deployment)
- [ ] Create `/shared/classes/Email.php` (email handler class)
- [ ] Add email-related methods to User class (if applicable)
- [ ] Create email templates in `/shared/email-templates/`:
  - [ ] verification.html
  - [ ] password-reset.html
  - [ ] welcome.html
- [ ] Create email_logs table in database
- [ ] Integrate email sending in registration flow
- [ ] Integrate email sending in password reset flow
- [ ] Add rate limiting checks

#### Security Implementation
- [ ] Add `/shared/config/email.local.php` to `.gitignore` (already done)
- [ ] Add `/shared/config/*.local.php` to `.gitignore` (already done)
- [ ] Implement credential validation in email.php
- [ ] Add error handling that doesn't expose credentials
- [ ] Set up email logging without password exposure
- [ ] Configure rate limiting (300 emails/day for Brevo free tier)
- [ ] Add CSRF protection to email-triggering forms
- [ ] Validate email addresses before sending

#### Deployment Steps
- [ ] Upload `/shared/config/email.php` to server
- [ ] Create `/shared/config/email.local.php` on server (manual step)
- [ ] Copy values from GitHub Secrets to email.local.php
- [ ] Set file permissions to 640 for email.local.php
- [ ] Upload Email class to `/shared/classes/`
- [ ] Upload email templates to `/shared/email-templates/`
- [ ] Run database migration for email_logs table
- [ ] Test with verify-smtp-config.php (then delete)
- [ ] Test with test-smtp-send.php (then delete)
- [ ] Monitor first 10 emails for delivery

#### Testing & Verification
- [ ] Unit test Email class methods
- [ ] Test registration email sending
- [ ] Test password reset email sending
- [ ] Test welcome email sending
- [ ] Verify emails don't go to spam folder
- [ ] Test rate limiting functionality
- [ ] Test error handling (invalid credentials, network failure)
- [ ] Verify email_logs table updates correctly
- [ ] Test on multiple email providers (Gmail, Outlook, etc.)
- [ ] Check email HTML rendering in different clients

#### Monitoring & Maintenance
- [ ] Set up email_logs monitoring dashboard
- [ ] Create alerts for email sending failures
- [ ] Monitor Brevo account usage (daily limit)
- [ ] Check spam folder placement rate
- [ ] Review email delivery success rate weekly
- [ ] Update email templates based on user feedback
- [ ] Rotate SMTP credentials periodically
- [ ] Document any email-related issues in issue tracker

### Current Implementation Status

**Files to Create:**

| File | Purpose | Status |
|------|---------|--------|
| `/shared/config/email.php` | Email configuration loader | ⏳ Not created |
| `/shared/config/email.local.php.example` | Template for deployment | ⏳ Not created |
| `/shared/classes/Email.php` | Email handler class | ⏳ Not created |
| `/shared/email-templates/verification.html` | Email verification template | ⏳ Not created |
| `/shared/email-templates/password-reset.html` | Password reset template | ⏳ Not created |
| `/shared/email-templates/welcome.html` | Welcome email template | ⏳ Not created |

**Database Changes:**

```sql
-- Email logs table (to be created)
CREATE TABLE IF NOT EXISTS email_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    email_type ENUM('verification', 'password_reset', 'welcome', 'custom') NOT NULL,
    status ENUM('sent', 'failed', 'pending') NOT NULL,
    error_message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_recipient (recipient_email),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Integration Points:**

| Feature | File | Integration Status |
|---------|------|-------------------|
| User Registration | `/auth/public/register.php` | ⏳ Not integrated |
| Email Verification | `/auth/public/verify-email.php` | ⏳ Not integrated |
| Password Reset Request | `/auth/public/forgot-password.php` | ⏳ Not integrated |
| Password Reset Confirm | `/auth/public/reset-password.php` | ⏳ Not integrated |

### Quick Reference

**When you need to implement email sending:**

1. **Check this status section** - Review current implementation status
2. **Follow the checklist** - Complete items in order (Pre-Implementation → Code → Security → Deployment)
3. **Use the code examples** - From the SMTP Configuration section above
4. **Test thoroughly** - Use the testing checklist before deployment
5. **Monitor actively** - Check email_logs and Brevo dashboard after deployment

**Common Implementation Pattern:**

```php
<?php
// In any file that needs to send email

// Load email configuration
require_once SHARED_PATH . 'config/email.php';
require_once SHARED_PATH . 'classes/Email.php';

// Initialize email handler
$emailHandler = new Email($db);

// Send email
try {
    $success = $emailHandler->sendVerificationEmail(
        $userEmail,
        $username,
        $verificationToken
    );
    
    if ($success) {
        // Email sent successfully
        $_SESSION['success'] = 'Verification email sent!';
    } else {
        // Email failed to send
        error_log('Email sending failed for user: ' . $userId);
        $_SESSION['error'] = 'Could not send email. Please contact support.';
    }
} catch (Exception $e) {
    // Handle exception
    error_log('Email exception: ' . $e->getMessage());
    $_SESSION['error'] = 'An error occurred. Please try again later.';
}
```

### Next Steps for Implementation

1. **Create Email class** (`/shared/classes/Email.php`) with methods:
   - `send($to, $subject, $htmlBody, $textBody)`
   - `sendVerificationEmail($email, $username, $token)`
   - `sendPasswordResetEmail($email, $username, $token)`
   - `sendWelcomeEmail($email, $username)`
   - `logEmail($userId, $recipient, $subject, $type, $status, $error)`

2. **Create email configuration** (`/shared/config/email.php`) that:
   - Loads from email.local.php if exists
   - Falls back to environment variables
   - Validates required credentials
   - Defines rate limiting constants

3. **Create email templates** in `/shared/email-templates/` with:
   - Professional HTML design
   - Plain text fallback
   - Proper variable substitution
   - Mobile-responsive layout

4. **Update .gitignore** (already done) to exclude:
   - `/shared/config/email.local.php`
   - Test files: `**/verify-smtp-config.php`, `**/test-smtp-send.php`

5. **Document in DEPLOYMENT_GUIDE.md** the manual steps for:
   - Creating email.local.php on server
   - Setting file permissions
   - Testing configuration
   - Monitoring email delivery

---

### Example: Database Setup

```markdown
# Manual Setup Steps for auth.babixgo.de Database

## Prerequisites
- Access to Strato phpMyAdmin or MySQL command line
- Database credentials from `/shared/config/database.php`

## Step 1: Access Database
**Location**: Strato Customer Portal → Database Administration
**Action**: 
1. Login to https://www.strato.de/apps/CustomerService
2. Navigate to "Datenbankverwaltung"
3. Click on your database name (shown in config file)
4. Click "phpMyAdmin öffnen"

**Expected Result**: phpMyAdmin interface opens with your database selected

## Step 2: Create Tables
**Location**: phpMyAdmin → SQL tab
**Action**: Copy and paste the following SQL and click "Execute"

**SQL Code**:
```sql
-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    description TEXT,
    friendship_link VARCHAR(8) UNIQUE,
    is_verified BOOLEAN DEFAULT 0,
    verification_token VARCHAR(64),
    reset_token VARCHAR(64),
    reset_token_expires DATETIME,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    domain VARCHAR(50) NOT NULL,
    content_id INT,
    comment TEXT NOT NULL,
    status ENUM('approved', 'pending', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_domain (domain),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Downloads Table
CREATE TABLE IF NOT EXISTS downloads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    filetype ENUM('apk', 'scripts', 'exe') NOT NULL,
    filesize BIGINT,
    version VARCHAR(50),
    description TEXT,
    download_count INT DEFAULT 0,
    active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_filetype (filetype),
    INDEX idx_active (active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Download Logs Table
CREATE TABLE IF NOT EXISTS download_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    downloaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (file_id) REFERENCES downloads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_file_id (file_id),
    INDEX idx_user_id (user_id),
    INDEX idx_downloaded_at (downloaded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Expected Result**: 
- Green checkmark in phpMyAdmin
- Message: "4 rows affected"
- Tables visible in left sidebar

**Troubleshooting**:
- Error "Table already exists": Tables already created, proceed to next step
- Error "Access denied": Check database credentials in config file
- Error "Unknown database": Database name in config is incorrect

## Step 3: Create First Admin User
**Location**: phpMyAdmin → SQL tab
**Action**: Execute the following SQL (replace password!)

**SQL Code**:
```sql
-- Generate password hash first (use PHP):
-- password_hash('YOUR_SECURE_PASSWORD', PASSWORD_DEFAULT)

INSERT INTO users (username, email, password_hash, role, is_verified, friendship_link) 
VALUES (
    'admin',
    'admin@babixgo.de',
    '$2y$10$REPLACE_THIS_WITH_ACTUAL_HASH',
    'admin',
    1,
    'ADM1N001'
);
```

**How to generate password hash**:
1. Create temporary file: `/auth/public/generate-hash.php`
2. Content:
```php
<?php
echo password_hash('YOUR_SECURE_PASSWORD_HERE', PASSWORD_DEFAULT);
?>
```
3. Visit: https://auth.babixgo.de/generate-hash.php
4. Copy the hash output
5. Use in SQL above
6. **DELETE generate-hash.php immediately after use!**

**Expected Result**: 
- 1 row inserted
- Admin user created

**Verification**:
1. Go to https://auth.babixgo.de/login.php
2. Login with: admin@babixgo.de / YOUR_SECURE_PASSWORD
3. Should redirect to profile/dashboard
4. Access https://auth.babixgo.de/admin/ should work (no 403 error)

## Step 4: Configure Email Settings
**Location**: `/shared/config/email.php` (create this file)
**Action**: Create file with following content

**Code**:
```php
<?php
// Email configuration for Strato
define('MAIL_FROM', 'noreply@babixgo.de');
define('MAIL_FROM_NAME', 'BabixGo Platform');
define('MAIL_REPLY_TO', 'support@babixgo.de');

// For Strato SMTP (optional, if PHP mail() doesn't work)
define('SMTP_HOST', 'smtp.strato.de');
define('SMTP_PORT', 465); // or 587 for TLS
define('SMTP_USER', 'your-email@babixgo.de');
define('SMTP_PASS', 'your-smtp-password');
define('SMTP_SECURE', 'ssl'); // or 'tls'
```

**Expected Result**: File created, no errors when loading auth pages

**Troubleshooting**:
- Test email sending via: https://auth.babixgo.de/test-email.php (create temporary test script)
- Check Strato email settings in customer portal
- Verify email address exists in Strato email administration

## Step 5: Set File Permissions
**Location**: FTP client (FileZilla, WinSCP)
**Action**: Set correct permissions for security

**Commands** (via FTP client or SSH if available):
1. Connect to FTP
2. Navigate to `/websites/`
3. Right-click on folders → File permissions

**Permissions**:
```
/shared/                     → 755 (rwxr-xr-x)
/shared/config/*.php         → 644 (rw-r--r--)
/downloads/                  → 750 (rwxr-x---)
/downloads/**/*              → 644 (rw-r--r--)
/auth/public/                → 755 (rwxr-xr-x)
/auth/public/*.php           → 644 (rw-r--r--)
/auth/public/admin/          → 755 (rwxr-xr-x)
```

**FileZilla Instructions**:
1. Select folder/file
2. Right-click → File permissions
3. Enter numeric value (e.g., 755)
4. Check "Recurse into subdirectories" for folders
5. Click OK

**Expected Result**: 
- No "Permission denied" errors
- Downloads folder not accessible via browser
- Auth pages load correctly

## Step 6: Configure Subdomains
**Location**: Strato Customer Portal → Domain Administration
**Action**: Point subdomains to correct directories

**Steps**:
1. Login to Strato Customer Portal
2. Navigate to "Domain-Verwaltung"
3. Select "babixgo.de"
4. Click "Subdomains verwalten"
5. Add/Edit subdomains:

| Subdomain | Target Directory |
|-----------|------------------|
| auth.babixgo.de | /auth/public |
| files.babixgo.de | /files/public |
| www.babixgo.de | /babixgo.de/public |
| (root) babixgo.de | /babixgo.de/public |

6. Click "Save" for each subdomain
7. Wait 5-15 minutes for DNS propagation

**Expected Result**:
- https://auth.babixgo.de loads login page
- https://files.babixgo.de loads download portal
- https://babixgo.de loads main site

**Troubleshooting**:
- "404 Not Found": Wrong directory path, check spelling
- "403 Forbidden": Check .htaccess or file permissions
- "DNS not found": DNS not propagated yet, wait longer
- Test with: `ping auth.babixgo.de` (should resolve to Strato IP)

## Step 7: SSL Certificate Configuration
**Location**: Strato Customer Portal → SSL/TLS
**Action**: Enable SSL for all subdomains

**Steps**:
1. Navigate to "SSL-Verwaltung"
2. Check if wildcard certificate exists for *.babixgo.de
3. If not, create new certificate:
   - Select "Wildcard certificate"
   - Covers: *.babixgo.de and babixgo.de
4. Enable "HTTPS Redirect" (forces all traffic to HTTPS)
5. Wait for certificate activation (up to 24 hours)

**Expected Result**:
- All domains accessible via https://
- http:// automatically redirects to https://
- No browser security warnings

**Verification**:
- Visit each domain with https://
- Check padlock icon in browser
- Verify certificate with: https://www.ssllabs.com/ssltest/

## Step 8: Test Cross-Domain Session
**Location**: Browser
**Action**: Verify session sharing works across domains

**Test Steps**:
1. Open https://auth.babixgo.de/login.php
2. Login with admin account
3. Open https://babixgo.de in same browser
4. Check if header shows "Logged in as admin"
5. Open https://files.babixgo.de in same browser
6. Check if header shows logged-in state
7. Click logout on any domain
8. Verify all three domains show logged-out state

**Expected Result**: 
- Login persists across all domains
- Logout affects all domains
- Session cookie domain is `.babixgo.de`

**Troubleshooting**:
- Session not shared: Check `/shared/config/session.php`
- Cookie domain must be `.babixgo.de` (note the dot!)
- Clear browser cookies and try again
- Check browser developer tools → Application → Cookies
- Verify cookie domain and secure flags

## Verification Checklist

After all steps completed, verify:

- [ ] All database tables created successfully
- [ ] Admin user can login at auth.babixgo.de
- [ ] Admin panel accessible at auth.babixgo.de/admin/
- [ ] Email verification emails are sent (test registration)
- [ ] File permissions set correctly
- [ ] All three domains accessible via HTTPS
- [ ] SSL certificate valid on all domains
- [ ] Cross-domain session sharing works
- [ ] Logout works across all domains
- [ ] Download system works (upload and download test file)
- [ ] User registration flow complete (register → verify email → login)
- [ ] Password reset works
- [ ] Comment system functional
- [ ] Admin can manage users
- [ ] Admin can manage downloads
- [ ] Admin can moderate comments
- [ ] No PHP errors in browser console
- [ ] No 404 or 403 errors on any page

## Rollback Instructions

If deployment fails:

### Rollback Database
```sql
-- Backup current database first!
DROP TABLE IF EXISTS download_logs;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS downloads;
DROP TABLE IF EXISTS users;
```

### Rollback Files
1. Delete `/auth/` folder via FTP
2. Restore previous version from backup
3. Clear browser cache and cookies

### Rollback Subdomains
1. Strato Portal → Domain Administration
2. Remove or redirect auth.babixgo.de subdomain
3. DNS changes take 5-15 minutes

## Common Issues & Solutions

### Issue: "Database connection failed"
**Solution**: 
- Check `/shared/config/database.php` credentials
- Verify database exists in Strato portal
- Test connection with phpMyAdmin

### Issue: "Session not working across domains"
**Solution**:
- Check cookie domain is `.babixgo.de` (with dot!)
- Verify HTTPS on all domains
- Clear browser cookies completely
- Check browser developer tools for cookie details

### Issue: "Email not sending"
**Solution**:
- Check Strato email configuration
- Verify sender email exists
- Test with mail() function first
- Check spam folder
- Review PHP error logs

### Issue: "File upload fails"
**Solution**:
- Check `/downloads/` permissions (750)
- Verify PHP upload_max_filesize in .htaccess
- Check disk space on server
- Review PHP error logs

### Issue: "Admin panel shows 403 Forbidden"
**Solution**:
- Check user role in database (should be 'admin')
- Verify session is active
- Check `/auth/admin/.htaccess` settings
- Clear session and login again

## Support Contacts

**Strato Support**: 
- Phone: [Strato Hotline]
- Portal: https://www.strato.de/apps/CustomerService

**Database Issues**: Access phpMyAdmin via Strato Portal

**DNS/Domain Issues**: Strato Domain Administration

**Emergency Rollback**: Restore from FTP backup

---

**IMPORTANT**: Delete any temporary files created during setup (generate-hash.php, test-email.php, etc.)

**SECURITY**: Never commit database credentials or sensitive configuration to Git!
```

### Additional Requirements for Implementation

When implementing features, you must ALSO document:

1. **Environment Variables** - If any are needed:
```markdown
## Environment Configuration

Create `/shared/config/local.php` (do not commit to Git):
```php
<?php
define('DB_HOST', 'your-database-host');
define('DB_NAME', 'your-database-name');
define('DB_USER', 'your-database-user');
define('DB_PASS', 'your-database-password');
```
```

2. **Third-Party Services** - If any external services are used:
```markdown
## External Service Configuration

### Google reCAPTCHA (for forms)
1. Go to https://www.google.com/recaptcha/admin
2. Register site: babixgo.de
3. Get Site Key and Secret Key
4. Add to `/shared/config/recaptcha.php`:
```php
<?php
define('RECAPTCHA_SITE_KEY', 'your-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');
```
```

3. **Cron Jobs** - If any scheduled tasks are needed:
```markdown
## Cron Job Setup

### Email Queue Processing
**Location**: Strato Portal → Cron Jobs
**Action**: Add new cron job

**Schedule**: Every 5 minutes
**Command**: `/usr/bin/php /path/to/websites/shared/cron/send-emails.php`
**Syntax**: `*/5 * * * *`
```

4. **.htaccess Changes** - If webserver configuration is needed:
```markdown
## .htaccess Configuration

**File**: `/auth/public/.htaccess`
**Action**: Add the following rules

```apache
# Your .htaccess content here
```

**Location in Strato**: File already processed by Apache automatically
**No manual action needed** - just upload via FTP
```

5. **Dependencies** - If any external libraries are required:
```markdown
## External Dependencies

### PHPMailer (if needed)
**Action**: Upload to `/shared/vendor/`
**Source**: https://github.com/PHPMailer/PHPMailer/releases
**Installation**:
1. Download latest release
2. Extract to `/shared/vendor/phpmailer/`
3. Include in code: `require SHARED_PATH . 'vendor/phpmailer/PHPMailerAutoload.php';`
```

### Documentation Template

Include this template at the end of your implementation:

```markdown
# Post-Implementation Checklist

## Files Created/Modified
- [ ] List all new files
- [ ] List all modified files
- [ ] List all deleted files (if any)

## Manual Steps Required
- [ ] Database changes (see Step X above)
- [ ] Configuration files (see Step Y above)
- [ ] File permissions (see Step Z above)
- [ ] Subdomain configuration (if applicable)
- [ ] Cron jobs (if applicable)

## Testing Steps
1. [Exact steps to test functionality]
2. [Expected results at each step]
3. [What to do if test fails]

## Deployment Order
1. Upload files in this order: [list]
2. Execute database changes
3. Configure settings
4. Test functionality
5. Monitor for errors

## Monitoring
- Check PHP error log: `/error_log` (Strato default location)
- Check browser console for JavaScript errors
- Monitor database for unusual activity
- Test cross-domain functionality

## Rollback Plan
If something breaks:
1. [Step-by-step rollback instructions]
2. [How to restore previous state]
3. [How to verify rollback successful]
```

---

## Summary for AI Agents

**When implementing any feature:**

1. ✅ **Always assume manual steps will be needed**
2. ✅ **Document every manual action explicitly**
3. ✅ **Provide exact commands, not descriptions**
4. ✅ **Include troubleshooting for common issues**
5. ✅ **Explain expected results at each step**
6. ✅ **Provide rollback instructions**
7. ✅ **List verification steps**
8. ✅ **Identify what needs Strato portal access**
9. ✅ **Flag security-sensitive steps clearly**
10. ✅ **Provide support contact information**

**Never assume:**
- ❌ User knows how to access phpMyAdmin
- ❌ User knows SQL syntax
- ❌ User knows how to set file permissions
- ❌ User knows Strato portal navigation
- ❌ User can figure out missing steps
- ❌ User will know what "configure the database" means

**Remember**: The person following your instructions may not be technical. Write for someone who needs step-by-step guidance with screenshots-level detail.

# Ergänzung: PWA Requirements & Monorepo Restructuring Task

Füge diese beiden Abschnitte zu den Copilot Instructions hinzu:

---

## Progressive Web App (PWA) Requirements (MANDATORY)

**ALL domains MUST be PWA-compliant** with installable, offline-capable functionality.

### Required PWA Files (Per Domain)

Each domain (`babixgo.de`, `files.babixgo.de`, `auth.babixgo.de`) must have:

#### 1. Manifest File (`manifest.json`)
**Location**: `/[domain]/public/manifest.json`

```json
{
  "name": "BabixGo [Domain Purpose]",
  "short_name": "BabixGo",
  "description": "[Domain-specific description]",
  "start_url": "/",
  "display": "standalone",
  "background_color": "#1a1a1a",
  "theme_color": "#6366f1",
  "orientation": "portrait-primary",
  "icons": [
    {
      "src": "/shared/assets/icons/icon-72x72.png",
      "sizes": "72x72",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-96x96.png",
      "sizes": "96x96",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-128x128.png",
      "sizes": "128x128",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-144x144.png",
      "sizes": "144x144",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-152x152.png",
      "sizes": "152x152",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-192x192.png",
      "sizes": "192x192",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-384x384.png",
      "sizes": "384x384",
      "type": "image/png",
      "purpose": "any maskable"
    },
    {
      "src": "/shared/assets/icons/icon-512x512.png",
      "sizes": "512x512",
      "type": "image/png",
      "purpose": "any maskable"
    }
  ],
  "screenshots": [
    {
      "src": "/shared/assets/screenshots/desktop-1.png",
      "sizes": "1280x720",
      "type": "image/png",
      "form_factor": "wide"
    },
    {
      "src": "/shared/assets/screenshots/mobile-1.png",
      "sizes": "750x1334",
      "type": "image/png",
      "form_factor": "narrow"
    }
  ],
  "categories": ["productivity", "utilities"],
  "shortcuts": [
    {
      "name": "Login",
      "short_name": "Login",
      "description": "Login to your account",
      "url": "/login",
      "icons": [{ "src": "/shared/assets/icons/login-icon.png", "sizes": "96x96" }]
    }
  ]
}
```

**Domain-Specific Adaptations**:
- `babixgo.de`: Main website, focus on content browsing
- `files.babixgo.de`: Download portal, focus on file management
- `auth.babixgo.de`: Authentication, focus on account management

#### 2. Service Worker (`sw.js`)
**Location**: `/[domain]/public/sw.js`

```javascript
const CACHE_NAME = 'babixgo-[domain]-v1';
const urlsToCache = [
  '/',
  '/shared/assets/css/main.css',
  '/assets/css/[domain-specific].css',
  '/shared/assets/js/main.js',
  '/assets/js/[domain-specific].js',
  '/shared/assets/icons/icon-192x192.png',
  '/shared/assets/icons/icon-512x512.png',
  '/offline.html'
];

// Install event - cache essential assets
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch event - network first, fallback to cache
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Clone response for caching
        const responseClone = response.clone();
        caches.open(CACHE_NAME).then(cache => {
          cache.put(event.request, responseClone);
        });
        return response;
      })
      .catch(() => {
        return caches.match(event.request)
          .then(response => {
            return response || caches.match('/offline.html');
          });
      })
  );
});
```

#### 3. Offline Fallback Page
**Location**: `/[domain]/public/offline.html`

```html
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - BabixGo</title>
    <link rel="stylesheet" href="/shared/assets/css/main.css">
</head>
<body>
    <div class="offline-container">
        <h1>Sie sind offline</h1>
        <p>Diese Seite benötigt eine Internetverbindung.</p>
        <button onclick="location.reload()">Erneut versuchen</button>
    </div>
</body>
</html>
```

### Required Meta Tags (In Every Page `<head>`)

```html
<!-- PWA Meta Tags -->
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="BabixGo">

<!-- Manifest Link -->
<link rel="manifest" href="/manifest.json">

<!-- Theme Color -->
<meta name="theme-color" content="#6366f1">

<!-- Apple Touch Icons -->
<link rel="apple-touch-icon" sizes="180x180" href="/shared/assets/icons/apple-touch-icon.png">

<!-- Favicon -->
<link rel="icon" type="image/png" sizes="32x32" href="/shared/assets/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/shared/assets/icons/favicon-16x16.png">
```

### Service Worker Registration (In Every Page)

**Add to `/shared/assets/js/main.js`:**

```javascript
// Service Worker Registration
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/sw.js')
      .then(registration => {
        console.log('ServiceWorker registered:', registration.scope);
      })
      .catch(error => {
        console.log('ServiceWorker registration failed:', error);
      });
  });
}
```

### PWA Icon Requirements

**Location**: `/shared/assets/icons/`

Required icon sizes (PNG format):
- favicon-16x16.png
- favicon-32x32.png
- icon-72x72.png
- icon-96x96.png
- icon-128x128.png
- icon-144x144.png
- icon-152x152.png
- icon-192x192.png (required for Android)
- icon-384x384.png
- icon-512x512.png (required for Android)
- apple-touch-icon.png (180x180)

**Design Requirements**:
- Transparent background OR solid brand color
- Maskable safe zone (80% of canvas)
- Recognizable at all sizes
- High contrast for visibility

### PWA Testing Checklist

Before deployment, verify:

- [ ] Manifest.json validates at https://manifest-validator.appspot.com/
- [ ] Service Worker registers without errors
- [ ] Offline page loads when network disconnected
- [ ] "Add to Home Screen" prompt appears on mobile
- [ ] Icons display correctly in installed app
- [ ] Theme color applies to browser UI
- [ ] Lighthouse PWA score > 90
- [ ] Works on iOS Safari (apple-mobile-web-app meta tags)
- [ ] Works on Android Chrome (manifest + service worker)
- [ ] App installs and launches in standalone mode
- [ ] All cached resources load offline

### Lighthouse PWA Audit Requirements

**Must pass all Lighthouse PWA checks:**
- ✅ Registers a service worker
- ✅ Responds with 200 when offline
- ✅ Has a web app manifest
- ✅ Configured for a custom splash screen
- ✅ Sets a theme color
- ✅ Content sized correctly for viewport
- ✅ Displays correctly on mobile
- ✅ Page load is fast on mobile networks
- ✅ Site works cross-browser
- ✅ Page transitions don't feel like blocking the network
- ✅ Each page has a URL

### Manual Setup Steps for PWA

**Step 1: Generate PWA Icons**
**Action**: Use online tool or script to generate all icon sizes

**Option A - Online Tool**:
1. Go to https://realfavicongenerator.net/
2. Upload your logo (minimum 512x512px)
3. Configure maskable safe zone
4. Generate and download icons
5. Upload to `/shared/assets/icons/`

**Option B - ImageMagick Script** (if available on server):
```bash
# Convert single source image to all sizes
convert logo.png -resize 16x16 favicon-16x16.png
convert logo.png -resize 32x32 favicon-32x32.png
convert logo.png -resize 72x72 icon-72x72.png
convert logo.png -resize 96x96 icon-96x96.png
convert logo.png -resize 128x128 icon-128x128.png
convert logo.png -resize 144x144 icon-144x144.png
convert logo.png -resize 152x152 icon-152x152.png
convert logo.png -resize 192x192 icon-192x192.png
convert logo.png -resize 384x384 icon-384x384.png
convert logo.png -resize 512x512 icon-512x512.png
convert logo.png -resize 180x180 apple-touch-icon.png
```

**Step 2: Test PWA Installation**

**On Android Chrome**:
1. Open https://[domain].babixgo.de
2. Tap menu (3 dots) → "Add to Home screen"
3. App icon should appear on home screen
4. Launch app → opens in standalone mode (no browser UI)

**On iOS Safari**:
1. Open https://[domain].babixgo.de
2. Tap Share button → "Add to Home Screen"
3. App icon should appear on home screen
4. Launch app → opens with splash screen

**On Desktop Chrome**:
1. Open https://[domain].babixgo.de
2. Look for install icon in address bar
3. Click install button
4. App opens in separate window

**Expected Result**: 
- App installs successfully on all platforms
- Offline functionality works
- App updates automatically when service worker updates

### PWA Debugging

**Chrome DevTools**:
1. Open DevTools → Application tab
2. Check "Manifest" section for errors
3. Check "Service Workers" section for registration status
4. Test "Offline" mode in Network tab
5. Use Lighthouse audit for PWA score

**Common Issues**:
- "Manifest not found": Check path and MIME type
- "Service Worker not registering": Check HTTPS requirement
- "Icons not displaying": Verify sizes and paths in manifest
- "Offline not working": Check service worker cache strategy
