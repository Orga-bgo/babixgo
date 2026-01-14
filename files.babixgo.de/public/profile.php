<?php
/**
 * BabixGO Files - User Profile Page
 */

require_once __DIR__ . '/init.php';

initSession();
requireLogin();

$user = getCurrentUser();
$error = '';
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken. Bitte lade die Seite neu.';
    } else {
        $description = trim($_POST['description'] ?? '');
        
        $updated = updateUserProfile($user['id'], [
            'description' => $description
        ]);
        
        if ($updated) {
            $success = 'Profil erfolgreich aktualisiert!';
            $user = getCurrentUser(); // Refresh user data
        } else {
            $error = 'Keine Änderungen vorgenommen.';
        }
    }
}

$pageTitle = 'Mein Profil';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#A0D8FA">
    
    <title><?php echo e($pageTitle); ?> - <?php echo e(SITE_NAME); ?></title>
    
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icons/icon-192.png">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/header-simple.css">
    <link rel="stylesheet" href="/assets/css/cookie-banner.css">
    
    <!-- Google Analytics Tracking Configuration -->
    <?php include __DIR__ . '/../includes/tracking.php'; ?>
</head>
<body>
    <?php include __DIR__ . "/../includes/header.php"; ?>
    <!-- Header -->

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="profile-card content-card">
                <h2><span class="icon-emoji">👤</span> Mein Profil</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" data-dismiss="5000"><?php echo e($success); ?></div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <div class="info-row">
                        <span class="info-label">Username</span>
                        <span class="info-value"><?php echo e($user['username']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">E-Mail</span>
                        <span class="info-value"><?php echo e($user['email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Rolle</span>
                        <span class="info-value">
                            <span class="badge badge-<?php echo $user['role']; ?>">
                                <?php echo e(ucfirst($user['role'])); ?>
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Kommentare</span>
                        <span class="info-value"><?php echo e($user['comment_count']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Registriert seit</span>
                        <span class="info-value"><?php echo formatDate($user['created_at']); ?></span>
                    </div>
                </div>
                
                <h3>Profil bearbeiten</h3>
                
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Beschreibung / Bio</label>
                        <textarea 
                            id="description" 
                            name="description" 
                            class="form-textarea" 
                            placeholder="Erzähle etwas über dich..."
                            rows="4"
                        ><?php echo e($user['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            💾 Speichern
                        </button>
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
    <?php include __DIR__ . '/../includes/cookie-banner.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/header.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/cookie-consent.js"></script>
</body>
</html>
