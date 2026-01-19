---
applyTo: "**/*.php"
---

# PHP Development Instructions

When working with PHP files in this repository, follow these mandatory guidelines:

## Path Management

1. **ALWAYS use the standard path pattern at the top of EVERY PHP file:**
   ```php
   <?php
   define('BASE_PATH', dirname(__DIR__, 2) . '/');
   define('SHARED_PATH', BASE_PATH . 'shared/');
   ```

2. **NEVER use:**
   - Relative paths: `require '../partials/header.php';`
   - `__DIR__` for shared resources: `require __DIR__ . '/../partials/header.php';`
   - Short PHP tags: `<? echo $var; ?>` (always use `<?php` and `<?=`)

## Required Partials Inclusion Order

Every page must include partials in this exact order:

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

## Security Requirements

### SQL Injection Prevention
```php
// ✅ ALWAYS use prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);

// ❌ NEVER use string concatenation
$query = "SELECT * FROM users WHERE id = " . $_GET['id'];  // FORBIDDEN
```

### XSS Prevention
```php
// ✅ ALWAYS escape output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// ❌ NEVER output raw user input
echo $_POST['comment'];  // FORBIDDEN
```

### CSRF Protection
```php
// Generate token at top of page with form
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include in form
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Validate in handler
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF validation failed');
}
```

## Authentication Checks

### User Area Files (`/user/*.php`)
```php
<?php
// At top of ALL /user/*.php pages
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/auth-check.php';  // Redirects if not logged in
?>
```

### Admin Area Files (`/admin/*.php`)
```php
<?php
// At top of ALL /admin/*.php pages
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/admin-check.php';  // Shows 403 if not admin
?>
```

## Code Style

1. **Use single quotes for simple strings, double quotes for variables:**
   ```php
   $simple = 'Hello World';
   $withVar = "Hello {$username}";
   ```

2. **Always validate and sanitize input:**
   ```php
   $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
   $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
   ```

3. **Use meaningful variable names:**
   ```php
   // ✅ GOOD
   $userEmail = $user['email'];
   $downloadCount = count($downloads);
   
   // ❌ BAD
   $e = $user['email'];
   $c = count($downloads);
   ```

## Error Handling

```php
// Use try-catch for database operations
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT ...");
    $stmt->execute([$param]);
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    // Show user-friendly message
    $errorMessage = "An error occurred. Please try again.";
}
```

## File Operations

```php
// ALWAYS validate file paths to prevent directory traversal
$filePath = DOWNLOADS_PATH . $fileType . '/' . $filename;
$realPath = realpath($filePath);

if (!$realPath || strpos($realPath, DOWNLOADS_PATH) !== 0) {
    http_response_code(403);
    die('Access denied');
}
```

## Session Management

```php
// Session is configured globally in shared/config/session.php
// Access session variables directly:
$userId = $_SESSION['user_id'] ?? null;
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// ALWAYS check if user is logged in before accessing user data
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login');
    exit;
}
```

## Common Patterns

### Database Query Pattern
```php
$db = Database::getInstance();
$stmt = $db->prepare("SELECT * FROM table WHERE column = ?");
$stmt->execute([$value]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Redirect Pattern
```php
// ✅ ALWAYS use relative paths
header('Location: /section/page');
exit;

// ❌ NEVER use full URLs
header('Location: https://babixgo.de/section/page');  // WRONG
```

### Form Validation Pattern
```php
$errors = [];

if (empty($_POST['username'])) {
    $errors[] = 'Username is required';
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($errors)) {
    // Process form
} else {
    // Display errors
}
```

## Testing Checklist

Before committing PHP changes:
- [ ] No PHP syntax errors: `php -l filename.php`
- [ ] All database queries use prepared statements
- [ ] All user input is validated and escaped
- [ ] CSRF tokens are present in all forms
- [ ] Authentication checks are in place for protected pages
- [ ] Error handling is implemented
- [ ] File paths are validated
- [ ] No hardcoded credentials or sensitive data
