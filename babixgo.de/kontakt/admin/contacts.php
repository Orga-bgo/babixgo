<?php
/**
 * Admin Panel für Kontaktanfragen
 * babixGO - admin/contacts.php
 * Admin-UI (separat, ohne öffentliche Partials)
 */

session_start();

// Load security utilities
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/csrf.php';
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/brute-force-protection.php';

$dbHost = getenv('BABIXGO_DB_HOST');
$dbName = getenv('BABIXGO_DB_NAME');
$dbUser = getenv('BABIXGO_DB_USER');
$dbPass = getenv('BABIXGO_DB_PASS');

if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {
    http_response_code(500);
    echo 'Serverkonfiguration fehlt.';
    exit;
}

// Login prüfen
if (!isset($_SESSION['admin_logged_in'])) {
    // Login-Formular verarbeiten
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        // CSRF validation
        if (!isset($_POST['csrf_token']) || !csrf_validate_token($_POST['csrf_token'])) {
            $error = 'Ungültige Anfrage. Bitte versuche es erneut.';
        } else {
            $identifier = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            // Check rate limiting
            $rateCheck = login_check_rate_limit($identifier);
            if (!$rateCheck['allowed']) {
                $error = $rateCheck['message'];
            } else {
                try {
                    $pdo = new PDO(
                        "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8mb4",
                        $dbUser,
                        $dbPass
                    );
                    
                    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
                    $stmt->execute([$_POST['username']]);
                    $user = $stmt->fetch();
                    
                    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
                        // Clear failed attempts on successful login
                        login_clear_attempts($identifier);
                        
                        $_SESSION['admin_logged_in'] = true;
                        $_SESSION['admin_id'] = $user['id'];
                        $_SESSION['admin_username'] = $user['username'];
                        
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);
                        
                        header('Location: contacts.php');
                        exit;
                    } else {
                        // Record failed attempt
                        $failResult = login_record_failed_attempt($identifier);
                        
                        if ($failResult['locked']) {
                            $minutes = ceil(($failResult['locked_until'] - time()) / 60);
                            $error = "Zu viele fehlgeschlagene Anmeldeversuche. Konto gesperrt für {$minutes} Minute(n).";
                        } else {
                            $remaining = $failResult['remaining'];
                            $error = "Ungültige Anmeldedaten. Noch {$remaining} Versuch(e) übrig.";
                        }
                    }
                } catch (PDOException $e) {
                    $error = 'Datenbankfehler';
                }
            }
        }
    }
    
    // Login-Seite anzeigen
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-meta.php'; ?>
        <title>Admin Login - babixGO</title>
        <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-links.php'; ?>
    </head>
    <body class="admin-body admin-login">
        <div class="admin-login-box">
            <h1 class="admin-login-title"><span class="logo-babix">babix</span><span class="logo-go">GO</span> Admin</h1>
            <?php if (isset($error)): ?>
                <div class="admin-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="admin-form-group">
                    <label class="admin-label">Username</label>
                    <input type="text" name="username" class="admin-input" required autofocus>
                </div>
                <div class="admin-form-group">
                    <label class="admin-label">Passwort</label>
                    <input type="password" name="password" class="admin-input" required>
                </div>
                <button type="submit" class="admin-button">Anmelden</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// User ist eingeloggt - Datenbank verbinden
try {
    $pdo = new PDO(
        "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Datenbankverbindung fehlgeschlagen');
}

// Status-Update verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !csrf_validate_token($_POST['csrf_token'])) {
        die('Ungültige Anfrage. Bitte lade die Seite neu.');
    }
    
    $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['id']]);
    header('Location: contacts.php?filter=' . ($_GET['filter'] ?? 'all'));
    exit;
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$sql = "SELECT * FROM contacts";
if ($filter !== 'all') {
    $sql .= " WHERE status = :status";
}
$sql .= " ORDER BY created_at DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
if ($filter !== 'all') {
    $stmt->execute([':status' => $filter]);
} else {
    $stmt->execute();
}
$contacts = $stmt->fetchAll();

