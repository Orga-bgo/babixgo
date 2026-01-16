# Migration Guide: Multi-Domain to Single-Domain Architecture

**Date**: January 15, 2026  
**Version**: 2.0.0  
**Status**: Complete

---

## Overview

This document details the migration from a multi-domain architecture (auth.babixgo.de, files.babixgo.de) to a unified single-domain architecture under **babixgo.de**.

### Before (Multi-Domain)
```
- babixgo.de           → Main website
- auth.babixgo.de      → Authentication & Admin
- files.babixgo.de     → Download portal
```

### After (Single-Domain)
```
- babixgo.de/          → Main website
- babixgo.de/auth/     → Authentication
- babixgo.de/user/     → User profiles & dashboard
- babixgo.de/files/    → Download portal
- babixgo.de/admin/    → Admin panel
```

---

## New Directory Structure

```
/babixgo/
├── shared/                          # Shared resources (unchanged)
│   ├── assets/
│   ├── classes/
│   ├── config/
│   └── partials/
│
├── downloads/                       # Secure file storage (unchanged)
│   ├── .htaccess                   # Deny from all
│   ├── apk/
│   ├── exe/
│   └── scripts/
│
└── babixgo.de/                      # UNIFIED DOMAIN
    ├── .htaccess                   # Unified routing config
    ├── index.php                   # Homepage
    ├── manifest.json               # PWA manifest (updated)
    ├── sw.js                       # Service worker (updated)
    │
    ├── auth/                       # Authentication (was auth.babixgo.de)
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
    ├── user/                       # NEW: User area
    │   ├── index.php              # Dashboard
    │   ├── profile.php            # Public profile
    │   ├── edit-profile.php       # Edit profile
    │   ├── settings.php           # User settings
    │   ├── my-comments.php        # User's comments
    │   ├── my-downloads.php       # Download history
    │   └── includes/
    │       └── auth-check.php
    │
    ├── files/                      # Download portal (was files.babixgo.de)
    │   ├── index.php
    │   ├── browse.php
    │   ├── category.php
    │   ├── download.php
    │   └── includes/
    │
    ├── admin/                      # Admin panel (was auth.babixgo.de/admin)
    │   ├── .htaccess              # Additional protection
    │   ├── index.php              # Dashboard
    │   ├── users.php              # User management
    │   ├── user-edit.php
    │   ├── downloads.php          # Download management
    │   ├── download-edit.php
    │   ├── comments.php           # Comment moderation
    │   └── includes/
    │       ├── admin-check.php
    │       └── handlers/
    │
    ├── assets/
    │   └── css/
    │       └── user.css           # NEW: User area styles
    │
    └── [existing content directories]
        ├── accounts/
        ├── anleitungen/
        ├── wuerfel/
        └── ...
```

---

## URL Migration Map

### Authentication URLs

| Old URL | New URL |
|---------|---------|
| `https://auth.babixgo.de/login.php` | `https://babixgo.de/auth/login` |
| `https://auth.babixgo.de/register.php` | `https://babixgo.de/auth/register` |
| `https://auth.babixgo.de/logout.php` | `https://babixgo.de/auth/logout` |
| `https://auth.babixgo.de/verify-email.php` | `https://babixgo.de/auth/verify-email` |
| `https://auth.babixgo.de/forgot-password.php` | `https://babixgo.de/auth/forgot-password` |
| `https://auth.babixgo.de/reset-password.php` | `https://babixgo.de/auth/reset-password` |

### User Profile URLs (NEW)

| Feature | New URL |
|---------|---------|
| User Dashboard | `https://babixgo.de/user/` |
| Edit Profile | `https://babixgo.de/user/edit-profile` |
| Settings | `https://babixgo.de/user/settings` |
| My Comments | `https://babixgo.de/user/my-comments` |
| My Downloads | `https://babixgo.de/user/my-downloads` |
| Public Profile | `https://babixgo.de/user/profile/{username}` |

### Download Portal URLs

| Old URL | New URL |
|---------|---------|
| `https://files.babixgo.de/` | `https://babixgo.de/files/` |
| `https://files.babixgo.de/browse.php` | `https://babixgo.de/files/browse` |
| `https://files.babixgo.de/category.php?type=apk` | `https://babixgo.de/files/category/apk` |
| `https://files.babixgo.de/download.php?id=123` | `https://babixgo.de/files/download/123/apk` |

### Admin Panel URLs

| Old URL | New URL |
|---------|---------|
| `https://auth.babixgo.de/admin/` | `https://babixgo.de/admin/` |
| `https://auth.babixgo.de/admin/users.php` | `https://babixgo.de/admin/users` |
| `https://auth.babixgo.de/admin/user-edit.php?id=123` | `https://babixgo.de/admin/user-edit?id=123` |
| `https://auth.babixgo.de/admin/downloads.php` | `https://babixgo.de/admin/downloads` |
| `https://auth.babixgo.de/admin/comments.php` | `https://babixgo.de/admin/comments` |

