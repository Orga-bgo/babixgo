<?php
/**
 * Email sending functionality using PHPMailer with Brevo SMTP
 */

require_once __DIR__ . '/config.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if composer autoload exists
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback: try to use PHPMailer manually if composer is not available
    if (file_exists(__DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
        require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
    }
}

/**
 * Send an email using PHPMailer with Brevo SMTP
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @return bool
 */
function sendEmail(string $to, string $subject, string $body): bool {
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        if (DEBUG_MODE) {
            error_log('PHPMailer not found, falling back to basic SMTP');
        }
        return sendSmtpEmailFallback($to, $subject, $body);
    }
    
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        
        // Reduce verbosity
        $mail->SMTPDebug  = DEBUG_MODE ? 2 : 0;
        $mail->Debugoutput = function($str, $level) {
            if (DEBUG_MODE) {
                error_log("PHPMailer: $str");
            }
        };
        
        // Recipients
        $mail->setFrom('noreply@babixgo.de', SITE_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo('noreply@babixgo.de', SITE_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log("PHPMailer Error: {$mail->ErrorInfo}");
        }
        // Try fallback method
        return sendSmtpEmailFallback($to, $subject, $body);
    }
}

/**
 * Fallback: Send email via SMTP socket connection (original method)
 * @param string $to
 * @param string $subject
 * @param string $body
 * @return bool
 */
function sendSmtpEmailFallback(string $to, string $subject, string $body): bool {
    try {
        $socket = fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);
        
        if (!$socket) {
            if (DEBUG_MODE) {
                error_log("SMTP connection failed: $errstr ($errno)");
            }
            throw new Exception("Could not connect to SMTP server: $errstr ($errno)");
        }
        
        // Set stream timeout
        stream_set_timeout($socket, 30);
        
        // Read initial response
        $response = fgets($socket, 512);
        
        // Say hello
        fwrite($socket, "EHLO " . SITE_URL . "\r\n");
        $response = readSmtpResponse($socket);
        
        // Start TLS
        fwrite($socket, "STARTTLS\r\n");
        $response = fgets($socket, 512);
        
        // Enable crypto
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        
        // Say hello again after TLS
        fwrite($socket, "EHLO " . SITE_URL . "\r\n");
        $response = readSmtpResponse($socket);
        
        // Authenticate
        fwrite($socket, "AUTH LOGIN\r\n");
        $response = fgets($socket, 512);
        
        fwrite($socket, base64_encode(SMTP_USER) . "\r\n");
        $response = fgets($socket, 512);
        
        fwrite($socket, base64_encode(SMTP_PASS) . "\r\n");
        $response = fgets($socket, 512);
        
        if (strpos($response, '235') === false && strpos($response, '250') === false) {
            fclose($socket);
            throw new Exception("SMTP authentication failed: $response");
        }
        
        // Send mail from
        fwrite($socket, "MAIL FROM:<noreply@babixgo.de>\r\n");
        $response = fgets($socket, 512);
        
        // Send recipient
        fwrite($socket, "RCPT TO:<$to>\r\n");
        $response = fgets($socket, 512);
        
        // Send data command
        fwrite($socket, "DATA\r\n");
        $response = fgets($socket, 512);
        
        // Build email content
        $email = "From: " . SITE_NAME . " <noreply@babixgo.de>\r\n";
        $email .= "To: $to\r\n";
        $email .= "Subject: $subject\r\n";
        $email .= "MIME-Version: 1.0\r\n";
        $email .= "Content-Type: text/html; charset=UTF-8\r\n";
        $email .= "\r\n";
        $email .= $body;
        $email .= "\r\n.\r\n";
        
        fwrite($socket, $email);
        $response = fgets($socket, 512);
        
        // Quit
        fwrite($socket, "QUIT\r\n");
        fclose($socket);
        
        return strpos($response, '250') !== false;
        
    } catch (Exception $e) {
        if (DEBUG_MODE) {
            error_log('SMTP Fallback Error: ' . $e->getMessage());
        }
        // Last resort: try PHP mail()
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SITE_NAME . ' <noreply@babixgo.de>',
            'Reply-To: noreply@babixgo.de',
            'X-Mailer: PHP/' . phpversion()
        ];
        return @mail($to, $subject, $body, implode("\r\n", $headers));
    }
}

/**
 * Read multi-line SMTP response
 * @param resource $socket
 * @return string
 */
function readSmtpResponse($socket): string {
    $response = '';
    while ($line = fgets($socket, 512)) {
        $response .= $line;
        if (substr($line, 3, 1) === ' ') {
            break;
        }
    }
    return $response;
}

/**
 * Send verification email
 * @param string $to
 * @param string $username
 * @param string $token
 * @return bool
 */
function sendVerificationEmail(string $to, string $username, string $token): bool {
    $verifyUrl = SITE_URL . '/verify.php?token=' . urlencode($token);
    
    $subject = 'Bestätige deine E-Mail-Adresse - ' . SITE_NAME;
    
    $body = getEmailTemplate('verification', [
        'username' => htmlspecialchars($username),
        'verify_url' => $verifyUrl,
        'site_name' => SITE_NAME,
        'site_url' => SITE_URL
    ]);
    
    return sendEmail($to, $subject, $body);
}

/**
 * Get email template
 * @param string $template
 * @param array $variables
 * @return string
 */
function getEmailTemplate(string $template, array $variables): string {
    $templates = [
        'verification' => '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Mail bestätigen</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background-color: #141418; 
            color: #ffffff; 
            margin: 0; 
            padding: 20px; 
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #2a2e32; 
            border-radius: 12px; 
            padding: 30px; 
        }
        h1 { 
            color: #A0D8FA; 
            margin-top: 0; 
        }
        .btn { 
            display: inline-block; 
            background: #A0D8FA; 
            color: #00293c; 
            padding: 12px 24px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            margin: 20px 0; 
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #948b99; 
            color: #bec8d2; 
            font-size: 14px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Willkommen bei {{site_name}}!</h1>
        <p>Hallo {{username}},</p>
        <p>vielen Dank für deine Registrierung. Bitte bestätige deine E-Mail-Adresse, um dein Konto zu aktivieren.</p>
        <a href="{{verify_url}}" class="btn">E-Mail bestätigen</a>
        <p>Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:</p>
        <p style="word-break: break-all; color: #A0D8FA;">{{verify_url}}</p>
        <div class="footer">
            <p>Diese E-Mail wurde automatisch versendet. Bitte antworte nicht auf diese Nachricht.</p>
            <p>&copy; {{site_name}} - <a href="{{site_url}}" style="color: #A0D8FA;">{{site_url}}</a></p>
        </div>
    </div>
</body>
</html>'
    ];
    
    $html = $templates[$template] ?? '';
    
    foreach ($variables as $key => $value) {
        $html = str_replace('{{' . $key . '}}', $value, $html);
    }
    
    return $html;
}
