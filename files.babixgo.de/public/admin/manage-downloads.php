<?php
/**
 * BabixGO Files - Manage Downloads
 */

require_once __DIR__ . '/../init.php';

initSession();
requireAdmin();

$error = '';
$success = $_GET['success'] ?? '';

// Handle delete action
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $downloadId = filter_input(INPUT_POST, 'download_id', FILTER_VALIDATE_INT);
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken.';
    } elseif (!$downloadId) {
        $error = 'Ungültige Download-ID.';
    } else {
        if (deleteDownload($downloadId)) {
            $success = 'Download erfolgreich gelöscht.';
        } else {
            $error = 'Fehler beim Löschen des Downloads.';
        }
    }
}

// Handle edit action
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $downloadId = filter_input(INPUT_POST, 'download_id', FILTER_VALIDATE_INT);
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken.';
    } elseif (!$downloadId) {
        $error = 'Ungültige Download-ID.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $fileSize = trim($_POST['file_size'] ?? '');
        $fileType = trim($_POST['file_type'] ?? '');
        $downloadLink = trim($_POST['download_link'] ?? '');
        $alternativeLink = trim($_POST['alternative_link'] ?? '');
        
        if (empty($name) || empty($downloadLink)) {
            $error = 'Name und Download-Link sind erforderlich.';
        } else {
            $updated = updateDownload($downloadId, [
                'name' => $name,
                'description' => $description,
                'file_size' => $fileSize,
                'file_type' => $fileType,
                'download_link' => $downloadLink,
                'alternative_link' => $alternativeLink
            ]);
            
            if ($updated) {
                $success = 'Download erfolgreich aktualisiert.';
            } else {
                $error = 'Keine Änderungen vorgenommen oder Fehler beim Aktualisieren.';
            }
        }
    }
}

$downloads = getDownloads();
$pageTitle = 'Downloads verwalten';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#A0D8FA">
    <meta name="robots" content="noindex, nofollow">
    
    <title><?php echo e($pageTitle); ?> - <?php echo e(SITE_NAME); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/header-simple.css">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css">
    
    <!-- Google Analytics Tracking Configuration -->
    <?php include __DIR__ . '/../../includes/tracking.php'; ?>
</head>
<body>
    <?php include __DIR__ . "/../../includes/header.php"; ?>
    <!-- Header -->

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-wide">
            <div class="flex-between mb-2">
                <h2><span class="icon-emoji">📥</span> Downloads verwalten</h2>
                <a href="/admin/upload.php" class="btn btn-primary">➕ Neuer Download</a>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" data-dismiss="5000"><?php echo e($success); ?></div>
            <?php endif; ?>
            
            <?php if (empty($downloads)): ?>
                <div class="empty-state content-card">
                    <div class="empty-state-icon">📭</div>
                    <h3>Keine Downloads vorhanden</h3>
                    <p>Erstelle deinen ersten Download-Eintrag.</p>
                    <a href="/admin/upload.php" class="btn btn-primary mt-2">➕ Neuer Download</a>
                </div>
            <?php else: ?>
                <div class="content-card">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Typ</th>
                                    <th>Größe</th>
                                    <th>Downloads</th>
                                    <th>Erstellt</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($downloads as $download): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo e($download['name']); ?></strong>
                                            <?php if ($download['creator_name']): ?>
                                                <br><small class="text-muted">von <?php echo e($download['creator_name']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($download['file_type'] ?: '-'); ?></td>
                                        <td><?php echo e($download['file_size'] ?: '-'); ?></td>
                                        <td><?php echo e($download['download_count']); ?></td>
                                        <td><?php echo formatDate($download['created_at']); ?></td>
                                        <td class="actions">
                                            <button 
                                                class="btn btn-secondary btn-sm" 
                                                data-download='<?php echo htmlspecialchars(json_encode([
                                                    'id' => $download['id'],
                                                    'name' => $download['name'],
                                                    'description' => $download['description'] ?? '',
                                                    'file_type' => $download['file_type'],
                                                    'file_size' => $download['file_size'],
                                                    'download_link' => $download['download_link'],
                                                    'alternative_link' => $download['alternative_link'] ?? ''
                                                ]), ENT_QUOTES, 'UTF-8'); ?>'
                                                onclick="openEditModalFromData(this)"
                                            >
                                                ✏️ Bearbeiten
                                            </button>
                                            <form method="post" action="" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="download_id" value="<?php echo e($download['id']); ?>">
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    data-confirm="Möchtest du diesen Download wirklich löschen?"
                                                >
                                                    🗑️ Löschen
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 2000; overflow: auto;">
        <div style="max-width: 600px; margin: 50px auto; padding: 20px;">
            <div class="content-card">
                <h3>Download bearbeiten</h3>
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="download_id" id="edit_download_id">
                    
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Name *</label>
                        <input type="text" id="edit_name" name="name" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description" class="form-label">Beschreibung</label>
                        <textarea id="edit_description" name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_file_type" class="form-label">Dateityp</label>
                        <input type="text" id="edit_file_type" name="file_type" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_file_size" class="form-label">Dateigröße</label>
                        <input type="text" id="edit_file_size" name="file_size" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_download_link" class="form-label">Download-Link *</label>
                        <input type="url" id="edit_download_link" name="download_link" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_alternative_link" class="form-label">Alternativer Link</label>
                        <input type="url" id="edit_alternative_link" name="alternative_link" class="form-input">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Speichern</button>
                        <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-inner">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(SITE_NAME); ?>. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <!-- Cookie Consent Banner -->
    <?php include __DIR__ . '/../../includes/cookie-banner.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/header.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/cookie-consent.js"></script>
    <script>
        function openEditModalFromData(button) {
            const data = JSON.parse(button.dataset.download);
            document.getElementById('edit_download_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_description').value = data.description;
            document.getElementById('edit_file_type').value = data.file_type;
            document.getElementById('edit_file_size').value = data.file_size;
            document.getElementById('edit_download_link').value = data.download_link;
            document.getElementById('edit_alternative_link').value = data.alternative_link;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal on outside click
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
</body>
</html>
