---
applyTo: "**/user/**/*.php"
---

# User Area Instructions

When working with files in the `/user/` directory, follow these guidelines for user-facing features:

## User Area Pages

The user section includes:
- `/user/index.php` - User dashboard
- `/user/profile.php` - View user profile
- `/user/edit-profile.php` - Edit user profile
- `/user/my-comments.php` - User's comments
- `/user/my-downloads.php` - Download history
- `/user/settings.php` - Account settings

## Authentication Requirement

**EVERY user page MUST include authentication check:**
```php
<?php
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/auth-check.php';  // Redirects to login if not logged in

// User is authenticated - proceed with page
?>
```

## User Dashboard

### Dashboard Statistics
```php
$userId = $_SESSION['user_id'];
$db = Database::getInstance();

// Get user info
$stmt = $db->prepare("SELECT username, email, role, verified, created_at FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Count user's comments
$stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
$stmt->execute([$userId]);
$commentCount = $stmt->fetchColumn();

// Count user's downloads
$stmt = $db->prepare("SELECT COUNT(*) FROM download_logs WHERE user_id = ?");
$stmt->execute([$userId]);
$downloadCount = $stmt->fetchColumn();

// Get recent activity
$stmt = $db->prepare("
    SELECT c.comment, c.created_at, c.page_url
    FROM comments c
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$recentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Profile Management

### Display Profile
```php
// Get user data
$username = $_GET['username'] ?? $_SESSION['username'];
$stmt = $db->prepare("
    SELECT id, username, email, role, verified, created_at, bio, avatar
    FROM users 
    WHERE username = ?
");
$stmt->execute([$username]);
$profileUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profileUser) {
    http_response_code(404);
    include BASE_PATH . 'babixgo.de/404.php';
    exit;
}

// Check if viewing own profile
$isOwnProfile = ($profileUser['id'] === $_SESSION['user_id']);
```

### Edit Profile
```php
// Validate CSRF token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Invalid request');
}

$errors = [];

// Validate username
$newUsername = trim($_POST['username']);
if (strlen($newUsername) < 3 || strlen($newUsername) > 20) {
    $errors[] = 'Username must be 3-20 characters';
}
if (!preg_match('/^[a-zA-Z0-9_-]+$/', $newUsername)) {
    $errors[] = 'Username can only contain letters, numbers, underscores, and hyphens';
}

// Check if username is taken (if changed)
if ($newUsername !== $user['username']) {
    $stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $stmt->execute([$newUsername, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = 'Username already taken';
    }
}

// Validate email
$newEmail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
if (!$newEmail) {
    $errors[] = 'Please enter a valid email address';
}

// Check if email is taken (if changed)
if ($newEmail !== $user['email']) {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$newEmail, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $errors[] = 'Email already registered';
    }
}

// Validate bio (optional)
$bio = trim($_POST['bio'] ?? '');
if (strlen($bio) > 500) {
    $errors[] = 'Bio must be 500 characters or less';
}

if (empty($errors)) {
    // Update profile
    $stmt = $db->prepare("
        UPDATE users 
        SET username = ?, email = ?, bio = ?
        WHERE id = ?
    ");
    $stmt->execute([$newUsername, $newEmail, $bio, $_SESSION['user_id']]);
    
    // Update session
    $_SESSION['username'] = $newUsername;
    $_SESSION['email'] = $newEmail;
    
    $_SESSION['success_message'] = 'Profile updated successfully';
    header('Location: /user/profile');
    exit;
}
```

## Avatar Upload

### Avatar Upload Handler
```php
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $avatar = $_FILES['avatar'];
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $avatar['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Avatar must be an image (JPEG, PNG, GIF, or WebP)';
    }
    
    // Validate file size (max 2MB)
    if ($avatar['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Avatar must be 2MB or less';
    }
    
    if (empty($errors)) {
        // Generate unique filename
        $extension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
        $filename = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
        $uploadPath = BASE_PATH . 'babixgo.de/assets/images/avatars/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        // Delete old avatar if exists
        if (!empty($user['avatar'])) {
            $oldAvatar = $uploadPath . $user['avatar'];
            if (file_exists($oldAvatar)) {
                unlink($oldAvatar);
            }
        }
        
        // Upload new avatar
        if (move_uploaded_file($avatar['tmp_name'], $uploadPath . $filename)) {
            // Update database
            $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$filename, $_SESSION['user_id']]);
            
            $_SESSION['success_message'] = 'Avatar uploaded successfully';
        } else {
            $errors[] = 'Failed to upload avatar';
        }
    }
}
```

## Account Settings

### Change Password
```php
// Verify current password
$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

$errors = [];

// Validate current password
$stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($currentPassword, $user['password'])) {
    $errors[] = 'Current password is incorrect';
}

// Validate new password
if (strlen($newPassword) < 8) {
    $errors[] = 'New password must be at least 8 characters';
}

if ($newPassword !== $confirmPassword) {
    $errors[] = 'New passwords do not match';
}

if (empty($errors)) {
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $_SESSION['user_id']]);
    
    $_SESSION['success_message'] = 'Password changed successfully';
    header('Location: /user/settings');
    exit;
}
```

### Delete Account
```php
// Require password confirmation
$password = $_POST['password'];

$stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($password, $user['password'])) {
    die('Incorrect password');
}

// Set success message BEFORE destroying session
$_SESSION['success_message'] = 'Account deleted successfully';

// Soft delete (preferred) - add deleted_at column
$stmt = $db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);

// Or hard delete
// $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
// $stmt->execute([$_SESSION['user_id']]);

// Clear session
session_destroy();

// Redirect to homepage
header('Location: /');
exit;
```

## User Comments

### List User Comments
```php
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalCount = $stmt->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Get comments
$stmt = $db->prepare("
    SELECT id, comment, page_url, approved, created_at
    FROM comments
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$_SESSION['user_id'], $perPage, $offset]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Delete Own Comment
```php
$commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);

// Verify comment belongs to user
$stmt = $db->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->execute([$commentId]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($comment && $comment['user_id'] === $_SESSION['user_id']) {
    $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    
    $_SESSION['success_message'] = 'Comment deleted';
} else {
    $_SESSION['error_message'] = 'Unauthorized';
}

header('Location: /user/my-comments');
exit;
```

## Download History

### List User Downloads
```php
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $db->prepare("SELECT COUNT(*) FROM download_logs WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalCount = $stmt->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Get download history
$stmt = $db->prepare("
    SELECT dl.id, dl.downloaded_at, d.title, d.filename, d.filetype, d.version
    FROM download_logs dl
    JOIN downloads d ON dl.download_id = d.id
    WHERE dl.user_id = ?
    ORDER BY dl.downloaded_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$_SESSION['user_id'], $perPage, $offset]);
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## User Preferences

### Save Preferences
```php
// Email notifications
$emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;

// Newsletter subscription
$newsletter = isset($_POST['newsletter']) ? 1 : 0;

// Privacy settings
$showProfile = isset($_POST['show_profile']) ? 1 : 0;

// Update preferences
$stmt = $db->prepare("
    UPDATE users 
    SET email_notifications = ?, newsletter = ?, show_profile = ?
    WHERE id = ?
");
$stmt->execute([$emailNotifications, $newsletter, $showProfile, $_SESSION['user_id']]);

$_SESSION['success_message'] = 'Preferences saved';
header('Location: /user/settings');
exit;
```

## Security Considerations

### CSRF Protection on All Forms
```php
// Generate token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include in form
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Validate in handler
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('Invalid request');
}
```

### Input Validation
```php
// ALWAYS validate and sanitize user input
$username = trim($_POST['username']);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$bio = trim($_POST['bio']);

// Escape output
echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
echo htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8');
```

### SQL Injection Prevention
```php
// ALWAYS use prepared statements
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
```

## UI Components

### User Navigation
```php
<nav class="user-nav">
    <a href="/user/" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">dashboard</span>
        Dashboard
    </a>
    <a href="/user/profile" class="<?= $currentPage === 'profile' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">person</span>
        Profile
    </a>
    <a href="/user/my-comments" class="<?= $currentPage === 'comments' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">comment</span>
        Comments
    </a>
    <a href="/user/my-downloads" class="<?= $currentPage === 'downloads' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">download</span>
        Downloads
    </a>
    <a href="/user/settings" class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
        <span class="material-symbols-outlined">settings</span>
        Settings
    </a>
    <a href="/auth/logout" class="logout">
        <span class="material-symbols-outlined">logout</span>
        Logout
    </a>
</nav>
```

### User Stats Cards
```php
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">comment</span>
        </div>
        <div class="stat-info">
            <h3><?= number_format($commentCount) ?></h3>
            <p>Comments</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">download</span>
        </div>
        <div class="stat-info">
            <h3><?= number_format($downloadCount) ?></h3>
            <p>Downloads</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <span class="material-symbols-outlined">calendar_today</span>
        </div>
        <div class="stat-info">
            <h3><?= date('M Y', strtotime($user['created_at'])) ?></h3>
            <p>Member Since</p>
        </div>
    </div>
</div>
```

## Testing Checklist

Before committing user area changes:
- [ ] Authentication check is present on ALL user pages
- [ ] CSRF tokens are present on ALL forms
- [ ] All user input is validated and sanitized
- [ ] SQL queries use prepared statements
- [ ] Users can only access/modify their own data
- [ ] Password changes require current password
- [ ] Account deletion requires password confirmation
- [ ] Avatar uploads validate type and size
- [ ] Email changes trigger verification
- [ ] Profile updates work correctly
- [ ] Comments list shows only user's comments
- [ ] Download history shows only user's downloads
- [ ] Preferences save correctly
- [ ] Error messages are user-friendly
- [ ] Success messages display correctly
- [ ] UI is consistent with design system (use user.css)
- [ ] Mobile view is tested and responsive

## Common Patterns

### Flash Messages
```php
// Display success message
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

// Display error message
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
```

### Empty State
```php
<?php if (empty($items)): ?>
    <div class="empty-state">
        <span class="material-symbols-outlined">inbox</span>
        <h3>No items yet</h3>
        <p>You haven't created any items yet. Get started!</p>
        <a href="/action" class="btn btn-primary">Create Item</a>
    </div>
<?php endif; ?>
```
