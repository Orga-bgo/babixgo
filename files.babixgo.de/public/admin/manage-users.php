<?php
/**
 * BabixGO Files - Manage Users
 */

require_once __DIR__ . '/../init.php';

initSession();
requireAdmin();

$error = '';
$success = '';

// Handle role change
if (isset($_POST['action']) && $_POST['action'] === 'change_role') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $newRole = $_POST['role'] ?? '';
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken.';
    } elseif (!$userId) {
        $error = 'Ungültige User-ID.';
    } elseif ($userId === getCurrentUserId()) {
        $error = 'Du kannst deine eigene Rolle nicht ändern.';
    } else {
        if (updateUserRole($userId, $newRole)) {
            $success = 'Rolle erfolgreich geändert.';
        } else {
            $error = 'Fehler beim Ändern der Rolle.';
        }
    }
}

// Handle delete
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken.';
    } elseif (!$userId) {
        $error = 'Ungültige User-ID.';
    } elseif ($userId === getCurrentUserId()) {
        $error = 'Du kannst dich selbst nicht löschen.';
    } else {
        if (deleteUser($userId)) {
            $success = 'User erfolgreich gelöscht.';
        } else {
            $error = 'Fehler beim Löschen des Users.';
        }
    }
}

$users = getAllUsers();
$pageTitle = 'User verwalten';
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
            <h2><span class="icon-emoji">👥</span> User verwalten</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" data-dismiss="5000"><?php echo e($success); ?></div>
            <?php endif; ?>
            
            <div class="content-card">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>E-Mail</th>
                                <th>Rolle</th>
                                <th>Status</th>
                                <th>Kommentare</th>
                                <th>Registriert</th>
                                <th>Aktionen</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><strong><?php echo e($user['username']); ?></strong></td>
                                    <td><?php echo e($user['email']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo e($user['role']); ?>">
                                            <?php echo e(ucfirst($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($user['email_verified']): ?>
                                            <span class="badge badge-verified">✓ Verifiziert</span>
                                        <?php else: ?>
                                            <span class="badge badge-unverified">✗ Nicht verifiziert</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($user['comment_count']); ?></td>
                                    <td><?php echo formatDate($user['created_at']); ?></td>
                                    <td class="actions">
                                        <?php if ($user['id'] !== getCurrentUserId()): ?>
                                            <form method="post" action="" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                <input type="hidden" name="action" value="change_role">
                                                <input type="hidden" name="user_id" value="<?php echo e($user['id']); ?>">
                                                <select name="role" class="form-select" style="width: auto; display: inline; padding: 4px 8px;" onchange="this.form.submit()">
                                                    <option value="member" <?php echo $user['role'] === 'member' ? 'selected' : ''; ?>>Member</option>
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </form>
                                            
                                            <form method="post" action="" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="user_id" value="<?php echo e($user['id']); ?>">
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    data-confirm="Möchtest du diesen User wirklich löschen? Alle seine Kommentare werden ebenfalls gelöscht."
                                                >
                                                    🗑️
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Du selbst</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
    <?php include __DIR__ . '/../../includes/cookie-banner.php'; ?>

    <!-- Scripts -->
    <script src="/assets/js/header.js"></script>
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/cookie-consent.js"></script>
</body>
</html>
