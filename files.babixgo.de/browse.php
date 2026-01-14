<?php
/**
 * Browse files by type
 */
$pageTitle = 'Dateien durchsuchen - babixgo';
require_once(__DIR__ . '/../shared/partials/header.php');
require_once(__DIR__ . '/../shared/classes/Database.php');
require_once(__DIR__ . '/../shared/classes/Download.php');

$type = $_GET['type'] ?? 'all';

$database = new Database();
$db = $database->getConnection();
$download = new Download($db);
?>

<h1>Dateien durchsuchen</h1>
<p>Kategorie: <?php echo htmlspecialchars($type); ?></p>

<div class="file-list">
    <p>Hier werden die Dateien angezeigt...</p>
    <!-- File listing would be implemented here -->
</div>

<p><a href="/index.php">Zurück zur Übersicht</a></p>

<?php require_once(__DIR__ . '/../shared/partials/footer.php'); ?>
