<?php
/**
 * Download Management Page
 */

require_once __DIR__ . '/includes/admin-check.php';

$db = Database::getInstance();
$download = new Download();

// Get all categories for the dropdown
$categories = $download->getAllCategories();

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
    $categoryId = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $version = trim($_POST['version'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $alternativeLink = trim($_POST['alternative_link'] ?? '');

    $errors = [];

    if (!in_array($filetype, ['apk', 'scripts', 'exe'])) {
        $errors[] = 'Invalid file type';
    }

    if (empty($version)) {
        $errors[] = 'Version is required';
    }
    
    // Validate alternative_link
    if (!empty($alternativeLink)) {
        if (!filter_var($alternativeLink, FILTER_VALIDATE_URL)) {
            $errors[] = 'Alternativer Link ist keine gültige URL';
        }
    } else {
        $alternativeLink = null;
    }
    
    // Validate file upload
    $file = $_FILES['file'];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE in form',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        $errorMsg = $errorMessages[$file['error']] ?? 'Unknown upload error';
        error_log("Upload error: " . $errorMsg . " (Code: " . $file['error'] . ")");
        $errors[] = 'File upload failed: ' . $errorMsg;
    } else {
        // Check file size (max 500MB)
        if ($file['size'] > 500 * 1024 * 1024) {
            error_log("Upload error: File too large - " . ($file['size'] / 1024 / 1024) . " MB");
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
            error_log("Upload error: Invalid MIME type '$mimeType' for filetype '$filetype'");
            $errors[] = 'Invalid file type for selected category';
        }
    }
    
    if (empty($errors)) {
        // Create directory if it doesn't exist
        $uploadDir = BASE_PATH . 'file-storage/' . $filetype . '/';
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                error_log("Failed to create upload directory: " . $uploadDir);
                $uploadError = 'Failed to create upload directory';
            }
        }

        if (!isset($uploadError)) {
            // Check if directory is writable
            if (!is_writable($uploadDir)) {
                error_log("Upload directory not writable: " . $uploadDir);
                $uploadError = 'Upload directory not writable';
            } else {
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
                        'description' => $description,
                        'category_id' => $categoryId,
                        'alternative_link' => $alternativeLink
                    ]);

                    if ($result['success']) {
                        $success = 'Download added successfully';
                        error_log("Upload success: " . $file['name'] . " (ID: " . $result['id'] . ", Category: " . ($categoryId ?? 'none') . ")");
                    } else {
                        error_log("Failed to save download metadata: " . ($result['error'] ?? 'unknown error'));
                        $uploadError = 'Failed to save download metadata: ' . ($result['error'] ?? 'unknown error');
                        // Clean up uploaded file since DB insert failed
                        if (file_exists($filepath)) {
                            unlink($filepath);
                        }
                    }
                } else {
                    error_log("Failed to move uploaded file from " . $file['tmp_name'] . " to " . $filepath);
                    $uploadError = 'Failed to move uploaded file';
                }
            }
        }
    } else {
        $uploadError = implode(', ', $errors);
        error_log("Upload validation errors: " . $uploadError);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Management - babixgo.de</title>
    <link rel="stylesheet" href="/assets/css/style.css">
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

            <form id="upload-form" method="POST" enctype="multipart/form-data">
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
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">-- No Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name'], ENT_QUOTES) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-help">Optional: Assign this download to a category for better organization</small>
                </div>

                <div class="form-group">
                    <label for="file">File (Max 500MB)</label>
                    <input type="file" id="file" name="file" required>
                    <small id="file-size-info" class="form-help"></small>
                </div>

                <div class="form-group">
                    <label for="version">Version</label>
                    <input type="text" id="version" name="version" required placeholder="e.g., 1.0.0">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="alternative_link">
                        Alternativer Link
                        <span style="color: #666; font-weight: normal;">(Optional)</span>
                    </label>
                    <input
                        type="url"
                        id="alternative_link"
                        name="alternative_link"
                        placeholder="https://play.google.com/store/apps/..."
                    >
                    <small class="form-help">Link zum PlayStore, Website der App oder externer Download-Quelle</small>
                </div>

                <button type="submit" id="upload-btn" class="btn btn-primary">Upload Download</button>

                <!-- Upload Progress Bar -->
                <div id="upload-progress-container" class="upload-progress-container" style="display: none;">
                    <div class="upload-progress-info">
                        <span id="upload-status">Uploading...</span>
                        <span id="upload-percentage">0%</span>
                    </div>
                    <div class="upload-progress-bar">
                        <div id="upload-progress-fill" class="upload-progress-fill"></div>
                    </div>
                    <div class="upload-progress-details">
                        <span id="upload-speed">0 MB/s</span>
                        <span id="upload-eta">Calculating...</span>
                    </div>
                </div>
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
        // File size display
        document.getElementById('file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const sizeInfo = document.getElementById('file-size-info');

            if (file) {
                const sizeMB = (file.size / 1024 / 1024).toFixed(2);
                sizeInfo.textContent = `Selected: ${file.name} (${sizeMB} MB)`;

                if (file.size > 500 * 1024 * 1024) {
                    sizeInfo.style.color = 'var(--error)';
                    sizeInfo.textContent += ' - File too large!';
                } else {
                    sizeInfo.style.color = 'var(--muted)';
                }
            } else {
                sizeInfo.textContent = '';
            }
        });

        // Upload with progress tracking
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const uploadBtn = document.getElementById('upload-btn');
            const progressContainer = document.getElementById('upload-progress-container');
            const progressFill = document.getElementById('upload-progress-fill');
            const progressPercentage = document.getElementById('upload-percentage');
            const uploadStatus = document.getElementById('upload-status');
            const uploadSpeed = document.getElementById('upload-speed');
            const uploadEta = document.getElementById('upload-eta');

            // Disable form and show progress
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            progressContainer.style.display = 'block';

            // Track upload progress
            let startTime = Date.now();
            let lastLoaded = 0;
            let lastTime = startTime;

            const xhr = new XMLHttpRequest();

            // Upload progress event
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    const currentTime = Date.now();
                    const timeDiff = (currentTime - lastTime) / 1000; // seconds
                    const loadedDiff = e.loaded - lastLoaded;

                    // Update progress bar
                    progressFill.style.width = percentComplete + '%';
                    progressPercentage.textContent = Math.round(percentComplete) + '%';

                    // Calculate speed (MB/s)
                    if (timeDiff > 0) {
                        const speedMBps = (loadedDiff / timeDiff / 1024 / 1024).toFixed(2);
                        uploadSpeed.textContent = speedMBps + ' MB/s';

                        // Calculate ETA
                        const remaining = e.total - e.loaded;
                        const etaSeconds = remaining / (loadedDiff / timeDiff);

                        if (etaSeconds < 60) {
                            uploadEta.textContent = 'ETA: ' + Math.round(etaSeconds) + 's';
                        } else {
                            const minutes = Math.floor(etaSeconds / 60);
                            const seconds = Math.round(etaSeconds % 60);
                            uploadEta.textContent = `ETA: ${minutes}m ${seconds}s`;
                        }
                    }

                    lastLoaded = e.loaded;
                    lastTime = currentTime;
                }
            });

            // Upload complete event
            xhr.addEventListener('load', function() {
                if (xhr.status === 200) {
                    uploadStatus.textContent = 'Processing...';
                    progressFill.style.width = '100%';
                    progressPercentage.textContent = '100%';
                    uploadSpeed.textContent = 'Complete';
                    uploadEta.textContent = '';

                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    uploadStatus.textContent = 'Upload failed!';
                    uploadStatus.style.color = 'var(--error)';
                    uploadBtn.disabled = false;
                    uploadBtn.textContent = 'Upload Download';

                    showMessage('Upload failed. Please try again.', 'error');
                }
            });

            // Upload error event
            xhr.addEventListener('error', function() {
                uploadStatus.textContent = 'Upload failed!';
                uploadStatus.style.color = 'var(--error)';
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Download';

                showMessage('Network error. Please try again.', 'error');
            });

            // Upload abort event
            xhr.addEventListener('abort', function() {
                uploadStatus.textContent = 'Upload cancelled';
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload Download';
                progressContainer.style.display = 'none';
            });

            // Send request
            xhr.open('POST', window.location.href, true);
            xhr.send(formData);
        });

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
