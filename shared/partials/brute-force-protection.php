<?php
/**
 * Brute-Force Protection Utilities
 * babixGO - partials/brute-force-protection.php
 */

/**
 * Check if a login attempt should be allowed
 * 
 * @param string $identifier The identifier (IP address or username)
 * @param int $maxAttempts Maximum failed attempts before lockout
 * @param int $lockoutTime Lockout duration in seconds
 * @return array ['allowed' => bool, 'message' => string, 'wait_time' => int]
 */
function login_check_rate_limit($identifier, $maxAttempts = 5, $lockoutTime = 900) {
    $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $identifier);
    $file = sys_get_temp_dir() . '/login_attempts_' . hash('sha256', $safeIdentifier) . '.json';
    $now = time();
    
    $data = [
        'attempts' => 0,
        'first_attempt' => $now,
        'locked_until' => 0
    ];
    
    // Try to read existing data
    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        return ['allowed' => true, 'message' => '', 'wait_time' => 0];
    }
    
    try {
        if (flock($handle, LOCK_EX)) {
            $content = stream_get_contents($handle);
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $data = array_merge($data, $decoded);
                }
            }
            
            // Check if currently locked out
            if ($data['locked_until'] > $now) {
                $waitTime = $data['locked_until'] - $now;
                $minutes = ceil($waitTime / 60);
                return [
                    'allowed' => false,
                    'message' => "Zu viele fehlgeschlagene Anmeldeversuche. Bitte versuche es in {$minutes} Minute(n) erneut.",
                    'wait_time' => $waitTime
                ];
            }
            
            // Reset if lockout period has passed
            if ($data['locked_until'] > 0 && $data['locked_until'] <= $now) {
                $data = [
                    'attempts' => 0,
                    'first_attempt' => $now,
                    'locked_until' => 0
                ];
            }
            
            // Check if we should reset the window (15 minutes)
            if ($now - $data['first_attempt'] > 900) {
                $data = [
                    'attempts' => 0,
                    'first_attempt' => $now,
                    'locked_until' => 0
                ];
            }
            
            return ['allowed' => true, 'message' => '', 'wait_time' => 0, 'data' => $data];
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
    
    return ['allowed' => true, 'message' => '', 'wait_time' => 0];
}

/**
 * Record a failed login attempt
 * 
 * @param string $identifier The identifier (IP address or username)
 * @param int $maxAttempts Maximum failed attempts before lockout
 * @param int $lockoutTime Lockout duration in seconds
 * @return array ['locked' => bool, 'attempts' => int, 'remaining' => int]
 */
function login_record_failed_attempt($identifier, $maxAttempts = 5, $lockoutTime = 900) {
    $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $identifier);
    $file = sys_get_temp_dir() . '/login_attempts_' . hash('sha256', $safeIdentifier) . '.json';
    $now = time();
    
    $data = [
        'attempts' => 1,
        'first_attempt' => $now,
        'locked_until' => 0
    ];
    
    $handle = @fopen($file, 'c+');
    if ($handle === false) {
        return ['locked' => false, 'attempts' => 1, 'remaining' => $maxAttempts - 1];
    }
    
    try {
        if (flock($handle, LOCK_EX)) {
            $content = stream_get_contents($handle);
            if ($content) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
            
            // Increment attempts
            $data['attempts'] = (int)($data['attempts'] ?? 0) + 1;
            
            // Check if we should lock
            $locked = false;
            if ($data['attempts'] >= $maxAttempts) {
                $data['locked_until'] = $now + $lockoutTime;
                $locked = true;
            }
            
            // Write back to file
            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, json_encode($data));
            
            $remaining = max(0, $maxAttempts - $data['attempts']);
            
            return [
                'locked' => $locked,
                'attempts' => $data['attempts'],
                'remaining' => $remaining,
                'locked_until' => $data['locked_until']
            ];
        }
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
    
    return ['locked' => false, 'attempts' => 1, 'remaining' => $maxAttempts - 1];
}

/**
 * Clear login attempts for an identifier (after successful login)
 * 
 * @param string $identifier The identifier (IP address or username)
 */
function login_clear_attempts($identifier) {
    $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $identifier);
    $file = sys_get_temp_dir() . '/login_attempts_' . hash('sha256', $safeIdentifier) . '.json';
    
    @unlink($file);
}
?>
