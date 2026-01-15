<?php
/**
 * Mail Helper - Uses PHPMailer with Brevo SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer autoload
$composerAutoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// SMTP Configuration
define('SMTP_HOST', 'smtp-relay.brevo.com');
define('SMTP_PORT', 587);
define('SMTP_USER', $_ENV['SMTP_USER'] ?? getenv('SMTP_USER') ?? '');
define('SMTP_PASS', $_ENV['SMTP_KEY'] ?? getenv('SMTP_KEY') ?? '');
define('MAIL_FROM', 'register@babixgo-mail.de');
define('SITE_NAME', 'BabixGO');

/**
 * Send email using PHPMailer with Brevo SMTP
 */
function sendEmail($to, $subject, $message, $headers = []) {
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log('PHPMailer not available');
        return false;
    }
    
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';
        
        $mail->setFrom(MAIL_FROM, SITE_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(MAIL_FROM, SITE_NAME);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send verification email
 */
function sendVerificationEmail($email, $username, $token) {
    $verifyUrl = "https://babixgo.de/auth/verify-email?token=" . urlencode($token);
    
    $subject = "E-Mail-Adresse bestätigen - BabixGO";
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background-color: #141418; color: #ffffff; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background-color: #2a2e32; border-radius: 12px; padding: 30px; }
            h1 { color: #A0D8FA; margin-top: 0; }
            .btn { display: inline-block; background: #A0D8FA; color: #00293c; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #948b99; color: #bec8d2; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Willkommen bei BabixGO!</h1>
            <p>Hallo ' . htmlspecialchars($username) . ',</p>
            <p>vielen Dank für deine Registrierung. Bitte bestätige deine E-Mail-Adresse, um dein Konto zu aktivieren.</p>
            <p style="text-align: center;">
                <a href="' . $verifyUrl . '" class="btn">E-Mail bestätigen</a>
            </p>
            <p>Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:</p>
            <p style="word-break: break-all; color: #A0D8FA;">' . $verifyUrl . '</p>
            <p>Der Link ist 24 Stunden gültig.</p>
            <div class="footer">
                <p>Diese E-Mail wurde automatisch versendet. Bitte antworte nicht auf diese Nachricht.</p>
                <p>&copy; ' . date('Y') . ' BabixGO</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $username, $token) {
    $resetUrl = "https://babixgo.de/auth/reset-password?token=" . urlencode($token);
    
    $subject = "Passwort zurücksetzen - BabixGO";
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background-color: #141418; color: #ffffff; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background-color: #2a2e32; border-radius: 12px; padding: 30px; }
            h1 { color: #A0D8FA; margin-top: 0; }
            .btn { display: inline-block; background: #e74c3c; color: #ffffff; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #948b99; color: #bec8d2; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Passwort zurücksetzen</h1>
            <p>Hallo ' . htmlspecialchars($username) . ',</p>
            <p>Du hast angefordert, dein Passwort zurückzusetzen. Klicke auf den Button unten, um ein neues Passwort zu erstellen:</p>
            <p style="text-align: center;">
                <a href="' . $resetUrl . '" class="btn">Passwort zurücksetzen</a>
            </p>
            <p>Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser:</p>
            <p style="word-break: break-all; color: #A0D8FA;">' . $resetUrl . '</p>
            <p>Der Link ist 1 Stunde gültig.</p>
            <p>Falls du kein neues Passwort angefordert hast, ignoriere diese E-Mail.</p>
            <div class="footer">
                <p>Diese E-Mail wurde automatisch versendet. Bitte antworte nicht auf diese Nachricht.</p>
                <p>&copy; ' . date('Y') . ' BabixGO</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send welcome email (after verification)
 */
function sendWelcomeEmail($email, $username) {
    $subject = "Willkommen bei BabixGO!";
    
    $message = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background-color: #141418; color: #ffffff; margin: 0; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background-color: #2a2e32; border-radius: 12px; padding: 30px; }
            h1 { color: #A0D8FA; margin-top: 0; }
            .btn { display: inline-block; background: #A0D8FA; color: #00293c; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 20px 0; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #948b99; color: #bec8d2; font-size: 14px; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Willkommen bei BabixGO!</h1>
            <p>Hallo ' . htmlspecialchars($username) . ',</p>
            <p>Deine E-Mail-Adresse wurde erfolgreich bestätigt! Du kannst dich jetzt anmelden und alle Funktionen nutzen.</p>
            <p style="text-align: center;">
                <a href="https://babixgo.de/auth/login" class="btn">Jetzt anmelden</a>
            </p>
            <p>Vielen Dank, dass du Teil unserer Community bist!</p>
            <div class="footer">
                <p>Diese E-Mail wurde automatisch versendet. Bitte antworte nicht auf diese Nachricht.</p>
                <p>&copy; ' . date('Y') . ' BabixGO</p>
            </div>
        </div>
    </body>
    </html>
    ';
    
    return sendEmail($email, $subject, $message);
}
