<?php

/**
 * Database configuration
 * Uses environment variables (from GitHub Secrets) when available,
 * falls back to defaults for local development
 */
return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'babixgo',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') ?: '',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4'
];
