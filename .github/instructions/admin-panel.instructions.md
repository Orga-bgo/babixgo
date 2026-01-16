---
applyTo: "**/admin/**/*.php"
---

# Admin Panel Development Instructions

When working with files in the `/admin/` directory, follow these security and functionality guidelines:

## Critical Security Requirements

### 1. MANDATORY Admin Authorization Check

**EVERY admin page MUST include this at the top:**
```php
<?php
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once __DIR__ . '/includes/admin-check.php';  // Shows 403 if not admin

// Admin is authorized - proceed with page
?>
```

### 2. Additional .htaccess Protection

The `/admin/` directory has an additional `.htaccess` file for defense-in-depth. Do not remove or modify it unless absolutely necessary.

### 3. CSRF Protection on ALL Forms

```php
// At top of page
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// In form
<form method="POST" action="/admin/handler.php">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <!-- Form fields -->
</form>

// In handler
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    die('CSRF validation failed');
}
```

## Admin Panel Pages

### Current Admin Pages
- `/admin/index.php` - Dashboard with statistics
- `/admin/users.php` - User management (view, edit, delete, verify)
- `/admin/downloads.php` - Download management with file uploads
- `/admin/comments.php` - Comment moderation
- `/admin/user-edit.php` - Edit individual user
- `/admin/download-edit.php` - Edit individual download

## User Management Best Practices

### Listing Users
```php
// Use pagination for performance
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

$stmt = $db->prepare("
    SELECT id, username, email, role, verified, created_at 
    FROM users 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Editing Users
```php
// ALWAYS validate admin action
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    die('Unauthorized');
}

// Validate user ID
$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
if (!$userId) {
    die('Invalid user ID');
}

// Prevent admin from demoting themselves
if ($userId === $_SESSION['user_id'] && $_POST['role'] !== 'admin') {
    die('Cannot change your own role');
}

// Update user
$stmt = $db->prepare("
    UPDATE users 
    SET username = ?, email = ?, role = ?, verified = ? 
    WHERE id = ?
");
$stmt->execute([
    $_POST['username'],
    $_POST['email'],
    $_POST['role'],
    isset($_POST['verified']) ? 1 : 0,
    $userId
]);
```

### Deleting Users
```php
// Prevent admin from deleting themselves
if ($userId === $_SESSION['user_id']) {
    die('Cannot delete your own account');
}

// Soft delete is preferred (add a deleted_at column)
$stmt = $db->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
$stmt->execute([$userId]);

// Or hard delete if necessary
$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$userId]);
```

## Download Management

### File Upload Validation
```php
define('DOWNLOADS_PATH', BASE_PATH . 'downloads/');

$allowedTypes = [
    'apk' => ['application/vnd.android.package-archive'],
    'exe' => ['application/x-msdownload', 'application/x-executable'],
    'scripts' => ['text/x-python', 'application/x-sh', 'text/x-sh']
];

// For text/plain scripts, additional validation is needed
if ($fileType === 'scripts' && $mimeType === 'text/plain') {
    // Verify file extension is allowed
    $extension = strtolower(pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['py', 'sh', 'bash', 'txt'];
    if (!in_array($extension, $allowedExtensions)) {
        die('Invalid script file extension');
    }
}

$fileType = $_POST['file_type'];
$uploadedFile = $_FILES['download_file'];

// Validate file type
if (!isset($allowedTypes[$fileType])) {
    die('Invalid file type');
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes[$fileType])) {
    die('Invalid file MIME type');
}

// Validate size (500MB max)
$maxSize = 500 * 1024 * 1024;
if ($uploadedFile['size'] > $maxSize) {
    die('File too large (max 500MB)');
}

// Generate safe filename
$extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
$safeBasename = preg_replace('/[^a-zA-Z0-9_-]/', '', pathinfo($uploadedFile['name'], PATHINFO_FILENAME));
$safeFilename = $safeBasename . '.' . $extension;

// Ensure unique filename
$targetPath = DOWNLOADS_PATH . $fileType . '/' . $safeFilename;
$counter = 1;
while (file_exists($targetPath)) {
    $safeFilename = $safeBasename . '_' . $counter . '.' . $extension;
    $targetPath = DOWNLOADS_PATH . $fileType . '/' . $safeFilename;
    $counter++;
}

// Move uploaded file
if (!move_uploaded_file($uploadedFile['tmp_name'], $targetPath)) {
    die('Failed to save file');
}

// Set secure permissions
chmod($targetPath, 0644);

