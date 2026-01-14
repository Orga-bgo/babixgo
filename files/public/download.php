<?php
/**
 * BabixGO Files - Download Handler
 * Increments download count and redirects to actual download
 */

require_once __DIR__ . '/init.php';

initSession();

// Require login to download
if (!isLoggedIn()) {
    header('Location: /login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$downloadId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$downloadId) {
    header('Location: /?error=' . urlencode('Ungültige Download-ID.'));
    exit;
}

$download = getDownloadById($downloadId);

if (!$download) {
    header('Location: /?error=' . urlencode('Download nicht gefunden.'));
    exit;
}

if (empty($download['download_link'])) {
    header('Location: /?error=' . urlencode('Kein Download-Link verfügbar.'));
    exit;
}

// Increment download counter
incrementDownloadCount($downloadId);

// Redirect to actual download
header('Location: ' . $download['download_link']);
exit;
