<?php
/**
 * Download Handler
 * URL: babixgo.de/files/download?id=123&type=apk
 * OR: babixgo.de/files/download/123/apk (via .htaccess rewrite)
 */

define('BASE_PATH', dirname(__DIR__) . '/');
define('SHARED_PATH', BASE_PATH . 'shared/');
define('DOWNLOADS_PATH', BASE_PATH . 'babixgo.de/file-storage/');

require_once SHARED_PATH . 'config/database.php';
require_once SHARED_PATH . 'config/session.php';
require_once SHARED_PATH . 'config/autoload.php';

// Get database connection from singleton
$db = Database::getInstance()->getConnection();

// Validate parameters
$fileId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fileType = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);

if (!$fileId || !$fileType) {
    http_response_code(400);
    die('Ungültige Parameter');
}

// Optional: Require login for downloads
// Uncomment to require authentication
// if (!User::isLoggedIn()) {
//     header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
//     exit;
// }

// Get file information from database
try {
    $stmt = $db->prepare("SELECT * FROM downloads WHERE id = ? AND filetype = ? AND active = 1");
    $stmt->execute([$fileId, $fileType]);
    $fileInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$fileInfo) {
        http_response_code(404);
        die('Datei nicht gefunden');
    }
    
    // Construct file path
    $filePath = DOWNLOADS_PATH . $fileInfo['filepath'];
    
    if (!file_exists($filePath)) {
        http_response_code(404);
        die('Datei existiert nicht auf dem Server');
    }
    
    // Log download
    $userId = $_SESSION['user_id'] ?? null;
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    
    $logStmt = $db->prepare("
        INSERT INTO download_logs (file_id, user_id, ip_address, user_agent) 
        VALUES (?, ?, ?, ?)
    ");
    $logStmt->execute([$fileId, $userId, $ipAddress, $userAgent]);
    
    // Increment download counter
    $updateStmt = $db->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?");
    $updateStmt->execute([$fileId]);
    
    // Determine MIME type
    $mimeTypes = [
        'apk' => 'application/vnd.android.package-archive',
        'exe' => 'application/x-msdownload',
        'scripts' => 'application/octet-stream',
        'sh' => 'application/x-sh',
        'ps1' => 'text/plain',
        'py' => 'text/x-python',
        'zip' => 'application/zip'
    ];
    
    $extension = strtolower(pathinfo($fileInfo['filename'], PATHINFO_EXTENSION));
    $mimeType = $mimeTypes[$fileType] ?? $mimeTypes[$extension] ?? 'application/octet-stream';
    
    // Set headers for download
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . basename($fileInfo['filename']) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    // Stream file in chunks (better for large files)
    $chunkSize = 1024 * 1024; // 1 MB
    $handle = fopen($filePath, 'rb');
    
    if ($handle === false) {
        die('Fehler beim Öffnen der Datei');
    }
    
    while (!feof($handle)) {
        echo fread($handle, $chunkSize);
        flush();
    }
    
    fclose($handle);
    exit;
    
} catch (PDOException $e) {
    error_log('Download error: ' . $e->getMessage());
    http_response_code(500);
    die('Interner Serverfehler');
}
