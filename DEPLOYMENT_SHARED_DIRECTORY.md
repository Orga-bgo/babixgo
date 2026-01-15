# Deployment Verification for Shared Directory Move

## Changes Summary

The `shared/` directory has been moved from `/babixgo.de/shared/` to the repository root `/shared/`.

## Required Server Configuration

When deploying to production, the web server must be configured to:

1. **Document Root**: Set to `/path/to/babixgo.de/`
2. **Shared Directory Access**: The `/shared/` directory needs to be accessible via web URLs

### Option 1: Apache Alias (Recommended)
Add to your Apache configuration or .htaccess:

```apache
Alias /shared /path/to/shared

<Directory "/path/to/shared">
    Options -Indexes
    Require all granted
    
    # Cache static assets
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType application/javascript "access plus 1 month"
        ExpiresByType image/svg+xml "access plus 3 months"
    </IfModule>
</Directory>
```

### Option 2: Symbolic Link
Create a symlink from the document root:

```bash
cd /path/to/babixgo.de/
ln -s ../shared shared
```

## PHP File Changes

### 1. Files using `$_SERVER['DOCUMENT_ROOT']`
**Pattern Changed:**
- Before: `$_SERVER['DOCUMENT_ROOT'] . '/shared/...'`
- After: `dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/...'`

**Files Updated (18 total):**
- babixgo.de/index.php
- babixgo.de/accounts/index.php
- babixgo.de/anleitungen/*/index.php
- babixgo.de/auth/*/index.php
- And 14 more files...

### 2. Files using `dirname(__DIR__, N)`
**Pattern:**
- Files maintain their original `dirname(__DIR__, N)` values
- These already correctly pointed to the repository root
- No changes needed (values were correct)

**Files with BASE_PATH (24 total):**
- All auth/, admin/, user/, and files/ PHP scripts
- All correctly resolve to repository root + 'shared/'

## Verification Tests

Run these tests after deployment:

```php
<?php
// Test 1: Check shared directory accessibility
$_SERVER['DOCUMENT_ROOT'] = '/var/www/babixgo.de';
$shared = dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/';
var_dump(is_dir($shared)); // Should be true

// Test 2: Check key files exist
$files_to_check = [
    'config/database.php',
    'config/session.php',
    'classes/User.php',
    'partials/header.php',
];

foreach ($files_to_check as $file) {
    $path = $shared . $file;
    if (!file_exists($path)) {
        echo "ERROR: $file not found!\n";
    }
}
?>
```

## Directory Structure

```
/var/www/                        # Server root
├── shared/                      # Shared resources (NEW LOCATION)
│   ├── assets/
│   ├── classes/
│   ├── config/
│   └── partials/
│
└── babixgo.de/                  # Document root
    ├── index.php
    ├── auth/
    ├── admin/
    ├── user/
    └── ... (all public files)
```

## Important Notes

1. **Asset URLs**: HTML references to `/shared/assets/...` remain unchanged
2. **PHP Includes**: All PHP includes now use `dirname($_SERVER['DOCUMENT_ROOT'])`
3. **No Functional Changes**: This is purely a structural reorganization
4. **Backward Compatibility**: Old structure will not work; must deploy with new structure

## Rollback Plan

If needed to rollback:
1. Move `/shared/` back to `/babixgo.de/shared/`
2. Revert PHP changes (restore previous commit)
3. Remove any Apache aliases or symlinks

