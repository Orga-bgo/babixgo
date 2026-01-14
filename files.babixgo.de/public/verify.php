<?php
/**
 * BabixGO Files - Email Verification Page
 */

require_once __DIR__ . '/init.php';

initSession();

$token = $_GET['token'] ?? '';
$success = false;
$message = '';

if (empty($token)) {
    $message = 'Kein Verifizierungs-Token angegeben.';
} else {
    $success = verifyEmail($token);
    
    if ($success) {
        header('Location: /login.php?verified=1');
        exit;
    } else {
        $message = 'Der Verifizierungslink ist ungültig oder bereits verwendet worden.';
    }
}

$pageTitle = 'E-Mail Verifizierung';
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
    <main class="main-content auth-container">
        <div class="container">
            <div class="auth-card content-card">
                <div class="auth-header">
                    <h1>📧 E-Mail Verifizierung</h1>
                </div>
                
                <div class="alert alert-error">
                    <?php echo e($message); ?>
                </div>
                
                <div class="auth-footer">
                    <p>
                        <a href="/login.php">Zur Anmeldung</a> | 
                        <a href="/register.php">Neu registrieren</a>
                    </p>
                </div>
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
