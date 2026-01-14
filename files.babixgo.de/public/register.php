<?php
/**
 * BabixGO Files - Registration Page
 */

require_once __DIR__ . '/init.php';

initSession();

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: /');
    exit;
}

$error = '';
$success = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken. Bitte lade die Seite neu.';
    } elseif (empty($username) || empty($email) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Die Passwörter stimmen nicht überein.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username darf nur Buchstaben, Zahlen und Unterstriche enthalten.';
    } else {
        $result = registerUser($username, $email, $password);
        
        if ($result['success']) {
            header('Location: /login.php?registered=1');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Registrieren';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Registrieren bei BabixGO Files">
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
                    <h1>🚀 Jetzt registrieren</h1>
                    <p class="text-muted">Erstelle ein Konto, um Downloads und Kommentare zu nutzen.</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <?php echo e($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="" data-validate="register">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            placeholder="Dein Username"
                            autocomplete="username"
                            pattern="[a-zA-Z0-9_]+"
                            minlength="3"
                            maxlength="50"
                            required
                            autofocus
                            value="<?php echo e($_POST['username'] ?? ''); ?>"
                        >
                        <p class="form-hint">3-50 Zeichen, nur Buchstaben, Zahlen und Unterstriche.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">E-Mail</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="deine@email.de"
                            autocomplete="email"
                            required
                            value="<?php echo e($_POST['email'] ?? ''); ?>"
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
                            autocomplete="new-password"
                            minlength="<?php echo PASSWORD_MIN_LENGTH; ?>"
                            required
                            data-strength="#password-strength"
                        >
                        <p class="form-hint">
                            Mindestens <?php echo PASSWORD_MIN_LENGTH; ?> Zeichen.
                            <span id="password-strength"></span>
                        </p>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Passwort bestätigen</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-input" 
                            placeholder="Passwort wiederholen"
                            autocomplete="new-password"
                            required
                        >
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            ✨ Registrieren
                        </button>
                    </div>
                </form>
                
                <div class="auth-footer">
                    <p>Bereits ein Konto? <a href="/login.php">Jetzt anmelden</a></p>
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
