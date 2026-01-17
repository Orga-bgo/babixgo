<artifact identifier="copilot-instructions-single-domain" type="application/vnd.ant.code" language="markdown" title=".github/copilot-instructions.md (Single-Domain Architecture)">
# GitHub Copilot Instructions for babixGO Platform

This repository contains the complete babixGO platform - a single-domain web application with authentication, user profiles, download portal, and admin panel.

## Critical Documents (READ BEFORE ANY CHANGES)

Before making any changes, **read these files** (violations = PR rejection):

1. **agents_instructions.md** - Mandatory rules, structure, and workflows for the entire platform
2. **DESIGN_SYSTEM.md** - Brand guide, design tokens, component styles, and governance
3. **README.md** - Project overview and orientation
4. **MIGRATION_COMPLETED.md** - Recent cleanup and restructuring documentation

## Project Overview

- **Architecture**: Single-domain with path-based routing (babixgo.de/*)
- **Technology**: Pure HTML, CSS, JavaScript with server-side PHP
- **No Build Tools**: No npm, webpack, bundlers, or compilation steps
- **Deployment**: Direct FTP/SFTP upload to Strato webhosting
- **Database**: MySQL/MariaDB (users, downloads, comments, email logs)

## CRITICAL: Single-Domain Architecture

**ALL functionality runs under `babixgo.de` with path-based routing:**

```
babixgo.de/                # Main site & homepage
babixgo.de/auth/*          # Authentication (login, register, logout)
babixgo.de/user/*          # User profiles & dashboard (login required)
babixgo.de/files/*         # Download portal
babixgo.de/admin/*         # Admin panel (admin role required)
```

**❌ NO subdomains** - The old multi-domain structure (auth.babixgo.de, files.babixgo.de) is **obsolete**.

## Development: Zero Dependencies

**Start Server:**
```bash
# From project root
cd babixgo.de
php -S localhost:8000
```

**Validate PHP:**
```bash
# Single file
php -l filename.php

# All PHP files
find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"
```

**Test Checklist:**
```bash
□ Pages load without errors (check all sections: auth, user, files, admin)
□ Browser console (F12) - NO errors
□ Mobile view works (responsive design)
□ Partials loaded correctly (view source)
□ NO inline styles/scripts added (CSP compliance)
□ Images have alt + width/height
□ Exactly ONE H1 per page
□ Meta complete (title, description, canonical)
□ Session works across sections
□ Auth/admin checks work correctly
□ Downloads serve properly
```

**Deploy:** Direct FTP/SFTP upload to Strato (manual or via workflow)

## Environment Variables & Secrets

**GitHub Secrets** (for SMTP email functionality):
```
SMTP_HOST                    # smtp-relay.brevo.com
SMTP_PORT                    # 587
SMTP_USER                    # Brevo account email
SMTP_KEY                     # Brevo SMTP API key
SMTP_SENDER_REGISTRATION     # noreply@babixgo.de
```

**Production Config** (manual setup on server):
```
/shared/config/email.local.php       # SMTP credentials (NOT in Git)
/shared/config/database.local.php    # DB credentials (NOT in Git)
```

**Database Environment Variables** (if using):
```
BABIXGO_DB_HOST, BABIXGO_DB_NAME, BABIXGO_DB_USER, BABIXGO_DB_PASS
```

## Repository Structure (Key Paths)

```
/
├── shared/                          # Shared across ALL sections
│   ├── assets/
│   │   ├── css/                     # (REMOVED - consolidated into /babixgo.de/assets/css/)
│   │   ├── js/main.js              # Global scripts
│   │   └── icons/                  # PWA icons, shared icons
│   ├── classes/                     # PHP classes (Database, User, Email, etc.)
│   ├── config/                      # Config files (database, session, email)
│   ├── partials/                    # Reusable PHP partials (v1.0.15+)
│   │   ├── head-meta.php, head-links.php, header.php, footer.php
│   │   ├── tracking.php, cookie-banner.php
│   │   └── version.php             # BABIXGO_VERSION = '1.0.15'
│   └── email-templates/             # HTML email templates
│
├── downloads/                       # Protected file storage (NOT web-accessible)
│   ├── .htaccess                   # Deny from all
│   ├── apk/, exe/, scripts/
│
└── babixgo.de/                      # SINGLE DOMAIN - Document Root
    ├── .htaccess                   # Main routing & security
    ├── index.php                   # Homepage
    ├── manifest.json, sw.js, offline.html  # PWA files
    │
    ├── assets/                     # Domain-specific assets
    │   ├── css/                    
    │   │   └── style.css          # SINGLE consolidated CSS file (all styles)
    │   ├── js/                     # Section-specific scripts
    │   └── icons/, images/
    │
    ├── auth/                       # Authentication
    │   ├── login.php, register.php, logout.php
    │   ├── verify-email.php, forgot-password.php, reset-password.php
    │   └── includes/form-handlers/
    │
    ├── user/                       # User section (login required)
    │   ├── index.php (dashboard), profile.php, edit-profile.php
    │   ├── my-comments.php, my-downloads.php, settings.php
    │   └── includes/auth-check.php
    │
    ├── files/                      # Download portal
    │   ├── index.php, browse.php, download.php, category.php
    │   └── includes/download-handler.php
    │
    ├── admin/                      # Admin panel (admin role required)
    │   ├── .htaccess              # Additional protection
    │   ├── index.php (dashboard)
    │   ├── users.php, downloads.php, comments.php
    │   └── includes/admin-check.php
    │
    ├── api/                        # Optional API endpoints
    │
    └── [existing pages]            # Content pages
        ├── accounts/, anleitungen/, datenschutz/, impressum/
        ├── kontakt/, partnerevents/, sticker/
        └── tycoon-racers/, wuerfel/
```

**Production Files:** `.php` files in `/babixgo.de/` and subdirectories

**Non-Production (DO NOT MODIFY):**
- `/weg/` - Archived files (read-only)
- `/add/` - Future potential files (no production references)
- `/examples/` - Reference only
- `/to-do/` - Planning only

## PHP Partials: MANDATORY Usage

### Standard Path Pattern

**ALWAYS use this pattern:**
```php
<?php
// At top of EVERY page
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'partials/FILE.php';
?>
```

**❌ NEVER use:**
- Relative paths: `require '../partials/header.php';`
- `__DIR__`: `require __DIR__ . '/../partials/header.php';`
- Short tags: `<? include 'file.php'; ?>`
- Old multi-domain URLs: `https://auth.babixgo.de/login`

### Required Inclusion Order (EVERY Page)

**In `<head>` section:**
1. `head-meta.php` - Common meta tags
2. Page-specific meta: `<title>`, `<meta name="description">`, `<link rel="canonical">`
3. `head-links.php` - CSS, fonts, favicons, PWA manifest

**After `<body>` tag:**
4. `tracking.php` - ALL tracking code (Google/GA/FB)
5. `cookie-banner.php` - Consent management
6. `header.php` - Navigation with user menu

**Before `</body>` tag:**
7. `footer.php` - Site footer
8. `footer-scripts.php` - Global scripts

### Partial-Specific Rules

- **head-meta.php**: Common meta ONLY - NO page-specific content
- **head-links.php**: CSS, fonts, PWA ONLY - NO meta tags, NO scripts
- **tracking.php**: ALL tracking code - NO tracking elsewhere
- **cookie-banner.php**: Consent control
- **header.php**: Navigation with section detection and user menu - NO meta, NO scripts
- **footer.php**: Layout and links ONLY
- **footer-scripts.php**: Global scripts ONLY - NO tracking, NO inline scripts

**Current Version:** v1.0.15 (defined in `shared/partials/version.php`)

## HTML Standards (MANDATORY)

### Heading Hierarchy

| Element | Location | Icon | Wrapper Class |
|---------|----------|------|---------------|
| **H1** | Hero section (first section-card) | No | `.welcome-title` |
| **H2** | Section titles (outside content box) | Yes (always) | `.section-header` |
| **H3** | Inside box/card | No (gradient underline) | In `.content-card` |

**H1 Rules:**
- Exactly ONE per page
- Exception: 404.php uses `.error-message` instead of `.welcome-title`

**H2 Structure Pattern:**
```html
<div class="section-header">
  <h2><img src="/shared/assets/icons/icon.svg" class="icon" alt="Description">Title</h2>
</div>
```

**H3 Rules:**
- Inside cards/content boxes
- Auto gradient underline via CSS `::after`

### Required Meta Tags (Every Page)

```html
<head>
    <?php require SHARED_PATH . 'partials/head-meta.php'; ?>
    
    <!-- Page-specific (REQUIRED) -->
    <title>Unique Page Title - babixGO</title>
    <meta name="description" content="150-160 character description">
    <link rel="canonical" href="https://babixgo.de/section/page">
    
    <?php require SHARED_PATH . 'partials/head-links.php'; ?>
</head>
```

### Image Requirements

```html
<!-- ✅ CORRECT: Alt + dimensions -->
<img src="/assets/images/photo.jpg" 
     alt="Descriptive alt text" 
     width="800" 
     height="600">

<!-- ✅ CORRECT: Lazy loading for below-fold -->
<img src="/assets/images/photo.jpg" 
     alt="Description" 
     loading="lazy" 
     width="800" 
     height="600">

<!-- ❌ WRONG: Missing alt or dimensions -->
<img src="/assets/images/photo.jpg">
```

## CSS & Design System

### Single Source of Truth

**Global CSS:** `/shared/assets/css/main.css` - Base styles for all sections

**Section-Specific CSS:**
- `/babixgo.de/assets/css/style.css` - Main site (3,852 lines)
- `/babixgo.de/assets/css/files.css` - Files section
- `/babixgo.de/assets/css/user.css` - User section
- `/babixgo.de/assets/css/admin.css` - Admin section

**❌ NEVER:**
- Inline styles (except technically mandatory)
- Additional global CSS files
- Hardcoded colors/spacing (use tokens)

**✅ ALWAYS:**
- Use design tokens: `var(--md-primary)`, `var(--spacing-md)`
- Add new global styles to `/shared/assets/css/main.css`
- Add section-specific styles to appropriate section CSS

### Design Tokens (Material Design 3 Dark)

```css
/* Typography */
--font-size-h1: 2rem;
--font-size-h2: 1.5rem;
--font-size-h3: 1.2rem;
--font-size-body: 1rem;
--font-size-small: 0.9rem;

/* Colors */
--md-primary: #6366f1;
--md-secondary: #8b5cf6;
--md-surface: #1e1e1e;
--md-surface-container-low: #1a1a1a;
--md-surface-container: #242424;
--md-surface-container-high: #2e2e2e;
--text: #ffffff;
--text-secondary: #e0e0e0;
--muted: #a0a0a0;

/* Spacing */
--spacing-xs: 0.5rem;
--spacing-sm: 1rem;
--spacing-md: 1.5rem;
--spacing-lg: 2rem;
--spacing-xl: 3rem;

/* Borders & Radius */
--radius-sm: 4px;
--radius-md: 8px;
--radius-lg: 12px;
```

**Fonts:**
- Body: Inter (400, 500, 600)
- Headings: Montserrat (700)

### CSS Loading Order

```html
<head>
    <!-- 1. Shared global styles FIRST -->
    <link rel="stylesheet" href="/shared/assets/css/main.css">
    
    <!-- 2. Domain-specific styles SECOND -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- 3. Section-specific styles THIRD (if on that section) -->
    <link rel="stylesheet" href="/assets/css/files.css">  <!-- files/* pages -->
    <link rel="stylesheet" href="/assets/css/user.css">   <!-- user/* pages -->
    <link rel="stylesheet" href="/assets/css/admin.css">  <!-- admin/* pages -->
</head>
```

## JavaScript Standards

### Global Scripts

**Location:** `/shared/assets/js/main.js` (835 lines)

**Includes:**
- Service worker registration
- Mobile menu toggle
- Cookie consent handling
- Form validation utilities

**❌ NO inline scripts** (CSP violation)
**❌ NO additional global JS files**
**✅ Add to existing main.js or section-specific JS**

### Section-Specific Scripts

```html
<body>
    <!-- Content -->
    
    <!-- 1. Shared global scripts FIRST -->
    <script src="/shared/assets/js/main.js"></script>
    
    <!-- 2. Section-specific scripts SECOND -->
    <script src="/assets/js/files.js"></script>   <!-- files/* pages -->
    <script src="/assets/js/user.js"></script>    <!-- user/* pages -->
    <script src="/assets/js/admin.js"></script>   <!-- admin/* pages -->
</body>
```

## Authentication & Authorization

### Session Configuration

**Single-domain session cookie:**
```php
<?php
// In /shared/config/session.php
ini_set('session.cookie_domain', 'babixgo.de');  // NO dot prefix!
ini_set('session.cookie_path', '/');
ini_set('session.cookie_secure', '1');           // HTTPS only
ini_set('session.cookie_httponly', '1');
session_name('BABIXGO_SESSION');
session_start();
?>
```

**Session Variables:**
```php
$_SESSION['user_id']       // int - User ID
$_SESSION['username']      // string - Username
$_SESSION['email']         // string - Email
$_SESSION['role']          // 'user' or 'admin'
$_SESSION['csrf_token']    // string - CSRF protection
```

### Protected Sections

**User Section** (`/user/*` - requires login):
```php
<?php
// At top of ALL /user/*.php pages
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/auth-check.php';  // Redirects to /auth/login if not logged in

// User is authenticated - proceed with page
?>
```

**Admin Section** (`/admin/*` - requires admin role):
```php
<?php
// At top of ALL /admin/*.php pages
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/admin-check.php';  // Shows 403 if not admin

// Admin is authorized - proceed with page
?>
```

### Login with Redirect

```php
<?php
// Redirect to login with return URL
if (!isset($_SESSION['user_id'])) {
    $returnUrl = urlencode($_SERVER['REQUEST_URI']);
    header("Location: /auth/login?redirect={$returnUrl}");
    exit;
}

// After successful login (in login handler)
$redirect = $_GET['redirect'] ?? '/user/';
header("Location: {$redirect}");
exit;
?>
```

## Security Requirements (CRITICAL)

### CSRF Protection (ALL Forms)

```php
<?php
// Generate token (at top of page with form)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<form method="POST" action="/section/handler.php">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <!-- Form fields -->
    <button type="submit">Submit</button>
</form>

<?php
// Validate token (in form handler)
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF validation failed');
}
?>
```

### SQL Injection Prevention

```php
<?php
// ✅ CORRECT: Prepared statements ALWAYS
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);

$stmt = $db->prepare("INSERT INTO comments (user_id, comment) VALUES (?, ?)");
$stmt->execute([$userId, $comment]);

// ❌ WRONG: String concatenation
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];  // NEVER DO THIS
?>
```

### XSS Prevention

```php
<?php
// ✅ CORRECT: Escape ALL output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
echo htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

// ❌ WRONG: Raw output
echo $_POST['comment'];  // NEVER DO THIS
?>
```

### File Upload Validation

```php
<?php
// Validate file uploads (in admin/download-upload.php)
$allowedTypes = [
    'apk' => ['application/vnd.android.package-archive'],
    'exe' => ['application/x-msdownload'],
    'scripts' => ['text/plain', 'text/x-python', 'application/x-sh']
];

$fileType = $_POST['file_type'];
$uploadedFile = $_FILES['download_file'];

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes[$fileType])) {
    die('Invalid file type');
}

// Validate size (500MB max)
if ($uploadedFile['size'] > 500 * 1024 * 1024) {
    die('File too large');
}

// Generate safe filename
$safeFilename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $uploadedFile['name']);
$targetPath = DOWNLOADS_PATH . $fileType . '/' . $safeFilename;

// Move file
if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
    die('Upload failed');
}

// Set permissions
chmod($targetPath, 0644);
?>
```

### Protected Downloads

```php
<?php
// /downloads/ directory MUST be blocked via .htaccess
// Order Deny,Allow
// Deny from all

// Files served ONLY via PHP handler
// Location: /babixgo.de/files/download.php

define('DOWNLOADS_PATH', BASE_PATH . 'downloads/');

$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fileType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

// Fetch from database
$stmt = $db->prepare("SELECT filename, filepath FROM downloads WHERE id = ? AND filetype = ? AND active = 1");
$stmt->execute([$fileId, $fileType]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    die('File not found');
}

$filePath = DOWNLOADS_PATH . $file['filepath'];

// Security: Verify path is within downloads directory
$realPath = realpath($filePath);
if (!$realPath || strpos($realPath, DOWNLOADS_PATH) !== 0) {
    http_response_code(403);
    die('Access denied');
}

// Serve file
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file['filename']) . '"');
header('Content-Length: ' . filesize($realPath));
readfile($realPath);
exit;
?>
```

## Routing & .htaccess

### Main Routing (babixgo.de/.htaccess)

```apache
# HTTPS redirect
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Download routing
# /files/download/123/apk → files/download.php?id=123&type=apk
RewriteRule ^files/download/([0-9]+)/(apk|exe|scripts)$ files/download.php?id=$1&type=$2 [L,QSA]

# Category routing
RewriteRule ^files/category/(apk|exe|scripts)$ files/category.php?type=$1 [L,QSA]

# User profile routing
RewriteRule ^user/profile/([a-zA-Z0-9_-]+)$ user/profile.php?username=$1 [L,QSA]

# Security: Block /downloads/ directory
RewriteCond %{REQUEST_URI} ^/downloads/ [NC]
RewriteRule .* - [F,L]

# Security: Block config files
<FilesMatch "(database\.php|email\.local\.php|\.env)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
```

## Email System (SMTP via Brevo)

### Configuration Files

**Config Loader** (committed to Git):
- `/shared/config/email.php`

**Credentials** (NOT in Git - manual setup):
- `/shared/config/email.local.php`

### email.local.php Template

```php
<?php
// Manual setup on production server
// Use values from GitHub Secrets

define('SMTP_HOST', 'smtp-relay.brevo.com');           // SMTP_HOST
define('SMTP_PORT', 587);                               // SMTP_PORT
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'your-brevo-email@example.com'); // SMTP_USER
define('SMTP_PASSWORD', 'your-smtp-api-key');           // SMTP_KEY
define('MAIL_FROM', 'noreply@babixgo.de');              // SMTP_SENDER_REGISTRATION
define('MAIL_FROM_NAME', 'BabixGo Platform');
define('MAIL_CHARSET', 'UTF-8');
define('MAIL_SMTP_DEBUG', 0);  // 0=off, 2=verbose
define('MAIL_RATE_LIMIT', 300);  // Brevo free: 300/day
define('MAIL_RATE_PERIOD', 86400);
?>
```

### Email Usage

```php
<?php
require_once SHARED_PATH . 'classes/Email.php';

$emailSender = new Email($db);

// Send verification email
$emailSender->sendVerificationEmail($email, $username, $token);

// Send password reset email
$emailSender->sendPasswordResetEmail($email, $username, $token);

// Send welcome email
$emailSender->sendWelcomeEmail($email, $username);

// Send custom email
$emailSender->send($to, $subject, $htmlBody, $textBody);
?>
```

## PWA Requirements (MANDATORY)

### Required Files (babixgo.de/)

1. **manifest.json** - App manifest with scope: `/`
2. **sw.js** - Service worker (caches all sections)
3. **offline.html** - Offline fallback page

### Manifest Configuration

```json
{
  "name": "babixGO - Download Platform",
  "short_name": "babixGO",
  "start_url": "/",
  "scope": "/",
  "display": "standalone",
  "theme_color": "#6366f1",
  "icons": [
    { "src": "/shared/assets/icons/icon-192x192.png", "sizes": "192x192" },
    { "src": "/shared/assets/icons/icon-512x512.png", "sizes": "512x512" }
  ]
}
```

### Required Meta Tags

```html
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="theme-color" content="#6366f1">
<link rel="manifest" href="/manifest.json">
```

## Common Mistakes to Avoid

### ❌ Don't Do This

```php
// Wrong: Full URLs to sections
header('Location: https://babixgo.de/auth/login');
<a href="https://auth.babixgo.de/login">

// Wrong: Relative paths for shared resources
require '../shared/partials/header.php';
require __DIR__ . '/../shared/config/database.php';

// Wrong: Duplicate partials
require __DIR__ . '/partials/header.php';

// Wrong: Inline styles/scripts
<div style="color: red;">
<script>alert('test');</script>

// Wrong: Hardcoded values
$color = '#6366f1';
padding: 20px;

// Wrong: SQL concatenation
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];

// Wrong: Unescaped output
echo $_POST['comment'];

// Wrong: Missing auth check
// In /user/*.php without require auth-check.php

// Wrong: Short PHP tags
<? echo $var; ?>
```

### ✅ Do This Instead

```php
// Correct: Relative paths
header('Location: /auth/login');
<a href="/auth/login">

// Correct: Standard path pattern
define('SHARED_PATH', dirname(__DIR__, 2) . '/shared/');
require SHARED_PATH . 'partials/header.php';

// Correct: Use shared partials
require SHARED_PATH . 'partials/header.php';

// Correct: CSS classes
<div class="error-message">
<!-- NO inline scripts -->

// Correct: Design tokens
color: var(--md-primary);
padding: var(--spacing-md);

// Correct: Prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_GET['id']]);

// Correct: Escape output
echo htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');

// Correct: Auth check included
require_once __DIR__ . '/includes/auth-check.php';

// Correct: Full PHP tags
<?php echo $var; ?>
```

## Validation Checklist (Before Commit)

```bash
# 1. PHP syntax validation (MUST pass)
find . -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors"

# 2. Start local server
cd babixgo.de
php -S localhost:8000

# 3. Test in browser
□ All sections load: /, /auth/, /user/, /files/, /admin/
□ Browser console (F12) - NO errors
□ Mobile view responsive
□ Session works across sections
□ Login/logout works
□ Auth checks redirect correctly
□ Admin checks show 403 for non-admins
□ Downloads work
□ Forms have CSRF tokens
□ Partials load correctly (view source)
□ NO inline styles/scripts
□ Images have alt + width/height
□ Exactly ONE H1 per page
□ Meta complete (title, description, canonical)

# 4. Security checks
□ SQL queries use prepared statements
□ User input is escaped on output
□ CSRF protection on all forms
□ Auth/admin checks on protected pages
□ /downloads/ not accessible via URL
□ Config files not accessible via URL
```

## Quick Reference

### Key Configuration Files

```
.htaccess                           # Routing, security, redirects
shared/config/session.php           # Session configuration
shared/config/email.php             # Email config loader
shared/config/email.local.php       # SMTP credentials (NOT in Git)
shared/partials/version.php         # Version: 1.0.15
babixgo.de/manifest.json            # PWA manifest
babixgo.de/sw.js                    # Service worker
```

### Key Functional Files

```
shared/classes/Database.php         # Database connection
shared/classes/User.php             # User management
shared/classes/Email.php            # Email sending
shared/classes/Download.php         # Download management
babixgo.de/user/includes/auth-check.php    # User authentication
babixgo.de/admin/includes/admin-check.php  # Admin authorization
babixgo.de/files/download.php      # Download handler
```

### Common Errors & Solutions

**PHP Syntax Error:**
```bash
php -l filename.php  # Shows line number of error
```

**404 Not Found:**
- Check `.htaccess` rewrite rules
- Verify file exists at expected path
- Check document root is `/babixgo.de/`

**Session Not Working:**
- Verify `session.cookie_domain` is `babixgo.de` (no dot)
- Check `session_start()` called before output
- Ensure HTTPS is enforced

**Download Fails:**
- Verify file exists in `/downloads/{type}/`
- Check database `filepath` matches actual location
- Verify `/downloads/.htaccess` blocks direct access
- Check file permissions (644)

**Email Not Sending:**
- Verify `/shared/config/email.local.php` exists
- Check SMTP credentials from Brevo dashboard
- Test with temporary verification script (then delete)
- Check Brevo account is active

**403 Forbidden (Admin):**
- Check `$_SESSION['role']` is `'admin'`
- Verify admin-check.php is included
- Check database: `SELECT role FROM users WHERE id = X`

## Documentation Updates

**When to update which file:**
- **Structure/rules** → `agents_instructions.md`
- **Design/visual** → `DESIGN_SYSTEM.md`
- **New styles** → Add to appropriate CSS file
- **Version bump** → Update `shared/partials/version.php`
- **Architecture changes** → Update this file + `agents_instructions.md`

## Git Workflow

### .gitignore (Critical)

```gitignore
# NEVER commit these
/shared/config/email.local.php
/shared/config/database.local.php
/shared/config/*.local.php
.env
.env.local

# Test files (temporary)
**/test-email.php
**/verify-smtp-config.php
**/test-smtp-send.php

# Uploaded files (optional)
/downloads/apk/*
/downloads/exe/*
/downloads/scripts/*

# System files
.DS_Store
Thumbs.db
*.log
```

### Commit Message Format

```
[Section] Action: Description

Examples:
[Auth] Add: Email verification flow
[User] Fix: Profile edit validation
[Admin] Update: User management pagination
[Files] Refactor: Download handler
[Shared] Add: Email rate limiting
[PWA] Update: Service worker cache
[Security] Fix: CSRF validation
[Docs] Update: copilot-instructions.md
```

## Deployment to Strato

### FTP Structure

```
/var/www/websites/
├── shared/          # Upload entire folder
├── downloads/       # Upload with .htaccess
└── babixgo.de/      # Upload entire folder
```

### Manual Steps Required

1. **Upload files via FTP/SFTP**
2. **Set Document Root** in Strato portal: `/babixgo.de/`
3. **Create `/shared/config/email.local.php`** with GitHub Secret values
4. **Set file permissions**:
   - `email.local.php` → 640
   - `/downloads/` → 750
   - All `.php` files → 644
5. **Create database tables** via phpMyAdmin
6. **Test all sections** after deployment

## Trust These Instructions

Created by analyzing complete platform architecture, testing all sections, validating security, and consolidating from multiple documentation sources.

**Validated:** 2026-01-15 | Single-domain architecture migration complete

For detailed architecture and workflows, **always consult `agents_instructions.md` first.**

---

**Current Version:** 1.0.15  
**Architecture:** Single-Domain (babixgo.de/*)  
**Last Major Change:** Multi-domain to single-domain migration (2026-01-15)
</artifact>