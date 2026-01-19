---
applyTo: "**/files/**/*.php"
---

# Download Portal Instructions

When working with files in the `/files/` directory, follow these security and functionality guidelines:

## Download Portal Pages

The download portal includes:
- `/files/index.php` - Main download portal homepage
- `/files/browse.php` - Browse all downloads
- `/files/category.php` - Filter by category (APK, EXE, Scripts)
- `/files/download.php` - Secure download handler
- `/files/includes/download-handler.php` - Download logic

## Critical Security Requirements

### 1. Protected Downloads Directory

**NEVER serve files directly from `/downloads/` directory:**
```apache
# /downloads/.htaccess MUST contain:
Order Deny,Allow
Deny from all
```

**ALWAYS serve files through PHP handler:**
- Users access: `/files/download/123/apk` (friendly URL)
- Rewrite rule routes to: `/files/download.php?id=123&type=apk`
- PHP validates, logs, and serves file

### 2. Download Handler Security

```php
<?php
define('BASE_PATH', dirname(__DIR__, 2) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('DOWNLOADS_PATH', BASE_PATH . 'downloads/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';

// Validate inputs
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fileType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

if (!$fileId || !$fileType) {
    http_response_code(400);
    die('Invalid request');
}

// Validate file type
$allowedTypes = ['apk', 'exe', 'scripts'];
if (!in_array($fileType, $allowedTypes)) {
    http_response_code(400);
    die('Invalid file type');
}

// Fetch from database
$db = Database::getInstance();
$stmt = $db->prepare("
    SELECT id, filename, filepath, filesize, filetype 
    FROM downloads 
    WHERE id = ? AND filetype = ? AND active = 1
");
$stmt->execute([$fileId, $fileType]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$file) {
    http_response_code(404);
    die('File not found');
}

// Build file path
$filePath = DOWNLOADS_PATH . $file['filepath'];

// Security: Verify path is within downloads directory
$realPath = realpath($filePath);
if (!$realPath || strpos($realPath, realpath(DOWNLOADS_PATH)) !== 0) {
    error_log("Path traversal attempt: {$filePath}");
    http_response_code(403);
    die('Access denied');
}

// Verify file exists
if (!file_exists($realPath)) {
    error_log("File not found on disk: {$realPath}");
    http_response_code(404);
    die('File not found');
}

// Log download
$userId = $_SESSION['user_id'] ?? null;
$stmt = $db->prepare("
    INSERT INTO download_logs (download_id, user_id, ip_address, user_agent, downloaded_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->execute([
    $fileId,
    $userId,
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT'] ?? ''
]);

// Increment download counter
$stmt = $db->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
$stmt->execute([$fileId]);

// Determine MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $realPath);
finfo_close($finfo);

// Serve file
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . basename($file['filename']) . '"');
header('Content-Length: ' . filesize($realPath));
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');

// Disable output buffering for large files
if (ob_get_level()) {
    ob_end_clean();
}

readfile($realPath);
exit;
?>
```

## Database Schema for Downloads

### downloads Table
```sql
CREATE TABLE downloads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    filepath VARCHAR(500) NOT NULL,
    filetype ENUM('apk', 'exe', 'scripts') NOT NULL,
    filesize BIGINT NOT NULL,
    version VARCHAR(50),
    description TEXT,
    download_count INT DEFAULT 0,
    active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### download_logs Table
```sql
CREATE TABLE download_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    download_id INT NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    downloaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (download_id) REFERENCES downloads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

## Listing Downloads

### Category Page Pattern
```php
<?php
// Validate category
$category = $_GET['type'] ?? 'all';
$allowedCategories = ['all', 'apk', 'exe', 'scripts'];

if (!in_array($category, $allowedCategories)) {
    $category = 'all';
}

// Build query
$db = Database::getInstance();

if ($category === 'all') {
    $stmt = $db->query("
        SELECT id, title, filename, filetype, filesize, version, download_count, created_at
        FROM downloads
        WHERE active = 1
        ORDER BY created_at DESC
    ");
    $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $db->prepare("
        SELECT id, title, filename, filetype, filesize, version, download_count, created_at
        FROM downloads
        WHERE active = 1 AND filetype = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$category]);
    $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
```

## Display Pattern

### Download Card
```php
<div class="download-card">
    <div class="download-icon">
        <?php if ($download['filetype'] === 'apk'): ?>
            <span class="material-symbols-outlined">android</span>
        <?php elseif ($download['filetype'] === 'exe'): ?>
            <span class="material-symbols-outlined">desktop_windows</span>
        <?php else: ?>
            <span class="material-symbols-outlined">code</span>
        <?php endif; ?>
    </div>
    
    <div class="download-info">
        <h3><?= htmlspecialchars($download['title'], ENT_QUOTES, 'UTF-8') ?></h3>
        
        <div class="download-meta">
            <span class="version">v<?= htmlspecialchars($download['version'], ENT_QUOTES, 'UTF-8') ?></span>
            <span class="size"><?= formatFileSize($download['filesize']) ?></span>
            <span class="downloads"><?= number_format($download['download_count']) ?> downloads</span>
        </div>
        
        <?php if (!empty($download['description'])): ?>
            <p class="description"><?= htmlspecialchars($download['description'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
    </div>
    
    <div class="download-actions">
        <a href="/files/download/<?= $download['id'] ?>/<?= $download['filetype'] ?>" 
           class="btn btn-primary"
           download>
            <span class="material-symbols-outlined">download</span>
            Download
        </a>
    </div>
</div>
```