// Statistiken
$counts = [
    'all' => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
    'new' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn(),
    'read' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='read'")->fetchColumn(),
    'replied' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='replied'")->fetchColumn(),
    'archived' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='archived'")->fetchColumn(),
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-meta.php'; ?>
    <title>Kontaktanfragen - babixGO Admin</title>
    <?php require dirname($_SERVER['DOCUMENT_ROOT']) . '/shared/partials/head-links.php'; ?>
</head>
<body class="admin-body admin-contacts">
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title"><span class="emoji" role="img" aria-label="Brief">📬</span>Kontaktanfragen</h1>
            <div>
                <span class="admin-user-meta">
                    Eingeloggt als: <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
                </span>
                <a href="logout.php" class="admin-logout">Abmelden</a>
            </div>
        </div>

        <div class="admin-filters">
            <a href="?filter=all" class="admin-filter-btn <?= $filter === 'all' ? 'active' : '' ?>">
                Alle <span class="admin-badge"><?= $counts['all'] ?></span>
            </a>
            <a href="?filter=new" class="admin-filter-btn <?= $filter === 'new' ? 'active' : '' ?>">
                Neu <span class="admin-badge"><?= $counts['new'] ?></span>
            </a>
            <a href="?filter=read" class="admin-filter-btn <?= $filter === 'read' ? 'active' : '' ?>">
                Gelesen <span class="admin-badge"><?= $counts['read'] ?></span>
            </a>
            <a href="?filter=replied" class="admin-filter-btn <?= $filter === 'replied' ? 'active' : '' ?>">
                Beantwortet <span class="admin-badge"><?= $counts['replied'] ?></span>
            </a>
            <a href="?filter=archived" class="admin-filter-btn <?= $filter === 'archived' ? 'active' : '' ?>">
                Archiviert <span class="admin-badge"><?= $counts['archived'] ?></span>
            </a>
        </div>

        <?php if (empty($contacts)): ?>
            <div class="admin-empty">
                <p class="admin-empty-icon"><span class="emoji" role="img" aria-label="Leerer Briefkasten">📭</span></p>
                <p class="admin-empty-text">Keine Kontaktanfragen gefunden.</p>
            </div>
        <?php else: ?>
            <?php foreach ($contacts as $contact): ?>
                <div class="admin-contact-card status-<?= $contact['status'] ?>">
                    <div class="admin-contact-header">
                        <div>
                            <div class="admin-contact-name"><?= htmlspecialchars($contact['name']) ?></div>
                            <div class="admin-contact-date">
                                <?= date('d.m.Y', strtotime($contact['created_at'])) ?> um 
                                <?= date('H:i', strtotime($contact['created_at'])) ?> Uhr
                            </div>
                        </div>
                    </div>

                    <div class="admin-contact-info">
                        <div>
                            <strong><span class="emoji" role="img" aria-label="E-Mail">📧</span>E-Mail:</strong><br>
                            <a href="mailto:<?= htmlspecialchars($contact['email']) ?>">
                                <?= htmlspecialchars($contact['email']) ?>
                            </a>
                        </div>
                        <?php if ($contact['whatsapp']): ?>
                            <div>
                                <strong><span class="emoji" role="img" aria-label="Sprechblase">💬</span>WhatsApp:</strong><br>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contact['whatsapp']) ?>" 
                                   target="_blank" rel="noopener noreferrer" class="link-whatsapp">
                                    <?= htmlspecialchars($contact['whatsapp']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong><span class="emoji" role="img" aria-label="Weltkugel">🌐</span>IP:</strong><br>
                            <?= htmlspecialchars($contact['ip_address']) ?>
                        </div>
                    </div>

                    <div class="admin-contact-message">
                        <strong><span class="emoji" role="img" aria-label="Sprechblase">💬</span>Nachricht:</strong><br><br>
                        <?= nl2br(htmlspecialchars($contact['message'])) ?>
                    </div>

                    <form method="POST" class="admin-contact-actions">
                        <?php csrf_field(); ?>
                        <input type="hidden" name="id" value="<?= $contact['id'] ?>">
                        <select name="status" class="admin-select">
                            <option value="new" <?= $contact['status'] === 'new' ? 'selected' : '' ?>>Neu</option>
                            <option value="read" <?= $contact['status'] === 'read' ? 'selected' : '' ?>>Gelesen</option>
                            <option value="replied" <?= $contact['status'] === 'replied' ? 'selected' : '' ?>>Beantwortet</option>
                            <option value="archived" <?= $contact['status'] === 'archived' ? 'selected' : '' ?>>Archiviert</option>
                        </select>
                        <button type="submit" name="update_status" class="admin-action-button">Status aktualisieren</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
