# Shared Assets

This directory contains CSS and JavaScript files that are shared across all babixgo.de domains.

**Note:** PHP partials are located in `/shared/partials/` (not in this assets directory).

## Directory Structure

```
shared/assets/
├── css/
│   ├── main.css      # Main site styles (base, forms, navigation, etc.)
│   └── admin.css     # Admin panel specific styles
└── js/
    ├── form-validation.js  # Client-side form validation
    └── admin.js           # Admin panel interactions

shared/partials/      # PHP partials (separate from assets)
├── header.php     # HTML head with CSS includes
├── footer.php     # Closing body tag with JS includes
├── nav.php        # Main navigation bar
└── admin-nav.php  # Admin panel navigation bar
```

## Using Shared CSS

The CSS files are located in `shared/assets/css/` and should be referenced from your pages:

### Method 1: Using Header Partial (Recommended)

```php
<?php
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('SHARED_ASSETS_PATH', '../../shared/assets/'); // Adjust path as needed

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Page configuration
$pageTitle = 'My Page - babixgo.de';
$includeAdminCSS = false; // Set to true for admin pages
$includeValidationJS = true; // Set to true if using form validation
$includeAdminJS = false; // Set to true for admin pages

// Include header
require_once SHARED_PATH . 'partials/header.php';
?>
```

### Method 2: Direct Link

```html
<link rel="stylesheet" href="../../shared/assets/css/main.css">
<!-- For admin pages, also include: -->
<link rel="stylesheet" href="../../shared/assets/css/admin.css">
```

## Using Shared JavaScript

### Method 1: Using Footer Partial (Recommended)

```php
<?php
// At the end of your page, before </body>
$includeValidationJS = true; // For form validation
$includeAdminJS = true; // For admin panel functionality
require_once SHARED_PATH . 'partials/footer.php';
?>
```

### Method 2: Direct Link

```html
<script src="../../shared/assets/js/form-validation.js"></script>
<script src="../../shared/assets/js/admin.js"></script>
```

## Using PHP Partials

### Header Partial

Includes the HTML `<head>` section with proper CSS includes.

**Variables you can set:**
- `$pageTitle` - Page title (default: 'babixgo.de')
- `$includeAdminCSS` - Include admin.css (default: false)
- `$additionalCSS` - Array of additional CSS files to include

```php
$pageTitle = 'Login - babixgo.de';
$includeAdminCSS = false;
require_once SHARED_PATH . 'partials/header.php';
```

### Footer Partial

Includes closing `</body>` and `</html>` tags with JavaScript includes.

**Variables you can set:**
- `$includeValidationJS` - Include form-validation.js (default: false)
- `$includeAdminJS` - Include admin.js (default: false)
- `$additionalJS` - Array of additional JS files to include

```php
$includeValidationJS = true;
$includeAdminJS = false;
require_once SHARED_PATH . 'partials/footer.php';
```

### Navigation Partial

Displays the main navigation bar with automatic login detection.

**Variables you can set:**
- `$currentPage` - Current page identifier for active state ('profile', 'login', 'register')
- `$showAdminLink` - Force show/hide admin link (default: auto-detect based on role)

```php
$currentPage = 'profile';
require_once SHARED_PATH . 'partials/nav.php';
```

### Admin Navigation Partial

Displays the admin panel navigation bar.

**Variables you can set:**
- `$currentAdminPage` - Current admin page ('dashboard', 'users', 'downloads', 'comments')

```php
$currentAdminPage = 'users';
require_once SHARED_PATH . 'partials/admin-nav.php';
```

## Complete Page Example

### Authentication Page Example

```php
<?php
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('SHARED_ASSETS_PATH', '../../shared/assets/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Page configuration
$pageTitle = 'Login - babixgo.de';
$currentPage = 'login';

// Include header and navigation
require_once SHARED_PATH . 'partials/header.php';
require_once SHARED_PATH . 'partials/nav.php';
?>

<div class="auth-container">
    <!-- Your page content here -->
</div>

<?php
// Include footer with validation JS
$includeValidationJS = true;
require_once SHARED_PATH . 'partials/footer.php';
?>
```

### Admin Page Example

```php
<?php
require_once __DIR__ . '/../includes/admin-check.php';

// Page configuration
$pageTitle = 'User Management - babixgo.de Admin';
$currentAdminPage = 'users';
$includeAdminCSS = true;

// Include header and admin navigation
require_once SHARED_PATH . 'partials/header.php';
require_once SHARED_PATH . 'partials/admin-nav.php';
?>

<div class="container">
    <!-- Your admin page content here -->
</div>

<?php
// Include footer with admin JS
$includeAdminJS = true;
require_once SHARED_PATH . 'partials/footer.php';
?>
```

## Path Configuration

When using shared assets, you need to define the correct path based on your current file location:

- From `auth/public/`: use `../../shared/assets/`
- From `auth/public/admin/`: use `../../../shared/assets/`
- From root: use `shared/assets/`

The `SHARED_ASSETS_PATH` constant should be set at the top of each page:

```php
// For files in auth/public/
define('SHARED_ASSETS_PATH', '../../shared/assets/');

// For files in auth/public/admin/
define('SHARED_ASSETS_PATH', '../../../shared/assets/');
```

## Benefits of Using Shared Assets

1. **Consistency**: All pages use the same styles and scripts
2. **Maintainability**: Update CSS/JS in one place, applies everywhere
3. **Performance**: Browser caches shared files across domains
4. **DRY Principle**: Don't repeat yourself - write once, use everywhere
5. **Easy Updates**: Change navigation/header/footer globally

## CSS Classes Available

### Main.css includes:
- Navigation (`.main-nav`, `.nav-menu`, `.logo`)
- Forms (`.form-group`, `.btn`, `.form-control`, `.form-input`, `.form-textarea`)
- Auth Pages (`.auth-container`, `.auth-box`, `.auth-footer`, `.subtitle`, `.error-message`, `.hint`, `.checkbox-group`)
- Messages (`.message`, `.message-success`, `.message-error`, `.message-info`, `.message-warning`)
- Containers (`.container`, `.box`, `.section-card`)
- Cards (`.profile-card`, `.profile-grid`, `.profile-header`, `.content-card`)
- Badges (`.badge`, `.badge-admin`, `.badge-success`, `.badge-warning`)
- Responsive utilities

### Admin.css includes:
- Statistics (`.stats-grid`, `.stat-card`, `.stat-value`)
- Tables (`.admin-table`, `.table-container`)
- Toolbar (`.toolbar`, `.search-form`, `.bulk-actions`)
- Admin-specific buttons (`.btn-small`)
- Pagination (`.pagination`)

## JavaScript Functions Available

### form-validation.js:
- `validateUsername(username)`
- `validateEmail(email)`
- `validatePassword(password)`
- `validatePasswordConfirmation(password, confirmation)`
- Real-time validation on form fields

### admin.js:
- `showMessage(message, type)`
- `confirmAction(message, callback)`
- `toggleAll(masterCheckbox, checkboxClass)`
- `getSelectedValues(checkboxClass)`
- `formatFileSize(bytes)`
- `formatNumber(num)`
- And more utilities for admin functionality

## Notes

- All paths are relative to ensure proper loading across different page locations
- The `SHARED_ASSETS_PATH` constant allows flexibility in file organization
- CSS and JS files are modular and can be included independently
- Partials automatically handle responsive design and accessibility
