<?php

/**
 * Database configuration
 *
 * Supports environment variables:
 * - DB_HOST: Database host (default: localhost)
 * - DB_PORT: Database port (optional, auto-detected based on driver)
 * - DB_NAME: Database name (default: babixgo)
 * - DB_USER: Database username (default: root)
 * - DB_PASSWORT or DB_PASSWORD: Database password (default: empty)
 * - DB_DRIVER: Database driver (optional: mysql or pgsql, auto-detected)
 *
 * Also supports Supabase-specific variables (for backward compatibility):
 * - SUPABASE_DB_HOST, SUPABASE_DB_PORT, SUPABASE_DB_NAME, etc.
 *
 * Priority order:
 * 1. Standard variables (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD/DB_PASSWORT, DB_PORT)
 * 2. Supabase variables (SUPABASE_DB_*)
 * 3. Default values
 */

// Load .env file if it exists (for local development)
$envFile = __DIR__ . '/../../../.env';
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
        
        // Only allow whitelisted database configuration variables
        $allowedKeys = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASSWORT', 'DB_PASSWORD', 'DB_DRIVER'];
        if (!in_array($key, $allowedKeys)) {
            continue;
        }
        
        // Remove quotes if present
        $value = trim($value, '"\'');
        
        // Only set if not already in environment
        if (!getenv($key)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Determine database driver
// Auto-detect PostgreSQL based on host (Supabase) or explicit DB_DRIVER
$host = getenv('DB_HOST') ?: 'localhost';
$driver = getenv('DB_DRIVER') ?: null;

// Auto-detect PostgreSQL if host contains supabase
if (!$driver && (strpos($host, 'supabase.com') !== false || strpos($host, 'supabase.co') !== false)) {
    $driver = 'pgsql';
}

// Default to MySQL if not detected
if (!$driver) {
    $driver = 'mysql';
}

// Set port based on driver if not explicitly set
$port = getenv('DB_PORT');
if (!$port) {
    $port = ($driver === 'pgsql') ? '5432' : '3306';
}

return [
    'driver' => $driver,
    'host' => $host,
    'port' => $port,
    'database' => getenv('DB_NAME') ?: 'babixgo',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASSWORT') ?: (getenv('DB_PASSWORD') ?: ''),
    'charset' => ($driver === 'pgsql') ? 'utf8' : 'utf8mb4'
];
