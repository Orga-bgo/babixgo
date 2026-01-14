<?php
/**
 * Auth subdomain index page
 */
require_once(__DIR__ . '/../shared/classes/Session.php');

$session = new Session();

if ($session->isLoggedIn()) {
    header('Location: https://babixgo.de');
    exit;
}

header('Location: /login.php');
exit;
