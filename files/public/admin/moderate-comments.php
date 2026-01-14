<?php
/**
 * BabixGO Files - Moderate Comments
 */

require_once __DIR__ . '/../init.php';

initSession();
requireAdmin();

$error = '';
$success = '';

// Handle delete action
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    
    if (!validateCsrfToken($csrfToken)) {
        $error = 'Ungültiger Sicherheitstoken.';
    } elseif (!$commentId) {
        $error = 'Ungültige Kommentar-ID.';
    } else {
        if (deleteComment($commentId)) {
            $success = 'Kommentar erfolgreich gelöscht.';
        } else {
            $error = 'Fehler beim Löschen des Kommentars.';
        }
    }
}

$comments = getAllComments();
$pageTitle = 'Kommentare moderieren';
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
            <h2><span class="icon-emoji">💬</span> Kommentare moderieren</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" data-dismiss="5000"><?php echo e($success); ?></div>
            <?php endif; ?>
            
            <?php if (empty($comments)): ?>
                <div class="empty-state content-card">
                    <div class="empty-state-icon">💭</div>
                    <h3>Keine Kommentare vorhanden</h3>
                    <p>Es gibt noch keine Kommentare zu moderieren.</p>
                </div>
            <?php else: ?>
                <div class="content-card">
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Autor</th>
                                    <th>Download</th>
                                    <th>Kommentar</th>
                                    <th>Datum</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td><strong><?php echo e($comment['username']); ?></strong></td>
                                        <td><?php echo e($comment['download_name']); ?></td>
                                        <td>
                                            <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo nl2br(e($comment['comment_text'])); ?>
                                            </div>
                                        </td>
                                        <td><?php echo formatRelativeTime($comment['created_at']); ?></td>
                                        <td class="actions">
                                            <form method="post" action="" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="comment_id" value="<?php echo e($comment['id']); ?>">
                                                <button 
                                                    type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    data-confirm="Möchtest du diesen Kommentar wirklich löschen?"
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