---

## Key Technical Changes

### 1. Path Structure

**All files now use consistent BASE_PATH:**

```php
// In /babixgo.de/auth/*.php, /user/*.php, /files/*.php, /admin/*.php
define('BASE_PATH', dirname(__DIR__, 2) . '/');  // Points to /babixgo/
define('SHARED_PATH', BASE_PATH . 'shared/');

// Load shared resources
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';
```

### 2. Partials Include Pattern

**OLD (Multi-Domain):**
```php
require_once SHARED_PATH . 'assets/partials/header.php';
```

**NEW (Single-Domain):**
```php
require_once SHARED_PATH . 'partials/header.php';
```

### 3. Authentication Checks

**User Area (`/user/includes/auth-check.php`):**
- Redirects to `/auth/login` if not logged in
- Sets convenience variables: `$currentUserId`, `$currentUsername`, etc.

**Admin Area (`/admin/includes/admin-check.php`):**
- Requires login AND admin role
- Shows 403 error if not admin
- Redirects to `/auth/login` if not logged in

### 4. Redirects After Actions

**Login Success:**
```php
// OLD: header('Location: /index.php');
// NEW:
header('Location: /user/');
```

**Logout:**
```php
// OLD: header('Location: /login.php');
// NEW:
header('Location: /auth/login');
```

**Admin Actions:**
```php
// Redirects stay within /admin/ section
header('Location: /admin/users');
```

### 5. Navigation Links

**In shared header (`/shared/partials/header.php`):**
- Detects current section: main, auth, user, files, admin
- Shows user menu when logged in
- Shows login/register buttons for guests
- Admin link visible only to admins

---

## .htaccess Configuration

**Location:** `/babixgo.de/.htaccess`

### Key Rules:

1. **HTTPS Enforcement:**
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

2. **Downloads Directory Protection:**
   ```apache
   RewriteCond %{REQUEST_URI} ^/downloads/ [NC]
   RewriteRule .* - [F,L]
   ```

3. **Download Routing:**
   ```apache
   # /files/download/123/apk → files/download.php?id=123&type=apk
   RewriteRule ^files/download/([0-9]+)/(apk|exe|scripts)$ files/download.php?id=$1&type=$2 [L,QSA]
   ```

4. **Category Routing:**
   ```apache
   # /files/category/apk → files/category.php?type=apk
   RewriteRule ^files/category/(apk|exe|scripts)$ files/category.php?type=$1 [L,QSA]
   ```

5. **Clean URLs (Optional):**
   ```apache
   # Removes .php extension for auth, user, files, admin sections
   RewriteCond %{REQUEST_URI} ^/(auth|user|files|admin)/
   RewriteRule ^(.+)$ $1.php [L]
   ```

---

## PWA Updates

### Manifest (`/babixgo.de/public/manifest.json`)

**Added shortcuts:**
```json
{
  "shortcuts": [
    {
      "name": "Downloads",
      "url": "/files/"
    },
    {
      "name": "My Profile",
      "url": "/user/"
    },
    {
      "name": "Login",
      "url": "/auth/login"
    }
  ]
}
```

### Service Worker (`/babixgo.de/public/sw.js`)

**Updated caching strategy:**
- Admin area: No caching (always fresh data)
- Auth POST requests: No caching
- Static assets: Cache-first
- HTML pages: Network-first with offline fallback
- Supports all sections: /, /auth/, /user/, /files/, /admin/

---

## Database Changes

**No database schema changes required.**

All existing tables work with the new structure:
- `users` - User accounts (unchanged)
- `downloads` - Download files (unchanged)
- `download_logs` - Download tracking (unchanged)
- `comments` - User comments (unchanged)

---

## Session Management

**Session cookie domain remains:**
```php
ini_set('session.cookie_domain', '.babixgo.de');
```

**Session behavior:**
- Login on `/auth/login` → Session valid across entire site
- User data accessible in `$_SESSION` on all pages
- Logout from any section → Logs out everywhere

---

## Deployment Steps

### 1. Backup Current State

```bash
# Via FTP or SSH
tar -czf babixgo-backup-$(date +%Y%m%d).tar.gz babixgo.de/ auth/ files.babixgo.de/
```

### 2. Upload New Files

**Upload order:**
1. Upload `/babixgo.de/auth/` directory
2. Upload `/babixgo.de/user/` directory
3. Upload `/babixgo.de/files/` directory
4. Upload `/babixgo.de/admin/` directory
5. Upload `/babixgo.de/assets/css/user.css`
6. Upload updated `/babixgo.de/.htaccess`
7. Upload updated `/babixgo.de/public/manifest.json`
8. Upload updated `/babixgo.de/public/sw.js`
9. Upload updated `/shared/partials/header.php`

