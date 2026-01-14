<?php

/**
 * Database configuration
 * 
 * Supports environment variables:
 * - DB_HOST: Database host (default: localhost)
 * - DB_NAME: Database name (default: babixgo)
 * - DB_USER: Database username (default: root)
 * - DB_PASSWORT or DB_PASSWORD: Database password (default: empty)
 * 
 * Priority order:
 * 1. Environment variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORT/DB_PASSWORD)
 * 2. Default values (for backward compatibility)
 */

// Load .env file if it exists (for local development)
$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Skip comments and invalid lines
        if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
            continue;
        }
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Remove quotes if present
        $value = trim($value, '"\'');
        
        // Only set if not already in environment
        if (!getenv($key)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'babixgo',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORT') ?: (getenv('DB_PASSWORD') ?: ''),
    'charset' => 'utf8mb4'
];