// Save to database
$stmt = $db->prepare("
    INSERT INTO downloads (title, filename, filepath, filetype, filesize, version, description, active)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
");
$stmt->execute([
    $_POST['title'],
    $safeFilename,
    $fileType . '/' . $safeFilename,
    $fileType,
    $uploadedFile['size'],
    $_POST['version'] ?? '1.0',
    $_POST['description'] ?? ''
]);
```

### Editing Downloads
```php
$downloadId = filter_input(INPUT_POST, 'download_id', FILTER_VALIDATE_INT);

$stmt = $db->prepare("
    UPDATE downloads 
    SET title = ?, version = ?, description = ?, active = ?
    WHERE id = ?
");
$stmt->execute([
    $_POST['title'],
    $_POST['version'],
    $_POST['description'],
    isset($_POST['active']) ? 1 : 0,
    $downloadId
]);
```

### Deleting Downloads
```php
// Get file info before deleting from database
$stmt = $db->prepare("SELECT filepath FROM downloads WHERE id = ?");
$stmt->execute([$downloadId]);
$download = $stmt->fetch(PDO::FETCH_ASSOC);

if ($download) {
    // Delete file from filesystem
    $filePath = DOWNLOADS_PATH . $download['filepath'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Delete from database
    $stmt = $db->prepare("DELETE FROM downloads WHERE id = ?");
    $stmt->execute([$downloadId]);
}
```

## Comment Moderation

### Listing Comments
```php
$stmt = $db->prepare("
    SELECT c.*, u.username 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Approving/Rejecting Comments
```php
$commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
$action = $_POST['action']; // 'approve' or 'reject'

if ($action === 'approve') {
    $stmt = $db->prepare("UPDATE comments SET approved = 1 WHERE id = ?");
    $stmt->execute([$commentId]);
} elseif ($action === 'reject' || $action === 'delete') {
    $stmt = $db->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
}
```

## Dashboard Statistics

### Calculating Statistics
```php
// Total users
$stmt = $db->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// Total downloads (files)
$stmt = $db->query("SELECT COUNT(*) FROM downloads WHERE active = 1");
$totalDownloads = $stmt->fetchColumn();

// Total download count (sum of all downloads)
$stmt = $db->query("SELECT SUM(download_count) FROM downloads");
$totalDownloadCount = $stmt->fetchColumn();

// Pending comments
$stmt = $db->query("SELECT COUNT(*) FROM comments WHERE approved = 0");
$pendingComments = $stmt->fetchColumn();

// Recent activity (last 7 days)
$stmt = $db->query("
    SELECT COUNT(*) FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$newUsers = $stmt->fetchColumn();
```

## Bulk Actions

### Bulk User Operations
```php
if (isset($_POST['bulk_action']) && isset($_POST['user_ids'])) {
    $action = $_POST['bulk_action'];
    $userIds = $_POST['user_ids']; // Array of IDs
    
    // Validate all IDs
    $userIds = array_filter($userIds, 'is_numeric');
    
    if (empty($userIds)) {
        die('No valid user IDs');
    }
    
    // Ensure current admin is not in the list
    $userIds = array_diff($userIds, [$_SESSION['user_id']]);
    
    $placeholders = implode(',', array_fill(0, count($userIds), '?'));
    
    switch ($action) {
        case 'delete':
            $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'verify':
            $stmt = $db->prepare("UPDATE users SET verified = 1 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
            
        case 'unverify':
            $stmt = $db->prepare("UPDATE users SET verified = 0 WHERE id IN ($placeholders)");
            $stmt->execute($userIds);
            break;
    }
}
```

## Activity Logging

### Log Admin Actions
```php
function logAdminAction($db, $adminId, $action, $targetType, $targetId, $details = '') {
    $stmt = $db->prepare("
        INSERT INTO admin_logs (admin_id, action, target_type, target_id, details, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$adminId, $action, $targetType, $targetId, $details]);
}

// Usage examples
logAdminAction($db, $_SESSION['user_id'], 'user_edit', 'user', $userId, "Changed role to admin");
logAdminAction($db, $_SESSION['user_id'], 'download_delete', 'download', $downloadId, "Deleted file: {$filename}");
logAdminAction($db, $_SESSION['user_id'], 'comment_approve', 'comment', $commentId, "Approved comment");
```

## Error Handling in Admin Panel

```php
try {
    // Admin operation
    $stmt = $db->prepare("...");
    $stmt->execute([...]);
    
    // Success message
    $_SESSION['success_message'] = 'Operation completed successfully';
    header('Location: /admin/users');
    exit;
    
} catch (Exception $e) {
    // Log error
    error_log("Admin error: " . $e->getMessage());
    
    // User-friendly error
    $_SESSION['error_message'] = 'Operation failed. Please try again.';
    header('Location: /admin/users');
    exit;
}
```

## Display Messages in Admin UI

```php
// At top of admin page
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
```

## Testing Checklist for Admin Features

Before committing admin panel changes:
- [ ] Admin authorization check is present on ALL admin pages
- [ ] CSRF tokens are present on ALL forms
- [ ] All user input is validated and sanitized
- [ ] All database queries use prepared statements
- [ ] Cannot perform actions on own admin account that would lock out
- [ ] File uploads validate MIME type, size, and filename
- [ ] Error messages are user-friendly (no sensitive data exposed)
- [ ] Success/error messages are displayed correctly
- [ ] Pagination works correctly
- [ ] Bulk actions work correctly
- [ ] Activity logging is implemented for sensitive actions
- [ ] UI is consistent with design system (use admin.css)
- [ ] Mobile view is tested and responsive

## Common Admin Patterns

### Data Table with Actions
```php
<table class="admin-table">
    <thead>
        <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>"></td>
                <td><?= $user['id'] ?></td>
                <td><?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $user['role'] ?></td>
                <td>
                    <a href="/admin/user-edit?id=<?= $user['id'] ?>" class="btn btn-sm">Edit</a>
                    <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                        <button onclick="deleteUser(<?= $user['id'] ?>)" class="btn btn-sm btn-danger">Delete</button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

### Pagination Component
```php
<?php
$totalPages = ceil($totalCount / $perPage);
if ($totalPages > 1):
?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn">Previous</a>
        <?php endif; ?>
        
        <span>Page <?= $page ?> of <?= $totalPages ?></span>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn">Next</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
```
