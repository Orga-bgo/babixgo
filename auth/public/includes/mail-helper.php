<?php
/**
 * Mail Helper
 * Functions for sending emails
 */

/**
 * Send email using PHP mail() function
 */
function sendEmail($to, $subject, $message, $headers = []) {
    $defaultHeaders = [
        'From' => 'noreply@babixgo.de',
        'Reply-To' => 'support@babixgo.de',
        'X-Mailer' => 'PHP/' . phpversion(),
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8'
    ];
    
    $headers = array_merge($defaultHeaders, $headers);
    
    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= "$key: $value\r\n";
    }
    
    return mail($to, $subject, $message, $headerString);
}

/**
 * Send verification email
 */
function sendVerificationEmail($email, $username, $token) {
    $verifyUrl = "https://babixgo.de/auth/verify-email?token=" . urlencode($token);
    
    $subject = "Verify your babixgo.de account";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f4f4f4; }
            .button { 
                display: inline-block; 
                padding: 12px 24px; 
                background: #3498db; 
                color: white; 
                text-decoration: none; 
                border-radius: 4px;
                margin: 20px 0;
            }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to babixgo.de!</h1>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($username) . ",</p>
                <p>Thank you for registering at babixgo.de. Please verify your email address by clicking the button below:</p>
                <p style='text-align: center;'>
                    <a href='$verifyUrl' class='button'>Verify Email Address</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all;'>$verifyUrl</p>
                <p>This link will expire in 24 hours.</p>
                <p>If you didn't create an account, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " babixgo.de. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send password reset email
 */
function sendPasswordResetEmail($email, $username, $token) {
    $resetUrl = "https://babixgo.de/auth/reset-password?token=" . urlencode($token);
    
    $subject = "Reset your babixgo.de password";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f4f4f4; }
            .button { 
                display: inline-block; 
                padding: 12px 24px; 
                background: #e74c3c; 
                color: white; 
                text-decoration: none; 
                border-radius: 4px;
                margin: 20px 0;
            }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Password Reset Request</h1>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($username) . ",</p>
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                <p style='text-align: center;'>
                    <a href='$resetUrl' class='button'>Reset Password</a>
                </p>
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all;'>$resetUrl</p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request a password reset, please ignore this email. Your password will remain unchanged.</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " babixgo.de. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send welcome email (after verification)
 */
function sendWelcomeEmail($email, $username) {
    $subject = "Welcome to babixgo.de!";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f4f4f4; }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Welcome to babixgo.de!</h1>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($username) . ",</p>
                <p>Your email has been verified successfully! You can now log in and access all features.</p>
                <p>Visit <a href='https://babixgo.de/auth/login'>babixgo.de/auth</a> to get started.</p>
                <p>Thank you for joining our community!</p>
            </div>
            <div class='footer'>
                <p>&copy; " . date('Y') . " babixgo.de. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}
