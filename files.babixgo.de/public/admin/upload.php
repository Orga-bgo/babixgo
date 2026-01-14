<?php
/**
 * BabixGO Files - Upload New Download
 */

require_once __DIR__ . '/../init.php';

initSession();
requireAdmin();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken. Bitte lade die Seite neu.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $fileSize = trim($_POST['file_size'] ?? '');
        $fileType = trim($_POST['file_type'] ?? '');
        $downloadLink = trim($_POST['download_link'] ?? '');
        $alternativeLink = trim($_POST['alternative_link'] ?? '');
        
        // Validation
        if (empty($name)) {
            $error = 'Name ist erforderlich.';
        } elseif (empty($downloadLink)) {
            $error = 'Download-Link ist erforderlich.';
        } elseif (!filter_var($downloadLink, FILTER_VALIDATE_URL)) {
            $error = 'Ungültiger Download-Link.';
        } elseif (!empty($alternativeLink) && !filter_var($alternativeLink, FILTER_VALIDATE_URL)) {
            $error = 'Ungültiger alternativer Link.';
        } else {
            $downloadId = createDownload([
                'name' => $name,
                'description' => $description,
                'file_size' => $fileSize,
                'file_type' => $fileType,
                'download_link' => $downloadLink,
                'alternative_link' => $alternativeLink,
                'created_by' => getCurrentUserId()
            ]);
            
            if ($downloadId) {
                header('Location: /admin/manage-downloads.php?success=' . urlencode('Download erfolgreich erstellt!'));
                exit;
            } else {
                $error = 'Fehler beim Erstellen des Downloads.';
            }
        }
    }
}

$pageTitle = 'Neuer Download';
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
        <div class="container">
            <div class="content-card" style="max-width: 600px;">
                <h2><span class="icon-emoji">➕</span> Neuer Download</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label for="name" class="form-label">Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input" 
                            placeholder="z.B. BabixGO App v2.0"
                            required
                            value="<?php echo e($_POST['name'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Beschreibung</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-textarea" 
                            placeholder="Beschreibe den Download..."
                            rows="4"
                        ><?php echo e($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="file_type" class="form-label">Dateityp</label>
                        <select id="file_type" name="file_type" class="form-select">
                            <option value="">-- Auswählen --</option>
                            <option value="APK" <?php echo ($_POST['file_type'] ?? '') === 'APK' ? 'selected' : ''; ?>>APK (Android)</option>
                            <option value="ZIP" <?php echo ($_POST['file_type'] ?? '') === 'ZIP' ? 'selected' : ''; ?>>ZIP</option>
                            <option value="PDF" <?php echo ($_POST['file_type'] ?? '') === 'PDF' ? 'selected' : ''; ?>>PDF</option>
                            <option value="EXE" <?php echo ($_POST['file_type'] ?? '') === 'EXE' ? 'selected' : ''; ?>>EXE (Windows)</option>
                            <option value="DMG" <?php echo ($_POST['file_type'] ?? '') === 'DMG' ? 'selected' : ''; ?>>DMG (macOS)</option>
                            <option value="TAR" <?php echo ($_POST['file_type'] ?? '') === 'TAR' ? 'selected' : ''; ?>>TAR</option>
                            <option value="7Z" <?php echo ($_POST['file_type'] ?? '') === '7Z' ? 'selected' : ''; ?>>7Z</option>
                            <option value="RAR" <?php echo ($_POST['file_type'] ?? '') === 'RAR' ? 'selected' : ''; ?>>RAR</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="file_size" class="form-label">Dateigröße</label>
                        <input 
                            type="text" 
                            id="file_size" 
                            name="file_size" 
                            class="form-input" 
                            placeholder="z.B. 15.5 MB"
                            value="<?php echo e($_POST['file_size'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="download_link" class="form-label">Download-Link *</label>
                        <input 
                            type="url" 
                            id="download_link" 
                            name="download_link" 
                            class="form-input" 
                            placeholder="https://..."
                            required
                            value="<?php echo e($_POST['download_link'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="alternative_link" class="form-label">Alternativer Link (optional)</label>
                        <input 
                            type="url" 
                            id="alternative_link" 
                            name="alternative_link" 
                            class="form-input" 
                            placeholder="https://..."
                            value="<?php echo e($_POST['alternative_link'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            ✅ Download erstellen
                        </button>
                        <a href="/admin/manage-downloads.php" class="btn btn-secondary">
                            Abbrechen
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

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
</body>
</html>
