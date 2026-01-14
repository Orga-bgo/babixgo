<?php
/**
 * Admin Check
 * Include this file to require admin authentication
 */

// First check if user is logged in
require_once __DIR__ . '/auth-check.php';

// Check if user is admin
if (!User::isAdmin()) {
    header('Location: /index.php');
    exit;
}