### 3. Update Subdomain Configuration (Strato)

**Option A: Redirect old subdomains (Recommended)**
- Configure `auth.babixgo.de` → redirect to `https://babixgo.de/auth/`
- Configure `files.babixgo.de` → redirect to `https://babixgo.de/files/`

**Option B: Remove old subdomains**
- Delete `auth.babixgo.de` subdomain
- Delete `files.babixgo.de` subdomain

### 4. Test All Sections

```
✅ Main site: https://babixgo.de/
✅ Login: https://babixgo.de/auth/login
✅ Register: https://babixgo.de/auth/register
✅ User Dashboard: https://babixgo.de/user/
✅ Files Portal: https://babixgo.de/files/
✅ Admin Panel: https://babixgo.de/admin/
✅ Download Handler: https://babixgo.de/files/download/1/apk
```

### 5. Monitor Error Logs

Check for any 404 errors or path issues:
```bash
tail -f /var/log/apache2/error.log  # or Strato's log location
```

### 6. Update External Links

If you have external links to:
- `auth.babixgo.de` → Update to `babixgo.de/auth/`
- `files.babixgo.de` → Update to `babixgo.de/files/`

### 7. Cleanup (After Verification)

Once everything works:
```bash
# Remove old directories (via FTP)
rm -rf auth/
rm -rf files.babixgo.de/
```

---

## Rollback Plan

If issues occur:

1. **Restore from backup:**
   ```bash
   tar -xzf babixgo-backup-YYYYMMDD.tar.gz
   ```

2. **Restore old .htaccess:**
   ```bash
   cp babixgo.de/.htaccess.backup babixgo.de/.htaccess
   ```

3. **Re-enable subdomains** in Strato panel

4. **Clear browser cache** and service worker:
   - Chrome DevTools → Application → Storage → Clear site data

---

## Compatibility Notes

### Backward Compatibility

**If you keep old subdomains active with redirects:**

Users visiting:
- `https://auth.babixgo.de/login.php` → 301 redirect to `https://babixgo.de/auth/login`
- `https://files.babixgo.de/` → 301 redirect to `https://babixgo.de/files/`

**Sessions:**
- Sessions remain valid due to `.babixgo.de` cookie domain
- Users stay logged in across the migration

### Breaking Changes

**None for end users** - All functionality preserved

**For developers:**
- Update any hardcoded domain references in code
- Update API endpoints if any external integrations exist
- Update documentation/README files

---

## Benefits of Single-Domain Architecture

### For Users:
✅ Simpler URLs (no subdomain confusion)  
✅ Consistent navigation across all sections  
✅ Single PWA installation covers everything  
✅ Unified search and indexing (better SEO)

### For Developers:
✅ Easier to maintain (one .htaccess, one routing config)  
✅ Shared resources work more efficiently  
✅ Simpler deployment process  
✅ No cross-domain issues  
✅ Clearer code organization

### For Hosting:
✅ Fewer subdomain configurations  
✅ Simpler SSL certificate management  
✅ Easier backup/restore process

---

## Support & Troubleshooting

### Common Issues:

**Issue: 404 on /auth/login**
- **Fix:** Check .htaccess uploaded correctly
- **Fix:** Verify clean URL rules are active

**Issue: Session lost after login**
- **Fix:** Check session.cookie_domain = '.babixgo.de'
- **Fix:** Clear browser cookies and try again

**Issue: Downloads not working**
- **Fix:** Verify /downloads/.htaccess denies access
- **Fix:** Check download.php has correct DOWNLOADS_PATH

**Issue: Admin panel shows 403**
- **Fix:** Verify user has 'admin' role in database
- **Fix:** Check admin-check.php is included correctly

**Issue: CSS not loading**
- **Fix:** Clear service worker cache
- **Fix:** Hard refresh (Ctrl+Shift+R)
- **Fix:** Check file paths in HTML

---

## Migration Checklist

- [x] Create new directory structure
- [x] Move auth files to /babixgo.de/auth/
- [x] Create user area in /babixgo.de/user/
- [x] Move files portal to /babixgo.de/files/
- [x] Move admin panel to /babixgo.de/admin/
- [x] Update all path references
- [x] Update shared header with user menu
- [x] Create unified .htaccess
- [x] Update PWA manifest and service worker
- [x] Create user.css stylesheet
- [ ] Deploy to production
- [ ] Configure subdomain redirects
- [ ] Test all sections
- [ ] Monitor error logs
- [ ] Update documentation

---

## Contact

For questions or issues with the migration:
- GitHub: Create an issue in the repository
- Email: Support contact as per README

**Last Updated:** January 15, 2026
