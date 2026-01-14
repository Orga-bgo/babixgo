<?php
/**
 * BabixGO Files - Logout Handler
 */

require_once __DIR__ . '/init.php';

logoutUser();

header('Location: /login.php');
exit;
