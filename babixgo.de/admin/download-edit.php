<?php
/**
 * Download Edit Page
 */

require_once __DIR__ . '/includes/admin-check.php';

$downloadId = intval($_GET['id'] ?? 0);

if (!$downloadId) {
    header('Location: /admin/downloads.php');
    exit;
}

$download = new Download();
$downloadData = $download->getById($downloadId);

if (!$downloadData) {
    header('Location: /admin/downloads.php');
    exit;
}

$db = Database::getInstance();

// Get all categories for the dropdown
$categories = $download->getAllCategories();

// Get download logs
$logs = $download->getLogs($downloadId, 20);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $categoryId = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    
    // Validate alternative_link
    $alternativeLink = trim($_POST['alternative_link'] ?? '');
    if (!empty($alternativeLink)) {
        if (!filter_var($alternativeLink, FILTER_VALIDATE_URL)) {
            $error = 'Alternativer Link ist keine gültige URL';
            $alternativeLink = null;
        }
    } else {
        $alternativeLink = null;
    }

    if (!isset($error)) {
        $data = [
            'filename' => trim($_POST['filename'] ?? ''),
            'filetype' => $_POST['filetype'] ?? '',
            'filesize' => $downloadData['filesize'], // Keep original filesize
            'version' => trim($_POST['version'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'category_id' => $categoryId,
            'alternative_link' => $alternativeLink,
            'active' => isset($_POST['active']) ? 1 : 0
        ];

        $result = $download->update($downloadId, $data);

        if ($result['success']) {
            $success = 'Download updated successfully';
            $downloadData = $download->getById($downloadId);
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Download - babixgo.de</title>
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
        <h1>Edit Download</h1>
        
        <?php if (isset($success)): ?>
            <div class="message message-success"><?= htmlspecialchars($success, ENT_QUOTES) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES) ?></div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <div class="profile-card">
                <h2>Download Information</h2>
                
                <div class="info-row">
                    <label>Download ID:</label>
                    <span><?= $downloadData['id'] ?></span>
                </div>
                
                <div class="info-row">
                    <label>File Path:</label>
                    <span><?= htmlspecialchars($downloadData['filepath'], ENT_QUOTES) ?></span>
                </div>
                
                <div class="info-row">
                    <label>File Size:</label>
                    <span><?= number_format($downloadData['filesize'] / 1024 / 1024, 2) ?> MB</span>
                </div>
                
                <div class="info-row">
                    <label>Total Downloads:</label>
                    <span><?= number_format($downloadData['download_count']) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Created:</label>
                    <span><?= date('F j, Y g:i A', strtotime($downloadData['created_at'])) ?></span>
                </div>
                
                <div class="info-row">
                    <label>Last Updated:</label>
                    <span><?= date('F j, Y g:i A', strtotime($downloadData['updated_at'])) ?></span>
                </div>
            </div>
            
            <div class="profile-card">
                <h2>Edit Download</h2>
                
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">

                    <div class="form-group">
                        <label for="filename">Filename</label>
                        <input
                            type="text"
                            id="filename"
                            name="filename"
                            value="<?= htmlspecialchars($downloadData['filename'], ENT_QUOTES) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="filetype">File Type</label>
                        <select id="filetype" name="filetype" required>
                            <option value="apk" <?= $downloadData['filetype'] === 'apk' ? 'selected' : '' ?>>APK</option>
                            <option value="exe" <?= $downloadData['filetype'] === 'exe' ? 'selected' : '' ?>>EXE</option>
                            <option value="scripts" <?= $downloadData['filetype'] === 'scripts' ? 'selected' : '' ?>>Scripts</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id">
                            <option value="">-- No Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $downloadData['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color: #666; display: block; margin-top: 4px;">Optional: Assign this download to a category for better organization</small>
                    </div>

                    <div class="form-group">
                        <label for="version">Version</label>
                        <input
                            type="text"
                            id="version"
                            name="version"
                            value="<?= htmlspecialchars($downloadData['version'], ENT_QUOTES) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                        ><?= htmlspecialchars($downloadData['description'] ?? '', ENT_QUOTES) ?></textarea>
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
                            value="<?= htmlspecialchars($downloadData['alternative_link'] ?? '', ENT_QUOTES) ?>"
                        >
                        <small style="color: #666; display: block; margin-top: 4px;">
                            Link zum PlayStore, Website der App oder externer Download-Quelle
                        </small>
                    </div>

                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="active" value="1" <?= $downloadData['active'] ? 'checked' : '' ?>>
                            Active (visible to users)
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="/admin/downloads.php" class="btn btn-secondary">Back to Downloads</a>
                </form>
            </div>
        </div>
        
        <div class="profile-card">
            <h2>Download Logs (Last 20)</h2>
            
            <?php if (!empty($logs)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Downloaded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= $log['username'] ? htmlspecialchars($log['username'], ENT_QUOTES) : '<em>Anonymous</em>' ?></td>
                                <td><?= htmlspecialchars($log['ip_address'], ENT_QUOTES) ?></td>
                                <td title="<?= htmlspecialchars($log['user_agent'], ENT_QUOTES) ?>">
                                    <?php
                                    $ua = htmlspecialchars($log['user_agent'], ENT_QUOTES);
                                    echo strlen($ua) > 50 ? substr($ua, 0, 50) . '...' : $ua;
                                    ?>
                                </td>
                                <td><?= date('M j, Y g:i A', strtotime($log['downloaded_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">No download logs yet</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
