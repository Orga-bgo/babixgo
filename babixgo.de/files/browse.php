<?php
/**
 * Browse files by type
 */

// Define paths
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__) . '/');
}
if (!defined('SHARED_PATH')) {
    define('SHARED_PATH', BASE_PATH . 'shared/');
}

// Load shared configuration
require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

$pageTitle = 'Dateien durchsuchen - babixgo';

$type = $_GET['type'] ?? 'all';

$download = new Download();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></title>
    <link rel="stylesheet" href="/shared/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php require_once SHARED_PATH . 'partials/header.php'; ?>

    <main class="container">
        <h1>Dateien durchsuchen</h1>
        <p>Kategorie: <?php echo htmlspecialchars($type, ENT_QUOTES); ?></p>

        <div class="file-list">
            <p>Hier werden die Dateien angezeigt...</p>
            <!-- File listing would be implemented here -->
        </div>

        <p><a href="/files/">Zurück zur Übersicht</a></p>
    </main>

    <?php require_once SHARED_PATH . 'partials/footer.php'; ?>
</body>
</html>
