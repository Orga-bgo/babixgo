<?php

/**
 * Session management class
 */
class Session {
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            $config = require_once(__DIR__ . '/../config/session.php');
            
            session_set_cookie_params([
                'lifetime' => $config['lifetime'],
                'path' => $config['path'],
                'domain' => $config['domain'],
                'secure' => $config['secure'],
                'httponly' => $config['httponly'],
                'samesite' => $config['samesite']
            ]);
            
            session_start();
        }
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function delete($key) {
        unset($_SESSION[$key]);
    }

    public function destroy() {
        session_destroy();
    }

    public function isLoggedIn() {
        return $this->has('user_id');
    }
}
