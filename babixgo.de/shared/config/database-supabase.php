<?php

/**
 * Supabase PostgreSQL Database configuration
 * 
 * Uses environment variables:
 * - SUPABASE_DB_HOST
 * - SUPABASE_DB_PORT
 * - SUPABASE_DB_NAME
 * - SUPABASE_DB_USER
 * - SUPABASE_DB_PASSWORD
 */

return [
    'driver' => 'pgsql',
    'host' => getenv('SUPABASE_DB_HOST') ?: 'localhost',
    'port' => getenv('SUPABASE_DB_PORT') ?: '5432',
    'database' => getenv('SUPABASE_DB_NAME') ?: 'postgres',
    'username' => getenv('SUPABASE_DB_USER') ?: 'postgres',
    'password' => getenv('SUPABASE_DB_PASSWORD') ?: '',
    'charset' => 'utf8'
];