## Utility Functions

### Format File Size
```php
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
```

### Get File Type Label
```php
function getFileTypeLabel($type) {
    $labels = [
        'apk' => 'Android App',
        'exe' => 'Windows Executable',
        'scripts' => 'Script/Tool'
    ];
    return $labels[$type] ?? 'Unknown';
}
```

### Get File Type Icon
```php
function getFileTypeIcon($type) {
    $icons = [
        'apk' => 'android',
        'exe' => 'desktop_windows',
        'scripts' => 'code'
    ];
    return $icons[$type] ?? 'file_download';
}
```

## Search and Filter

### Search Implementation
```php
$searchQuery = $_GET['search'] ?? '';
$searchQuery = trim($searchQuery);

if (!empty($searchQuery)) {
    $stmt = $db->prepare("
        SELECT id, title, filename, filetype, filesize, version, download_count, created_at
        FROM downloads
        WHERE active = 1 
        AND (title LIKE ? OR description LIKE ? OR filename LIKE ?)
        ORDER BY download_count DESC, created_at DESC
    ");
    $searchTerm = '%' . $searchQuery . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

### Filter by Multiple Criteria
```php
$filters = [];
$params = [];

// Filter by type
if (isset($_GET['type']) && in_array($_GET['type'], ['apk', 'exe', 'scripts'])) {
    $filters[] = "filetype = ?";
    $params[] = $_GET['type'];
}

// Filter by version
if (isset($_GET['version']) && !empty($_GET['version'])) {
    $filters[] = "version = ?";
    $params[] = $_GET['version'];
}

// Build query
$sql = "SELECT * FROM downloads WHERE active = 1";
if (!empty($filters)) {
    $sql .= " AND " . implode(" AND ", $filters);
}
$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Pagination

### Pagination Implementation
```php
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
$perPage = 12; // 12 items per page
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $db->query("SELECT COUNT(*) FROM downloads WHERE active = 1");
$totalCount = $stmt->fetchColumn();
$totalPages = ceil($totalCount / $perPage);

// Get downloads for current page
$stmt = $db->prepare("
    SELECT * FROM downloads 
    WHERE active = 1 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$perPage, $offset]);
$downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### Pagination Display
```php
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn">
                <span class="material-symbols-outlined">chevron_left</span>
                Previous
            </a>
        <?php endif; ?>
        
        <span class="page-info">
            Page <?= $page ?> of <?= $totalPages ?>
        </span>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn">
                Next
                <span class="material-symbols-outlined">chevron_right</span>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>
```

## Analytics and Tracking

### Download Statistics
```php
// Get most popular downloads
$stmt = $db->query("
    SELECT id, title, download_count 
    FROM downloads 
    WHERE active = 1 
    ORDER BY download_count DESC 
    LIMIT 10
");
$popularDownloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent downloads
$stmt = $db->query("
    SELECT d.title, dl.downloaded_at, u.username
    FROM download_logs dl
    JOIN downloads d ON dl.download_id = d.id
    LEFT JOIN users u ON dl.user_id = u.id
    ORDER BY dl.downloaded_at DESC
    LIMIT 20
");
$recentDownloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get download stats by type
$stmt = $db->query("
    SELECT filetype, COUNT(*) as count, SUM(download_count) as total_downloads
    FROM downloads
    WHERE active = 1
    GROUP BY filetype
");
$typeStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

## Error Handling

### Graceful Error Handling
```php
try {
    // Download operation
    $stmt = $db->prepare("...");
    $stmt->execute([...]);
    
} catch (Exception $e) {
    // Log error
    error_log("Download error: " . $e->getMessage());
    
    // Show user-friendly error
    http_response_code(500);
    include BASE_PATH . 'babixgo.de/500.php';
    exit;
}
```

## Testing Checklist

Before committing download portal changes:
- [ ] Downloads directory is protected via .htaccess
- [ ] All downloads go through PHP handler
- [ ] File paths are validated to prevent directory traversal
- [ ] Download logging is implemented
- [ ] Download counter increments correctly
- [ ] File type validation is implemented
- [ ] MIME type is correctly determined
- [ ] Large files download without timeout
- [ ] Search and filter work correctly
- [ ] Pagination works correctly
- [ ] All user input is validated and sanitized
- [ ] SQL queries use prepared statements
- [ ] Error handling is graceful
- [ ] Analytics data is accurate
- [ ] File size formatting is correct
- [ ] Icons match file types

## Performance Considerations

### Large File Downloads
```php
// Disable script timeout for large files
set_time_limit(0);

// Disable output buffering
if (ob_get_level()) {
    ob_end_clean();
}

// Use chunked reading for large files
$handle = fopen($realPath, 'rb');
while (!feof($handle)) {
    echo fread($handle, 8192); // 8KB chunks
    flush();
}
fclose($handle);
```

### Caching Headers
```php
// For file metadata pages (category, browse)
header('Cache-Control: public, max-age=3600'); // 1 hour

// For download handler
header('Cache-Control: no-cache, must-revalidate');
header('Expires: 0');
```
