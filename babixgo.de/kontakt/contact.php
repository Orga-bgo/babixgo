<?php
/**
 * Kontaktformular Backend
 * babixGO - contact.php
 * DSGVO-konform
 */

// Error Reporting (in Production auf 0 setzen)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Headers
header('Content-Type: application/json');
$allowedOrigin = 'https://babixgo.de';
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin === $allowedOrigin) {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Vary: Origin');
}

// Preflight Request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code($origin === $allowedOrigin ? 200 : 403);
    exit();
}

function respondWithStatus(bool $expectsJson, string $statusKey, string $message, int $httpCode = 400): void
{
    if ($expectsJson) {
        http_response_code($httpCode);
        echo json_encode([
            'success' => $statusKey === 'success',
            'message' => $message
        ]);
        exit();
    }

    $location = '/kontakt/?status=' . urlencode($statusKey);
    header('Location: ' . $location);
    exit();
}

function sanitizeHeaderValue(string $value): string
{
    return preg_replace('/[\r\n]+/', '', $value);
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
$expectsJson = stripos($contentType, 'application/json') !== false
    || stripos($accept, 'application/json') !== false;

// Nur POST erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondWithStatus($expectsJson, 'error', 'Method not allowed', 405);
}

$dbHost = getenv('BABIXGO_DB_HOST');
$dbName = getenv('BABIXGO_DB_NAME');
$dbUser = getenv('BABIXGO_DB_USER');
$dbPass = getenv('BABIXGO_DB_PASS');

if (!$dbHost || !$dbName || !$dbUser || !$dbPass) {
    respondWithStatus(
        $expectsJson,
        'config',
        'Serverkonfiguration fehlt. Bitte versuche es später erneut.',
        500
    );
}

function isRateLimited(string $ip, int $limit = 5, int $windowSeconds = 600): bool
{
    $safeIp = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $ip);
    $file = sys_get_temp_dir() . '/contact_rate_' . hash('sha256', $safeIp) . '.json';
    $now = time();
    $data = ['start' => $now, 'count' => 0];

    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        return false;
    }

    try {
        if (flock($handle, LOCK_EX)) {
            $content = stream_get_contents($handle);
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded) && isset($decoded['start'], $decoded['count'])) {
                    $data = $decoded;
                }
            }

            if ($now - (int) $data['start'] > $windowSeconds) {
                $data = ['start' => $now, 'count' => 0];
            }

            if ((int) $data['count'] >= $limit) {
                return true;
            }

            $data['count'] = (int) $data['count'] + 1;
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($data));
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    return false;
}

try {
    $data = [];

    if (stripos($contentType, 'application/json') !== false) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
    } else {
        $data = $_POST;
    }

    if (!is_array($data) || empty($data)) {
        throw new Exception('Ungültige Anfrage.');
    }

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    if (isRateLimited($ipAddress)) {
        respondWithStatus($expectsJson, 'rate', 'Zu viele Anfragen. Bitte versuche es später erneut.', 429);
    }

    if (!empty($data['website'])) {
        throw new Exception('Ungültige Anfrage.');
    }

    $formTime = isset($data['form_time']) ? (int) $data['form_time'] : 0;
    if ($formTime > 0) {
        $delta = time() - $formTime;
        if ($delta < 3 || $delta > 86400) {
            throw new Exception('Ungültige Anfrage.');
        }
    }

    // Validierung
    if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
        respondWithStatus($expectsJson, 'validation', 'Bitte fülle alle Pflichtfelder aus.', 400);
    }

    // DSGVO: Datenschutz-Checkbox prüfen
    $privacyAccepted = false;
    if (is_bool($data['privacy'] ?? null)) {
        $privacyAccepted = $data['privacy'] === true;
    } elseif (is_string($data['privacy'] ?? null)) {
        $privacyAccepted = in_array($data['privacy'], ['1', 'on', 'true'], true);
    } elseif (is_int($data['privacy'] ?? null)) {
        $privacyAccepted = $data['privacy'] === 1;
    }

    if (!$privacyAccepted) {
        respondWithStatus($expectsJson, 'validation', 'Bitte akzeptiere die Datenschutzerklärung.', 400);
    }

    // Input bereinigen
    $name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $whatsapp = !empty($data['whatsapp']) ? htmlspecialchars(trim($data['whatsapp']), ENT_QUOTES, 'UTF-8') : null;
    $message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');

    // E-Mail validieren
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respondWithStatus($expectsJson, 'validation', 'Ungültige E-Mail-Adresse.', 400);
    }

    // Nachricht validieren
    if (strlen($message) < 10) {
        respondWithStatus($expectsJson, 'validation', 'Nachricht ist zu kurz (mindestens 10 Zeichen).', 400);
    }

    if (strlen($message) > 5000) {
        respondWithStatus($expectsJson, 'validation', 'Nachricht ist zu lang (maximal 5000 Zeichen).', 400);
    }

    // Datenbankverbindung
    $pdo = new PDO(
        "mysql:host=" . $dbHost . ";dbname=" . $dbName . ";charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // Alte Kontakte automatisch löschen (DSGVO: 6 Monate nach Archivierung)
    $pdo->exec("
        DELETE FROM contacts 
        WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH) 
        AND status = 'archived'
    ");

    // In Datenbank einfügen
    $stmt = $pdo->prepare("
        INSERT INTO contacts (name, email, whatsapp, message, ip_address, user_agent, created_at) 
        VALUES (:name, :email, :whatsapp, :message, :ip, :user_agent, NOW())
    ");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':whatsapp' => $whatsapp,
        ':message' => $message,
        ':ip' => $ipAddress,
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
    ]);

    // Optional: E-Mail-Benachrichtigung
    $to = 'info@babixgo.de';
    $safeName = sanitizeHeaderValue($name);
    $safeEmail = sanitizeHeaderValue($email);
    $subject = 'Neue Kontaktanfrage von ' . $safeName;
    $email_message = "Neue Kontaktanfrage:\n\n";
    $email_message .= "Name: $name\n";
    $email_message .= "E-Mail: $email\n";
    $email_message .= "WhatsApp: " . ($whatsapp ?: 'Nicht angegeben') . "\n\n";
    $email_message .= "Nachricht:\n$message\n";
    
    $replyTo = $safeEmail;
    $headers = "From: noreply@babixgo.de\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    
    @mail($to, $subject, $email_message, $headers);

    // Erfolg
    respondWithStatus($expectsJson, 'success', 'Nachricht erfolgreich gesendet!', 200);

} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    respondWithStatus($expectsJson, 'error', 'Datenbankfehler. Bitte versuche es später erneut.', 500);

} catch (Exception $e) {
    respondWithStatus($expectsJson, 'error', $e->getMessage(), 400);
}
?>
