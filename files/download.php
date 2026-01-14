<?php
/**
 * Download handler
 */
require_once(__DIR__ . '/../shared/classes/Database.php');
require_once(__DIR__ . '/../shared/classes/Download.php');
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();

if (!isset($_GET['id'])) {
    header('Location: /index.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();
$download = new Download($db);

$file = $download->getById($_GET['id']);

if (!$file) {
    die('Datei nicht gefunden.');
}

// Increment download counter
$download->incrementDownloadCount($_GET['id']);

// Serve the file
if (!$download->serveFile($file['filepath'])) {
    die('Fehler beim Herunterladen der Datei.');
}
