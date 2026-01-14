<?php
/**
 * BabixGO Files - Comment API Endpoint
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../init.php';

initSession();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Require authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Bitte melde dich an.']);
    exit;
}

// Validate CSRF token
$csrfToken = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Ungültiger Sicherheitstoken.']);
    exit;
}

// Get input
$downloadId = filter_input(INPUT_POST, 'download_id', FILTER_VALIDATE_INT);
$commentText = trim($_POST['comment'] ?? '');

// Validate input
if (!$downloadId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige Download-ID.']);
    exit;
}

if (empty($commentText)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kommentar darf nicht leer sein.']);
    exit;
}

if (strlen($commentText) > 2000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Kommentar ist zu lang (max. 2000 Zeichen).']);
    exit;
}

// Check if download exists
$download = getDownloadById($downloadId);
if (!$download) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Download nicht gefunden.']);
    exit;
}

// Add comment
$userId = getCurrentUserId();
$commentId = addComment($downloadId, $userId, $commentText);

if (!$commentId) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Fehler beim Speichern des Kommentars.']);
    exit;
}

// Return success with comment data
echo json_encode([
    'success' => true,
    'message' => 'Kommentar erfolgreich hinzugefügt.',
    'comment' => [
        'id' => $commentId,
        'username' => $_SESSION['username'],
        'comment_text' => $commentText,
        'created_at' => date('Y-m-d H:i:s')
    ]
]);
