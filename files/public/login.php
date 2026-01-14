<?php
/**
 * BabixGO Files - Login Page
 */

require_once __DIR__ . '/init.php';

initSession();

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';
$redirect = $_GET['redirect'] ?? '/';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken. Bitte lade die Seite neu.';
    } elseif (empty($username) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } else {
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            $redirect = filter_var($redirect, FILTER_SANITIZE_URL);
            // Prevent open redirect
            if (strpos($redirect, '/') !== 0) {
                $redirect = '/';
            }
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Anmelden';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Anmelden bei BabixGO Files">
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
                    <h1>👋 Willkommen zurück!</h1>
                    <p class="text-muted">Melde dich an, um Downloads zu starten.</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo e($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['verified'])): ?>
                    <div class="alert alert-success">
                        E-Mail erfolgreich bestätigt! Du kannst dich jetzt anmelden.
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        Registrierung erfolgreich! Bitte bestätige deine E-Mail-Adresse.
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username oder E-Mail</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            placeholder="Dein Username oder E-Mail"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Passwort</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Dein Passwort"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            🔐 Anmelden
                        </button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    <p>Noch kein Konto? <a href="/register.php">Jetzt registrieren</a></p>
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
