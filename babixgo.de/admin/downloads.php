<?php
/**
 * Download Management Page
 */

require_once __DIR__ . '/includes/admin-check.php';

$db = Database::getInstance();
$download = new Download();

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter
$filter = $_GET['filter'] ?? 'all';
$filterType = in_array($filter, ['apk', 'scripts', 'exe']) ? $filter : null;

// Get total count
$totalDownloads = $db->fetchOne(
    "SELECT COUNT(*) as count FROM downloads" . ($filterType ? " WHERE filetype = ?" : ""),
    $filterType ? [$filterType] : []
)['count'];
$totalPages = ceil($totalDownloads / $perPage);

// Get downloads
$downloads = $download->getAll($filterType, false, $perPage, $offset);

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file']) && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $filetype = $_POST['filetype'] ?? '';
    $version = trim($_POST['version'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    $errors = [];
    
    if (!in_array($filetype, ['apk', 'scripts', 'exe'])) {
        $errors[] = 'Invalid file type';
    }
    
    if (empty($version)) {
        $errors[] = 'Version is required';
    }
    
    // Validate file upload
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload failed';
    } else {
        // Check file size (max 500MB)
        if ($file['size'] > 500 * 1024 * 1024) {
            $errors[] = 'File too large (max 500MB)';
        }
        
        // Validate MIME type
        $allowedTypes = [
            'apk' => ['application/vnd.android.package-archive', 'application/octet-stream'],
            'exe' => ['application/x-msdownload', 'application/x-msdos-program', 'application/octet-stream'],
            'scripts' => ['text/plain', 'text/x-python', 'application/x-sh', 'application/octet-stream']
        ];
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes[$filetype])) {
            $errors[] = 'Invalid file type for selected category';
        }
    }
    
    if (empty($errors)) {
        // Create directory if it doesn't exist
        $uploadDir = BASE_PATH . 'babixgo.de/file-storage/' . $filetype . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate safe filename
        $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);
        $filename = $safeFilename . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            chmod($filepath, 0644);
            
            $result = $download->add([
                'filename' => $file['name'],
                'filepath' => $filetype . '/' . $filename,
                'filetype' => $filetype,
                'filesize' => $file['size'],
                'version' => $version,
                'description' => $description
            ]);
            
            if ($result['success']) {
                $success = 'Download added successfully';
            } else {
                $uploadError = 'Failed to save download metadata';
            }
        } else {
            $uploadError = 'Failed to move uploaded file';
        }
    } else {
        $uploadError = implode(', ', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Management - babixgo.de</title>
    <link rel="stylesheet" href="/shared/assets/css/style.css">
    <link rel="stylesheet" href="/shared/assets/css/admin.css">
</head>
<body>
    <nav class="main-nav">
        <div class="nav-container">
            <a href="/admin/" class="logo">babixgo.de Admin</a>
            <ul class="nav-menu">
                <li><a href="/admin/">Dashboard</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/admin/downloads.php" class="active">Downloads</a></li>
                <li><a href="/admin/comments.php">Comments</a></li>
                <li><a href="/user/">My Profile</a></li>
                <li><a href="/auth/logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <div class="container">
        <h1>Download Management</h1>
        
        <div id="message-container">
            <?php if (isset($success)): ?>
                <div class="message message-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></div>
            <?php endif; ?>
            <?php if (isset($uploadError)): ?>
                <div class="message message-error"><?= htmlspecialchars($uploadError, ENT_QUOTES) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="profile-card">
            <h2>Add New Download</h2>
            
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
                
                <div class="form-group">
                    <label for="filetype">File Type</label>
                    <select id="filetype" name="filetype" required>
                        <option value="">Select type...</option>
                        <option value="apk">APK (Android)</option>
                        <option value="exe">EXE (Windows)</option>
                        <option value="scripts">Scripts</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="file">File (Max 500MB)</label>
                    <input type="file" id="file" name="file" required>
                </div>
                
                <div class="form-group">
                    <label for="version">Version</label>
                    <input type="text" id="version" name="version" required placeholder="e.g., 1.0.0">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Download</button>
            </form>
        </div>
        
        <div class="toolbar">
            <div class="filter-buttons">
                <a href="?filter=all" class="btn btn-secondary <?= $filter === 'all' ? 'active' : '' ?>">All</a>
                <a href="?filter=apk" class="btn btn-secondary <?= $filter === 'apk' ? 'active' : '' ?>">APK</a>
                <a href="?filter=exe" class="btn btn-secondary <?= $filter === 'exe' ? 'active' : '' ?>">EXE</a>
                <a href="?filter=scripts" class="btn btn-secondary <?= $filter === 'scripts' ? 'active' : '' ?>">Scripts</a>
            </div>
        </div>
        
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Filename</th>
                        <th>Type</th>
                        <th>Version</th>
                        <th>Size</th>
                        <th>Downloads</th>
                        <th>Active</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($downloads)): ?>
                        <?php foreach ($downloads as $dl): ?>
                            <tr>
                                <td><?= $dl['id'] ?></td>
                                <td title="<?= htmlspecialchars($dl['filename'], ENT_QUOTES) ?>">
                                    <?php
                                    $filename = htmlspecialchars($dl['filename'], ENT_QUOTES);
                                    echo strlen($filename) > 30 ? substr($filename, 0, 30) . '...' : $filename;
                                    ?>
                                </td>
                                <td><span class="badge badge-<?= $dl['filetype'] ?>"><?= $dl['filetype'] ?></span></td>
                                <td><?= htmlspecialchars($dl['version'], ENT_QUOTES) ?></td>
                                <td><?= number_format($dl['filesize'] / 1024 / 1024, 2) ?> MB</td>
                                <td><?= number_format($dl['download_count']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $dl['active'] ? 'success' : 'warning' ?>">
                                        <?= $dl['active'] ? 'Yes' : 'No' ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($dl['created_at'])) ?></td>
                                <td class="actions">
                                    <a href="/admin/download-edit.php?id=<?= $dl['id'] ?>" class="btn-small btn-primary">Edit</a>
                                    <button onclick="deleteDownload(<?= $dl['id'] ?>)" class="btn-small btn-danger">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="empty-state">No downloads found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&filter=<?= $filter ?>" class="btn btn-secondary">← Previous</a>
                <?php endif; ?>
                
                <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&filter=<?= $filter ?>" class="btn btn-secondary">Next →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="/shared/assets/js/main.js"></script>
    <script src="/shared/assets/js/admin.js"></script>
    <script>
        async function deleteDownload(downloadId) {
            if (!confirm('Are you sure you want to delete this download? The file will be permanently deleted.')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('csrf_token', '<?= getCsrfToken() ?>');
            formData.append('action', 'delete_download');
            formData.append('download_id', downloadId);
            
            try {
                const response = await fetch('/admin/includes/form-handlers/admin-handlers.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message || 'Download deleted successfully', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage(result.error, 'error');
                }
            } catch (error) {
                showMessage('An error occurred. Please try again.', 'error');
            }
        }
    </script>
</body>
</html>
